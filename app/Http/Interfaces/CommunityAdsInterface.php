<?php

namespace App\Http\Interfaces;

interface CommunityAdsInterface
{
    public function getAllCommunityAds();
    public function getCommunityAdsByUuid($id);
}
