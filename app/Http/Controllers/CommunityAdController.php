<?php

namespace App\Http\Controllers;

use App\Models\CommunityAd;
use Illuminate\Http\Request;
use App\Http\Interfaces\CommunityAdsInterface;

class CommunityAdController extends Controller
{
    protected $communityAdRepo;

    public function __construct(CommunityAdsInterface $communityAdRepo)
    {
        $this->communityAdRepo = $communityAdRepo;
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CommunityAd $communityAd)
    {
        $result =  $this->communityAdRepo->getCommunityAdsByUuid($communityAd->id);
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
