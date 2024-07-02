<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CommunityAdsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $eligibleUserIds = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->whereIn('roles.name', ['committee', 'coordinator'])
            ->where('users.is_accepted', 'accepted')
            ->pluck('users.id');

        if ($eligibleUserIds->isEmpty()) {
            echo "No eligible users found. Please check your users and roles tables.";
            return;
        }

        for ($i = 0; $i < 5; $i++) {
            DB::table('community_ads')->insert([
                'id' => Str::uuid()->toString(),
                'title' => 'Ad Title ' . Str::random(10),
                'content' => 'Content for ad ' . Str::random(20),
                'image_url' => 'https://example.com/image' . $i . '.jpg',
                'price' => rand(100, 1000),
                'creator_id' => $eligibleUserIds->random(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}