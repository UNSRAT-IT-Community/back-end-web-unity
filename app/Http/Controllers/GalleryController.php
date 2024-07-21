<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\GalleryRepositoryInterface;
use App\Http\Interfaces\UserRepositoryInterface;
use App\Http\Requests\UpdateGalleryRequest;
use Illuminate\Http\Request;
use App\Models\Gallery;
use Illuminate\Http\Response;
use App\Traits\FirebaseStorageTrait;
use App\Traits\TokenTrait; 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Http\Requests\CreateGalleryRequest;
use App\Http\Repositories\UserRepository;
use App\Http\Repositories\GalleryRepository;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    use FirebaseStorageTrait, TokenTrait; 
    protected $galleryRepository;
    protected $firebaseStorage;
    protected $userRepository;

    public function __construct(GalleryRepositoryInterface $galleryRepository, UserRepository $userRepository)
    {
        $this->galleryRepository = $galleryRepository;
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

    public function store(Request $request)
    {
        $creatorId = $this->getUserIdFromToken($request);

        $userData = $this->decodeToken($request);
        if (!$userData) {
            return $this->sendUnauthorizedResponse('Token tidak valid atau telah kadaluarsa');
        }

        $roleName = $this->userRepository->getRoleNameById($userData->role_id);
        if ($roleName === 'member') {
            return $this->sendForbiddenResponse('User tidak memiliki hak untuk menambah gambar baru');
        }
        try {
            $request->validate([
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'caption' => 'required|string|max:255'
            ]);

            $data = [
                'image' => $request->file('image'),
                'caption' => $request->caption,
                'creator_id' => $creatorId
            ];

            $this->galleryRepository->createGallery($data);

            return $this->sendSuccessResponse(null, 'Gambar berhasil dibuat!', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    public function getAllGallery()
    {
        try {
            $gallery = $this->galleryRepository->getAllGallery();
            return $this->sendSuccessResponse($gallery, 'Berhasil mendapatkan isi gallery');
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    public function getGallery($galleryId)
    {
        try {
            $gallery = $this->galleryRepository->getGalleryById($galleryId);
            if (!$gallery) {
                return $this->sendNotFoundResponse('Gallery tidak ditemukan');
            }
            return $this->sendSuccessResponse($gallery, 'Berhasil mendapatkan detail gallery');
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    public function update(UpdateGalleryRequest $request, Gallery $gallery)
{
    
    $creatorId = $this->getUserIdFromToken($request);
    if (!$creatorId) {
        return $this->sendUnauthorizedResponse('Token tidak valid atau telah kadaluarsa');
    }

    $userData = $this->decodeToken($request);
    if (!$userData) {
        return $this->sendUnauthorizedResponse('Token tidak valid atau telah kadaluarsa');
    }

    $roleName = $this->userRepository->getRoleNameById($userData->role_id);
    if ($roleName === 'member') {
        return $this->sendForbiddenResponse('User tidak memiliki hak untuk mengubah gallery');
    }

    try {
        if ($gallery->creator_id !== $creatorId) {
            return $this->sendForbiddenResponse('Tidak memiliki hak untuk mengubah gallery ini');
        }

        $photoUrl = $gallery->photo_url; 
        if ($request->hasFile('photo_url')) {
            $oldFilePath = $this->extractFilePathFromUrl($photoUrl);
            if ($oldFilePath) {
                $this->deleteImageFromFirebase($oldFilePath);
            }
            $photoUrl = $this->uploadImageToFirebase($request->file('photo_url'), 'gallery');
        }

        $updateData = [
            'photo_url' => $photoUrl,
            'caption' => $request->input('caption', $gallery->caption), 
        ];

        $this->galleryRepository->updateGallery($gallery->id, $updateData);

        return $this->sendSuccessResponse(['id' => $gallery->id, 'photo_url' => $photoUrl, 'caption' => $request->input('caption', $gallery->caption)], 'Berhasil mengubah gallery');
    } catch (\Exception $e) {
        return $this->sendValidationErrorResponse($e->getMessage());
    }
}


        public function destroy(Request $request,Gallery $gallery)
    {
        $creatorId = $this->getUserIdFromToken($request);

        $userData = $this->decodeToken($request);
        if (!$userData) {
            return $this->sendUnauthorizedResponse('Token tidak valid atau telah kadaluarsa');
        }

        $roleName = $this->userRepository->getRoleNameById($userData->role_id);
        if ($roleName === 'member') {
            return $this->sendForbiddenResponse('User tidak memiliki hak untuk menghapus gambar baru');
        }

        try {
            if ($gallery->creator_id !== $creatorId) {
                return $this->sendForbiddenResponse();
            }

            $this->galleryRepository->deleteGallery($gallery);
            return $this->sendSuccessResponse(null, 'Gambar berhasil dihapus!');
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }
        
}