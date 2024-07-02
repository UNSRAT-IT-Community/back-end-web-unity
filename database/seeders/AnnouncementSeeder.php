<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AnnouncementSeeder extends Seeder
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

        $announcements = [
            ['title' => 'New Year Event', 'content' => 'Join us to celebrate the New Year!', 'image_url' => 'https://example.com/newyear.jpg'],
            ['title' => 'Summer Workshop', 'content' => 'A workshop about summer activities.', 'image_url' => 'https://example.com/summer.jpg'],
            ['title' => 'Tech Conference 2024', 'content' => 'Explore the latest in technology.', 'image_url' => 'https://example.com/tech.jpg'],
            ['title' => 'Health and Wellness', 'content' => 'Learn tips about health and wellness.', 'image_url' => 'https://example.com/health.jpg'],
            ['title' => 'Art Exhibition', 'content' => 'Discover beautiful art pieces.', 'image_url' => 'https://example.com/art.jpg']
        ];

        foreach ($announcements as $announcement) {
            if ($eligibleUserIds->isNotEmpty()) {
                DB::table('announcements')->insert([
                    'id' => Str::uuid()->toString(),
                    'title' => $announcement['title'],
                    'content' => $announcement['content'],
                    'image_url' => $announcement['image_url'],
                    'creator_id' => $eligibleUserIds->random(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}