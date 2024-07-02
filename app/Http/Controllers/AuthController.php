<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\ThirdParty\Internals;
use App\Exceptions\UnauthorizedAccess;
use App\Exceptions\UnexpectedField;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $role= Role::where("name", "Anggota")->first();
        $role_id = $role->id;

        try {
                $expectedField = ['name', 'nim', 'email', 'profile_picture', 'password', 'division', 'password_confirmation'];

                $unexpectedFields = array_diff(array_keys($request->all()), $expectedField);
                if (!empty($unexpectedFields)) {
                    throw new UnexpectedField($unexpectedFields);
                }

                $data = $request->only($expectedField);


                if (!$request->hasFile('profile_picture') || !$request->file('profile_picture')->isValid()) {
                    return response()->json([
                        'error' => 'Foto profil harus berupa file'
                    ], 400);
                }



                $this->validate($request, [
                    'name' => 'required|string',
                    'nim' => 'required|unique:users|min:12|max:12',
                    'email' => 'required|email|unique:users',
                    'profile_picture' => 'required|image|mimes:jpeg,jpg,png|max:5120',
                    'password' => 'required|min:8|confirmed',
                    'division' => 'required'
                ],
                [
                    'name.required' => "Nama tidak boleh kosong",
                    'name.string' => "Nama harus string",
                    'nim.required' => "NIM tidak boleh kosong",
                    'nim.unique' => "NIM tersebut sudah dipakai",
                    'nim.min' => "NIM harus minimal 12 karakter",
                    'nim.max' => "NIM tidak boleh lebih dari 12 karakter",
                    'email.required' => "Email tidak boleh kosong",
                    'email.email' => "Email harus sesuai format email",
                    'email.unique' => "Email tersebut sudah dipakai",
                    'profile_picture.required' => "Foto Profil tidak boleh kosong",
                    'profile_picture.image' => "Foto profil harus berupa gambar",
                    'profile_picture.mimes' => "Foto profil harus dalam format jpeg, jpg dan png",
                    'profile_picture.max' => "Foto profil tidak boleh lebih dari 5mb",
                    'password.required' => "Password tidak boleh kosong",
                    'password.min' => "Password minimal 8 karakter",
                    'password.confirmed' => "Password tidak sama dengan password yang kamu ketik",
                    'division.required' => "Divisi tidak boleh kosong",
                ]
                );

                $profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');


                User::create([
                    'name' => $data['name'],
                    'nim' => $data['nim'],
                    'email' => $data['email'],
                    'profile_picture' => $profile_picture,
                    'role_id' => $role_id,
                    'division_id' => $this->getDivision($data['division'],),
                    'password' => Hash::make($data['password'],),
                ]);

                return $this->sendSuccessResponse(null, 'Registrasi berhasil');
            } catch (ValidationException $e) {
                return $this->sendBadRequestResponse($e->errors());
            } catch(UnexpectedField $e){
                return response()->json([
                    'error' => $e->getMessage()
                ], 400);
            } catch (Throwable $e) {
                Log::error($e->getMessage());
                return $this->sendInternalServerErrorResponse($e);
            }
    }

    public function login(Request $request)
    {
        try {
            $expectedField = ['email','password'];

            $unexpectedFields = array_diff(array_keys($request->all()), $expectedField);
            if (!empty($unexpectedFields)) {
                throw new UnexpectedField($unexpectedFields);
            }

            $data = $request->only($expectedField);

            // Validasi form login
            $validator = Validator::make($data, [
                'email' => 'required|email',
                'password' => 'required',
            ],
            [
                'email.required' => "Email tidak boleh kosong",
                'email.email' => "Email harus sesuai format email",
                'password.required' => "Password tidak boleh kosong",
            ]);

            // Response 422 jika validasi gagal
            if ($validator->fails()) {
                return $this->sendBadRequestResponse($validator->errors());
            }

            $token = $this->emailPasswordLogin($request->email, $request->password);
            return $this->sendSuccessResponse($token, "Login berhasil");
            } catch (UnauthorizedAccess $e) {
                return $this->sendUnauthorizedResponse("Email atau Password salah");
            } catch(UnexpectedField $e){
                return response()->json([
                    'error' => $e->getMessage()
                ], 400);
            } catch (Throwable $e) {
                Log::error($e->getMessage());
                return $this->sendInternalServerErrorResponse($e);
            }
    }

    private function emailPasswordLogin(string $email, string $password)
    {
        $user = User::where('email', $email)->first();

        if (
            empty($user) ||
            !$user->is_accepted == "accepted"||
            !Hash::check($password, $user->password)
        ) {
            throw new UnauthorizedAccess("Email atau Password salah");
        }

        return $this->generatePairToken($user);
    }

    private function ttl(int $minute): int
    {
        return time() + ($minute * 60);
    }

    private function generatePairToken($user)
    {
        /** Set TTL
         * 1 day for auth token
         * 7 days for default refresh token
         */
        $accessTokenTTL = $this->ttl(1440);
        $refreshTokenTTL = $this->ttl(10080);

        $accessTokenPayload = [
            'iss' => 'https://unityunsrat.dev',
            'aud' => 'https://unityunsrat.dev',
            'iat' => time(),
            'exp' => $accessTokenTTL,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'nim' => $user->nim,
                'email' => $user->email,
                'profile_picture' => $user->profile_picture,
                'division_id' => $user->division_id,
                'role_id' => $user->role_id,
                'is_accepted' => $user->is_accepted,
            ]
        ];

        $refreshTokenPayload = $accessTokenPayload;
        unset($refreshTokenPayload['data']);
        $refreshTokenPayload['data']['id'] = $user->id;
        $refreshTokenPayload['exp'] = $refreshTokenTTL;

        $privateKeyPath = base_path('private_key.pem');
        if (!file_exists($privateKeyPath)) {
            throw new \Exception("Private key file not found.");
        }
        $privateKey = file_get_contents($privateKeyPath);

        $jwtPair = [
            'access_token' => JWT::encode($accessTokenPayload, $privateKey, 'RS256'),
            'refresh_token' => JWT::encode($refreshTokenPayload, $privateKey, 'RS256'),
        ];

        return $jwtPair;
    }

    public function refreshTokenLogin()
    {
        try {
            $userId = Internals::getUserContextId();
            $user = User::find($userId);

            if (empty($user)) {
                throw new UnauthorizedAccess("Token tidak Valid");
            }

            $newToken = $this->generatePairToken($user);
            return $this->sendSuccessResponse($newToken, "Login Berhasil");
        } catch (Throwable $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    private function generateExpiredToken()
    {
        $accessTokenPayload = [
            'iss' => 'https://unityunsrat.dev',
            'aud' => 'https://unityunsrat.dev',
            'iat' => time(),
            'exp' => time(),
            'data' => Crypt::encrypt([
                'id' => Internals::getUserContextId(),
                'email' => Internals::getUserContextEmail(),
                'name' => Internals::getUserContextUsername(),
            ])
        ];

        $refreshTokenPayload = $accessTokenPayload;
        unset($refreshTokenPayload['data']);
        $refreshTokenPayload['data']['id'] = Internals::getUserContextId();

        $privateKey = file_get_contents(base_path('/' . env('JWT_PRIVATE_KEY')));

        $jwtPair = [
            'access_token' => JWT::encode($accessTokenPayload, $privateKey, 'RS256'),
            'refresh_token' => JWT::encode($refreshTokenPayload, $privateKey, 'RS256'),
        ];

        return $jwtPair;
    }

    

    public function logout(Request $request)
    {
        try {
            $token = $this->generateExpiredToken();
            return $this->sendSuccessResponse($token, 'Logout berhasil');
        } catch (Throwable $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    private function getDivision($division_name)
    {
        $division = Division::where("name", $division_name)->first();
        if($division == null) return response()->json(['message' => 'Divisi tersebut tidak ada']);
        $division_id = $division->id;
        return $division_id;
    }

}