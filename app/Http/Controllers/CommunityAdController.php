<?php

namespace App\Http\Controllers;

use App\Models\CommunityAd;
use Illuminate\Http\Request;
use App\Http\Interfaces\CommunityAdsInterface;
use App\Http\Repositories\UserRepository;
use App\Traits\FirebaseStorageTrait;
use App\Traits\TokenTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Http\Requests\CreateCommunityAdRequest;
use App\Http\Requests\UpdateCommunityAdRequest;

class CommunityAdController extends Controller
{
    use FirebaseStorageTrait, TokenTrait;

    protected $communityAdRepo;
    protected $firebaseStorage;
    protected $userRepository;

    public function __construct(CommunityAdsInterface $communityAdRepo, UserRepository $userRepository)
    {
        $this->communityAdRepo = $communityAdRepo;
        $this->userRepository = $userRepository;
        $this->initializeFirebaseStorage();
    }

    public function getUserIdFromToken(Request $request)
    {
        $token = $request->bearerToken();
        $publicKey = file_get_contents(base_path('public_key.pem'));

        try {
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

            if (isset($decoded->data->id)) return $decoded->data->id;

            throw new \Exception("Token tidak valid !");
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result =  $this->communityAdRepo->getAllCommunityAds();
        return $this->sendSuccessResponse($result, 'Berhasil mendapatkan daftar Iklan');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCommunityAdRequest $request)
    {
        $creatorId = $this->getUserIdFromToken($request);

        $userData = $this->decodeToken($request);
        if (!$userData) return $this->sendUnauthorizedResponse('Token tidak valid atau telah kadaluarsa');

        $roleName = $this->userRepository->getRoleNameById($userData->role_id);
        if ($roleName !== 'committee') return $this->sendForbiddenResponse('User tidak memiliki hak untuk menambah postingan iklan komunitas');

        if (!$request->hasFile('image') && empty($request->content)) {
            return response()->json([
                'status' => 413,
                'message' => 'Ukuran file melebihi batas maksimum yang diizinkan.',
                'data' => null
            ], 413);
        }

        try {
            $imageUrl = $this->uploadImageToFirebase($request->file('image'), 'community-ads');

            $data = [
                'title' => $request->title,
                'content' => $request->content,
                'price' => $request->price,
                'image_url' => $imageUrl,
                'creator_id' => $creatorId
            ];

            $this->communityAdRepo->insertCommunityAds($data);

            return $this->sendSuccessCreatedResponse(null, 'Berhasil membuat Iklan');
        } catch (\Exception $e) {
            return $this->sendValidationErrorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $result =  $this->communityAdRepo->getCommunityAdsByUuid($id);
        if(!$result) return $this->sendNotFoundResponse('Iklan tidak ditemukan');
        return $this->sendSuccessResponse($result, 'Berhasil mendapatkan Iklan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommunityAdRequest $request, $id)
    {
        $userData = $this->decodeToken($request);
        if (!$userData) return $this->sendUnauthorizedResponse('Token tidak valid atau telah kadaluarsa');

        $roleName = $this->userRepository->getRoleNameById($userData->role_id);
        if ($roleName !== 'committee') return $this->sendForbiddenResponse('User tidak memiliki hak untuk menambah postingan iklan komunitas');

        $communityAd = $this->communityAdRepo->getCommunityAdsByUuid($id);
        if(!$communityAd) return $this->sendNotFoundResponse('Iklan tidak ditemukan');

        if (!$request->hasFile('image') && empty($request->content)) {
            return response()->json([
                'status' => 413,
                'message' => 'Ukuran file melebihi batas maksimum yang diizinkan.',
                'data' => null
            ], 413);
        }

        try {
            $oldImageUrl = $communityAd->image_url;
            $oldFilePath = $this->extractFilePathFromUrl($oldImageUrl);
            $this->deleteImageFromFirebase($oldFilePath);
            $newImageUrl = $request->hasFile('image') ? $this->uploadImageToFirebase($request->file('image'), 'community-ads', $oldImageUrl) : $oldImageUrl;

            $data = [
                'title' => $request->title,
                'content' => $request->content,
                'price' => $request->price,
                'image_url' => $newImageUrl,
            ];

            $this->communityAdRepo->updateCommunityAds($id, $data);
            return $this->sendSuccessCreatedResponse(null, 'Berhasil mengubah Iklan');
        } catch (\Exception $e) {
            return $this->sendValidationErrorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $userData = $this->decodeToken($request);
            $roleName = $this->userRepository->getRoleNameById($userData->role_id);
            if ($roleName !== 'committee') return $this->sendForbiddenResponse('User tidak memiliki hak untuk menghapus postingan iklan komunitas');

            $communityAd = $this->communityAdRepo->getCommunityAdsByUuid($id);
            if(!$communityAd) return $this->sendNotFoundResponse('Iklan tidak ditemukan');


            if ($communityAd->image_url) {
                $filePath = $this->extractFilePathFromUrl($communityAd->image_url);
                $this->deleteImageFromFirebase($filePath);
            }

            $this->communityAdRepo->deleteCommunityAd($id);

            return $this->sendSuccessResponse(null, 'Berhasil menghapus Iklan');
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }
}
