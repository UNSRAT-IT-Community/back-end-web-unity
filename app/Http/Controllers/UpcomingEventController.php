<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Chatbot;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\Log;
use App\Traits\FirebaseStorageTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Repositories\UserRepository;
use App\Http\Requests\CreateUpcomingEventRequest;
use App\Http\Requests\UpdateUpcomingEventRequest;
use App\Traits\TokenTrait; // Import the new TokenTrait
use App\Http\Interfaces\UpcomingEventRepositoryInterface;

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
        $userData = $this->getToken($request);
        $this->validateRoleUser($userData);

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

    public function getUpcomingEvent($upcomingEventId)
    {
        try {
            $event = $this->upcomingEventRepo->getUpcomingEvent($upcomingEventId);
            if (!$event) {
                return $this->sendNotFoundResponse('Acara mendatang tidak ditemukan');
            }
            return $this->sendSuccessResponse($event, 'Berhasil mendapatkan detail acara mendatang');
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    public function update(UpdateUpcomingEventRequest $request, $upcomingEventId)
    {
        $creatorId = $this->getUserIdFromToken($request);

        $userData = $this->getToken($request);
        $this->validateRoleUser($userData);
        $event = $this->getUpcomingEventId($upcomingEventId);

        try {
            $oldImageUrl = $event->image_url;
            $oldFilePath = $this->extractFilePathFromUrl($oldImageUrl);
            $this->deleteImageFromFirebase($oldFilePath);
            $newImageUrl = $request->hasFile('image') ? $this->uploadImageToFirebase($request->file('image'), 'upcoming-event', $oldImageUrl) : $oldImageUrl;

            $eventData = [
                'title' => $request->input("title"),
                'content' => $request->input("content"),
                'start_time' => $request->input("start_time"),
                'end_time' => $request->input("end_time"),
                'image_url' => $newImageUrl
            ];

            $this->upcomingEventRepo->updateUpcomingEvent($upcomingEventId, $eventData);

            return $this->sendSuccessResponse(null, 'Berhasil mengubah acara mendatang');
        } catch (\Exception $e) {
            return $this->sendValidationErrorResponse($e->getMessage());
        }
    }

    public function delete(Request $request, $upcomingEventId)
    {
        try {
            $userData = $this->getToken($request);
            $this->validateRoleUser($userData);
            $event = $this->getUpcomingEventId($upcomingEventId);
    
            if ($event->image_url) {
                $filePath = $this->extractFilePathFromUrl($event->image_url);
                $this->deleteImageFromFirebase($filePath);
            }
    
            $this->upcomingEventRepo->deleteUpcomingEvent($upcomingEventId);
    
            return $this->sendSuccessResponse(null, 'Berhasil menghapus acara mendatang');
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }
    
    protected function getToken($request)
    {
        $userData = $this->decodeToken($request);
        if (!$userData) {
            return $this->sendUnauthorizedResponse('Token tidak valid atau telah kadaluarsa');
        }
        return $userData;
    }

    protected function validateRoleUser($userData)
    {
        $roleName = $this->userRepository->getRoleNameById($userData->role_id);
        if ($roleName === 'member') {
            return $this->sendForbiddenResponse('User tidak memiliki hak untuk mengubah postingan acara mendatang');
        }
    }

    protected function getUpcomingEventId($upcomingEventId)
    {
        $event = $this->upcomingEventRepo->getUpcomingEvent($upcomingEventId);
        if (!$event) {
            return $this->sendNotFoundResponse('Acara mendatang tidak ditemukan');
        }
        return $event;
    }


}
