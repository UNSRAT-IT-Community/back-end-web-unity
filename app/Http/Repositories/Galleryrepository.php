<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\GalleryRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GalleryRepository implements GalleryRepositoryInterface
{
    public function getAllGallery()
    {
        return DB::table('galleries')
        ->join('users', 'galleries.creator_id', '=', 'users.id')
        ->select(
            'galleries.id',
            'galleries.photo_url',
            'galleries.caption',
            'users.name',
        )
        ->get();
    }

    public function insertGallery($data)
    {
        return DB::table('galleries')->insert([
            'id' => Str::uuid(),
            'photo_url' => $data['photo_url'],
            'caption' => $data['caption'],
            'creator_id' => $data['creator_id'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    public function getGalleryById($id)
    {
        return DB::table('galleries')->where('id', $id)->first();
    }

    public function deleteGallery($id)
    {
        return DB::table('galleries')->where('id', $id)->delete();
    }
    public function updateGallery($id, $data)
    {
        return DB::table('galleries')->where('id', $id)->update([
            'photo_url' => $data['photo_url'],
            'caption' => $data['caption'],
            'creator_id' => $data['creator_id'],
            'updated_at' => now()
        ]);
    }
}