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
