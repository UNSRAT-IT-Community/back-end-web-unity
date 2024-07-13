<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->truncate(); 

        $roles = [
            ['name' => 'Anggota'],
            ['name' => 'Pengurus'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'id' => Str::uuid()->toString(),
                'name' => $role['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}