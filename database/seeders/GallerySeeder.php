<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $eligibleUserIds = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->whereIn('roles.name', ['coordinator', 'committee'])
            ->where('users.is_accepted', 'accepted')
            ->pluck('users.id');

        $galleryItems = [
            ['photo_url' => 'https://example.com/photos/photo1.jpg', 'caption' => 'First gallery image caption'],
            ['photo_url' => 'https://example.com/photos/photo2.jpg', 'caption' => 'Second gallery image caption'],
            ['photo_url' => 'https://example.com/photos/photo3.jpg', 'caption' => 'Third gallery image caption'],
            ['photo_url' => 'https://example.com/photos/photo4.jpg', 'caption' => 'Fourth gallery image caption'],
            ['photo_url' => 'https://example.com/photos/photo5.jpg', 'caption' => 'Fifth gallery image caption'],
        ];

        foreach ($galleryItems as $item) {
            if ($eligibleUserIds->isEmpty()) {
                throw new \Exception("No eligible users found for gallery creation.");
            }

            DB::table('galleries')->insert([
                'id' => Str::uuid()->toString(),
                'photo_url' => $item['photo_url'],
                'caption' => $item['caption'],
                'creator_id' => $eligibleUserIds->random(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}