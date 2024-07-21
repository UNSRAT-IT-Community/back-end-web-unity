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

    public function getCommunityAdsByUuid($id)
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
        ->where('community_ads.id', $id)
        ->first();
    }

    public function insertCommunityAds($data){
        return DB::table('community_ads')->insert([
            'id' => Str::uuid(),
            'title' => $data['title'],
            'content' => $data['content'],
            'image_url' => $data['image_url'],
            'price' => $data['price'],
            'creator_id' => $data['creator_id'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function updateCommunityAds($id, $data)
    {
        return DB::table('community_ads')
        ->where('id', $id)
        ->update([
            'title' => $data['title'],
            'content' => $data['content'],
            'price' => $data['price'],
            'image_url' => $data['image_url'],
            'updated_at' => now(),
        ]);
    }

    public function deleteCommunityAd($id)
    {
        return DB::table('community_ads')->where('id', $id)->delete();
    }
}
