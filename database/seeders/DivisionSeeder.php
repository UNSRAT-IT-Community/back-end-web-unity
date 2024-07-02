<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('divisions')->truncate();

        $divisions = [
            ['name' => 'Front-end'],
            ['name' => 'Back-end'],
            ['name' => 'UI/UX'],
            ['name' => 'Machine Learning'],
            ['name' => 'Unity Engineer'],
            ['name' => '3D Artist'],
        ];

        foreach ($divisions as $division) {
            DB::table('divisions')->insert([
                'id' => Str::uuid()->toString(),
                'name' => $division['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}