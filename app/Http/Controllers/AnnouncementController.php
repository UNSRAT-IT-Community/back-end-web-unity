<?php

namespace App\Http\Controllers;


use App\Http\Interfaces\AnnouncementRepositoryInterface;
use App\Http\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Http\Response;
use App\Traits\FirebaseStorageTrait;
use App\Traits\TokenTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;



class AnnouncementController extends Controller
{
    use FirebaseStorageTrait, TokenTrait;
    protected $announcementRepository;
    protected $firebaseStorage;
    protected $userRepository;

    public function __construct(AnnouncementRepositoryInterface $announcementRepository, UserRepositoryInterface $userRepository)
    {
        $this->announcementRepository = $announcementRepository;
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
            return $this->sendUnauthorizedResponse($e->getMessage());
        }
    }

    public function index()
    {
        try {
            $announcements = $this->announcementRepository->getAllAnnouncements();
            return $this->sendSuccessResponse($announcements);
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    public function store(Request $request)
    {
        $creatorId = $this->getUserIdFromToken($request);

        $userData = $this->decodeToken($request);
        if (!$userData) {
            return $this->sendUnauthorizedResponse('Token tidak valid atau telah kadaluarsa');
        }

        $roleName = $this->userRepository->getRoleNameById($userData->role_id);
        if ($roleName === 'member') {
            return $this->sendForbiddenResponse('User tidak memiliki hak untuk menambah pengumuman baru');
        }
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $data = [
                'title' => $request->title,
                'content' => $request->content,
                'image' => $request->file('image'),
                'creator_id' => $creatorId
            ];

            $this->announcementRepository->createAnnouncement($data);

            return $this->sendSuccessResponse(null, 'Pengumuman berhasil dibuat!', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    public function update(Request $request, Announcement $announcement)
    {
        $creatorId = $this->getUserIdFromToken($request);

        $userData = $this->decodeToken($request);
        if (!$userData) {
            return $this->sendUnauthorizedResponse('Token tidak valid atau telah kadaluarsa');
        }

        $roleName = $this->userRepository->getRoleNameById($userData->role_id);
        if ($roleName === 'member') {
            return $this->sendForbiddenResponse('User tidak memiliki hak untuk mengubah pengumuman baru');
        }
        try {

            if ($announcement->creator_id !== $creatorId) {
                return $this->sendForbiddenResponse();
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $data = [
                'title' => $request->title,
                'content' => $request->content,
                'image' => $request->file('image'),
            ];

            $updatedAnnouncement = $this->announcementRepository->updateAnnouncement($announcement, $data);

            return $this->sendSuccessResponse($updatedAnnouncement, 'Pengumuman berhasil diubah!');
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    public function destroy(Request $request,Announcement $announcement)
    {
        $creatorId = $this->getUserIdFromToken($request);

        $userData = $this->decodeToken($request);
        if (!$userData) {
            return $this->sendUnauthorizedResponse('Token tidak valid atau telah kadaluarsa');
        }

        $roleName = $this->userRepository->getRoleNameById($userData->role_id);
        if ($roleName === 'member') {
            return $this->sendForbiddenResponse('User tidak memiliki hak untuk menghapus pengumuman baru');
        }

        try {
            if ($announcement->creator_id !== $creatorId) {
                return $this->sendForbiddenResponse();
            }

            $this->announcementRepository->deleteAnnouncement($announcement);
            return $this->sendSuccessResponse(null, 'Pengumuman berhasil dihapus!');
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    public function show(Announcement $announcement)
    {
        try {
            return $this->sendSuccessResponse($announcement);
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }
}
