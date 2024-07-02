<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UpcomingEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eligibleUserIds = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->whereIn('roles.name', ['coordinator', 'committee'])
            ->where('users.is_accepted', 'accepted')
            ->pluck('users.id');

        $events = [
            ['title' => 'Tech Expo 2024', 'content' => 'A showcase of new technology and innovations.', 'start_time' => '10:00:00', 'end_time' => '18:00:00', 'image_url' => 'http://example.com/event1.jpg'],
            ['title' => 'Annual Developer Conference', 'content' => 'Meet and learn from top developers around the world.', 'start_time' => '09:00:00', 'end_time' => '17:00:00', 'image_url' => 'http://example.com/event2.jpg'],
            ['title' => 'Startup Pitch Day', 'content' => 'A day for startups to pitch their ideas to potential investors.', 'start_time' => '08:00:00', 'end_time' => '20:00:00', 'image_url' => 'http://example.com/event3.jpg'],
            ['title' => 'Digital Marketing Workshop', 'content' => 'Learn about the latest trends in digital marketing.', 'start_time' => '11:00:00', 'end_time' => '16:00:00', 'image_url' => 'http://example.com/event4.jpg'],
            ['title' => 'AI and Machine Learning Symposium', 'content' => 'Exploring the future of AI and machine learning.', 'start_time' => '12:00:00', 'end_time' => '19:00:00', 'image_url' => 'http://example.com/event5.jpg']
        ];

        foreach ($events as $event) {
            DB::table('upcoming_events')->insert([
                'id' => Str::uuid()->toString(),
                'title' => $event['title'],
                'content' => $event['content'],
                'start_time' => $event['start_time'],
                'end_time' => $event['end_time'],
                'image_url' => $event['image_url'],
                'creator_id' => $eligibleUserIds->random(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}