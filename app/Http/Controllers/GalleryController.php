<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\GalleryRepositoryInterface;
use App\Http\Requests\CreateGalleryRequest;
use App\Http\Repositories\UserRepository;
use App\Http\Repositories\GalleryRepository;
use App\Traits\FirebaseStorageTrait;
use App\Traits\TokenTrait; // Import the new TokenTrait
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    use FirebaseStorageTrait, TokenTrait; 

    protected $galleryRepo;
    protected $firebaseStorage;
    protected $userRepository;

    public function __construct(GalleryRepositoryInterface $galleryRepo, UserRepository $userRepository)
    {
        $this->galleryRepo = $galleryRepo;
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

    public function create(CreateGalleryRequest $request)
    {
        $creatorId = $this->getUserIdFromToken($request);

        $userData = $this->decodeToken($request);
        if (!$userData) {
            return $this->sendUnauthorizedResponse('Token tidak valid atau telah kadaluarsa');
        }

        $roleName = $this->userRepository->getRoleNameById($userData->role_id);
        if ($roleName === 'member') {
            return $this->sendForbiddenResponse('User tidak memiliki hak untuk mengubah gallery');
        }

        if (!$request->hasFile('photo_url') && empty($request->content)) {
            return response()->json([
                'status' => 413,
                'message' => 'Ukuran file melebihi batas maksimum yang diizinkan.',
                'data' => null
            ], 413);
        }

        try {
            $photoUrl = $this->uploadImageToFirebase($request->file('photo_url'), 'galleries');

            $galleryData = [
                'photo_url' => $photoUrl,
                'caption' => $request->caption,
                'creator_id' => $creatorId
            ];

            $this->galleryRepo->insertGallery($galleryData);

            return $this->sendSuccessCreatedResponse(null, 'Berhasil mengupload gallery');
        } catch (\Exception $e) {
            return $this->sendValidationErrorResponse($e->getMessage());
        }
    }

    public function getAllGallery()
    {
        try {
            $gallery = $this->galleryRepo->getAllGallery();
            return $this->sendSuccessResponse($gallery, 'Berhasil mendapatkan isi gallery');
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    public function getGallery($galleryId)
    {
        try {
            $gallery = $this->galleryRepo->getGalleryById($galleryId);
            if (!$gallery) {
                return $this->sendNotFoundResponse('Gallery tidak ditemukan');
            }
            return $this->sendSuccessResponse($gallery, 'Berhasil mendapatkan detail gallery');
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    public function update(Request $request, $id)
    {
        $creatorId = $this->getUserIdFromToken($request);

        if (is_string($creatorId)) {
            $userData = $this->decodeToken($request);
            if (!$userData) {
                return $this->sendUnauthorizedResponse('Token tidak valid atau telah kadaluarsa');
            }

            $roleName = $this->userRepository->getRoleNameById($userData->role_id);
            if ($roleName === 'member') {
                return $this->sendForbiddenResponse('User tidak memiliki hak untuk mengubah gallery');
            }

            try {
                $gallery = $this->galleryRepo->getGalleryById($id);
                if (!$gallery) {
                    return $this->sendNotFoundResponse('Gallery tidak ditemukan');
                }
                // $this->galleryRepo->deleteGallery($id); // Delete the existing gallery

                if ($request->hasFile('photo_url')) {
                    $photoUrl = $this->uploadImageToFirebase($request->file('photo_url'), 'galleries');
                } else {
                    $photoUrl = $gallery->photo_url; // Retain the existing photo_url if no new image is provided
                }

                $this->galleryRepo->Updategallery($id, [
                    'photo_url' =>$photoUrl,
                    'caption' => $request->caption,
                    'creator_id' => $creatorId
                ]);

                // $this->galleryRepo->insertGallery($newGalleryData); // Insert the new gallery data

                return $this->sendSuccessResponse(['id' => $id, 'photo_url' => $photoUrl, 'caption' => $request->caption], 'Berhasil mengubah gallery');
            } catch (\Exception $e) {
                return $this->sendValidationErrorResponse($e->getMessage());
            }
        }

        return $creatorId;
    }
}