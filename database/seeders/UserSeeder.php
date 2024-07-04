<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('users')->truncate();

        $roleIds = DB::table('roles')->pluck('id');

        $divisionIds = DB::table('divisions')->pluck('id');

        $users = [
            ['name' => 'John Doe', 'nim' => '210211060001', 'email' => 'john@example.com', 'profile_picture' => 'https://example.com/path/to/image1.jpg', 'password' => bcrypt('password'), 'is_accepted' => 'accepted'],
            ['name' => 'Jane Smith', 'nim' => '210211060002', 'email' => 'jane@example.com', 'profile_picture' => 'https://example.com/path/to/image2.jpg', 'password' => bcrypt('password'), 'is_accepted' => 'accepted'],
            ['name' => 'Alice Johnson', 'nim' => '210211060003', 'email' => 'alice@example.com', 'profile_picture' => 'https://example.com/path/to/image3.jpg', 'password' => bcrypt('password'), 'is_accepted' => 'accepted'],
            ['name' => 'Bob Brown', 'nim' => '210211060004', 'email' => 'bob@example.com', 'profile_picture' => 'https://example.com/path/to/image4.jpg', 'password' => bcrypt('password'), 'is_accepted' => 'pending'],
            ['name' => 'Charlie Davis', 'nim' => '210211060005', 'email' => 'charlie@example.com', 'profile_picture' => 'https://example.com/path/to/image5.jpg', 'password' => bcrypt('password'), 'is_accepted' => 'pending'],
            ['name' => 'Daisy Evans', 'nim' => '210211060006', 'email' => 'daisy@example.com', 'profile_picture' => 'https://example.com/path/to/image6.jpg', 'password' => bcrypt('password'), 'is_accepted' => 'pending'],
            ['name' => 'Ella Fitzgerald', 'nim' => '210211060007', 'email' => 'ella@example.com', 'profile_picture' => 'https://example.com/path/to/image7.jpg', 'password' => bcrypt('password'), 'is_accepted' => 'denied'],
            ['name' => 'George Harris', 'nim' => '210211060008', 'email' => 'george@example.com', 'profile_picture' => 'https://example.com/path/to/image8.jpg', 'password' => bcrypt('password'), 'is_accepted' => 'denied'],
            ['name' => 'Ivy Johnson', 'nim' => '210211060009', 'email' => 'ivy@example.com', 'profile_picture' => 'https://example.com/path/to/image9.jpg', 'password' => bcrypt('password'), 'is_accepted' => 'denied']
        ];

        foreach ($users as $user) {
            DB::table('users')->insert([
                'id' => Str::uuid()->toString(),
                'name' => $user['name'],
                'nim' => $user['nim'],
                'email' => $user['email'],
                'profile_picture' => $user['profile_picture'],
                'role_id' => $roleIds->random(),
                'division_id' => $divisionIds->random(),
                'password' => $user['password'],
                'is_accepted' => $user['is_accepted'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}