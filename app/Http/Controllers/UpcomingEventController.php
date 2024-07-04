<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\UpcomingEventRepositoryInterface;
use App\Http\Requests\CreateUpcomingEventRequest;
use App\Http\Repositories\UserRepository;
use App\Traits\FirebaseStorageTrait;
use App\Traits\TokenTrait; // Import the new TokenTrait
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UpcomingEventController extends Controller
{
    use FirebaseStorageTrait, TokenTrait; 

    protected $upcomingEventRepo;
    protected $firebaseStorage;
    protected $userRepository;

    public function __construct(UpcomingEventRepositoryInterface $upcomingEventRepo, UserRepository $userRepository)
    {
        $this->upcomingEventRepo = $upcomingEventRepo;
        $this->userRepository = $userRepository;
        $this->initializeFirebaseStorage(); 
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
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function create(CreateUpcomingEventRequest $request)
    {
        $creatorId = $this->getUserIdFromToken($request);

        $userData = $this->decodeToken($request);
        if (!$userData) {
            return $this->sendUnauthorizedResponse('Token tidak valid atau telah kadaluarsa');
        }

        $roleName = $this->userRepository->getRoleNameById($userData->role_id);
        if ($roleName === 'member') {
            return $this->sendForbiddenResponse('User tidak memiliki hak untuk menambah postingan acara mendatang');
        }

        if (!$request->hasFile('image') && empty($request->content)) {
            return response()->json([
                'status' => 413,
                'message' => 'Ukuran file melebihi batas maksimum yang diizinkan.',
                'data' => null
            ], 413);
        }

        try {
            $imageUrl = $this->uploadImageToFirebase($request->file('image'), 'upcoming-event');

            $eventData = [
                'title' => $request->title,
                'content' => $request->content,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'image_url' => $imageUrl,
                'creator_id' => $creatorId
            ];

            $this->upcomingEventRepo->insertUpcomingEvent($eventData);

            return $this->sendSuccessCreatedResponse(null, 'Berhasil membuat acara mendatang');
        } catch (\Exception $e) {
            return $this->sendValidationErrorResponse($e->getMessage());
        }
    }

    public function getAllUpcomingEvents()
    {
        try {
            $events = $this->upcomingEventRepo->getAllUpcomingEvents();
            return $this->sendSuccessResponse($events, 'Berhasil mendapatkan daftar acara mendatang');
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }
}