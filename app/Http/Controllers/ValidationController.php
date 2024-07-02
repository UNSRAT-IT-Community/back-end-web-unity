<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Interfaces\UserRepositoryInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;

class ValidationController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validateTokenService(Request $request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return $this->sendBadRequestResponse("Token is required");
            }

            $publicKeyPath = base_path('public_key.pem');
            if (!file_exists($publicKeyPath)) {
                throw new \Exception("Public key file not found.");
            }
            $publicKey = file_get_contents($publicKeyPath);
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

            $user = $this->userRepository->getUserById($decoded->data->id, [
                'id', 'name', 'nim', 'email', 'profile_picture', 'role_id', 'division_id', 'is_accepted'
            ]);

            if (!$user) {
                return $this->sendNotFoundResponse("User not found");
            }

            if ($user->is_accepted !== 'accepted') {
                return $this->sendUnauthorizedResponse("User access is not accepted. Status: " . $user->is_accepted);
            }

            return $this->sendSuccessResponse([
                'id' => $user->id,
                'name' => $user->name,
                'nim' => $user->nim,
                'email' => $user->email,
                'profile_picture' => $user->profile_picture,
                'role_id' => $user->role_id,
                'division_id' => $user->division_id
            ], "Valid");

        } catch (Throwable $e) {
            return $this->sendInternalServerErrorResponse($e, "Token validation failed");
        }
    }
}