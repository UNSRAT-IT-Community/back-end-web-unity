<?php

namespace App\Http\Controllers;

use App\Traits\TokenTrait;
use Illuminate\Http\Request;
use App\Http\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key; 

class UserController extends Controller
{
    use TokenTrait;
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserIdFromToken(Request $request)
    {
        $token = $request->bearerToken();
    
        $publicKey = file_get_contents(base_path('public_key.pem'));
    
        try {
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));
            if (isset($decoded->data->id)) {
                return $decoded->data->id;
            }
    
            throw new \Exception("Token tidak valid !");
        } catch (\Exception $e) {
            return $this->sendUnauthorizedResponse($e->getMessage());
        }
    }
    public function index (Request $request)
    {
        try {
            $userData = $this->decodeToken($request);
            $userRole = $this->userRepository->getRoleNameById($userData->role_id);

            if ($userRole && in_array($userRole, ['committee', 'coordinator'])) {
                $members = $this->userRepository->getUserDataByRole(['member']);
                return $this->sendSuccessResponse($members);
            } else {
                return $this->sendUnauthorizedResponse("Anda tidak memiliki izin untuk melihat data ini.");
            }
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }
}