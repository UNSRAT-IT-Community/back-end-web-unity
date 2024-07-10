<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Response;
use App\Models\User;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->middleware('pengurus');
        $this->userRepository = $userRepository;
    }

    public function index ()
    {
        try {
            $users = $this->userRepository->getUserData();
            return $this->sendSuccessResponse($users);
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }
}
