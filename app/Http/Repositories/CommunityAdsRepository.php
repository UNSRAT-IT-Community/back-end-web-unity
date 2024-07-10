<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\CommunityAdsInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommunityAdsRepository implements CommunityAdsInterface
{
    public function getAllCommunityAds()
    {
        return DB::table('community_ads')
            ->join('users', 'community_ads.creator_id', '=', 'users.id')
            ->select(
                'community_ads.id',
                'community_ads.title',
                'community_ads.content',
                'community_ads.image_url',
                'community_ads.price',
                'users.name as creator_name',
                'community_ads.created_at',
                'community_ads.updated_at',
            )
            ->get();
    }

    public function getCommunityAdsByUuid($uuid)
    {
        return DB::table('community_ads')
        ->join('users', 'community_ads.creator_id', '=', 'users.id')
        ->select(
            'community_ads.id',
            'community_ads.title',
            'community_ads.content',
            'community_ads.image_url',
            'community_ads.price',
            'users.name as creator_name',
            'community_ads.created_at',
            'community_ads.updated_at',
        )
        ->where('community_ads.id', $uuid)
        ->get();
    }
}
