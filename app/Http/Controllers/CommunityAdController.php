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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result =  $this->communityAdRepo->getAllCommunityAds();
        return $this->sendSuccessResponse($result, 'Berhasil mendapatkan daftar Iklan Komunitas');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCommunityAdRequest $request)
    {
        $creatorId = $this->getUserIdFromToken($request);

        $userData = $this->decodeToken($request);
        if (!$userData) {
            return $this->sendUnauthorizedResponse('Token tidak valid atau telah kadaluarsa');
        }

        $roleName = $this->userRepository->getRoleNameById($userData->role_id);
        if ($roleName !== 'committee') {
            return $this->sendForbiddenResponse('User tidak memiliki hak untuk menambah postingan iklan komunitas');
        }

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

            return $this->sendSuccessCreatedResponse(null, 'Berhasil membuat Iklan Komunitas');
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
        return $this->sendSuccessResponse($result, 'Berhasil mendapatkan Iklan Komunitas');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CommunityAd $communityAd)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CommunityAd $communityAd)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CommunityAd $communityAd)
    {
        //
    }
}
