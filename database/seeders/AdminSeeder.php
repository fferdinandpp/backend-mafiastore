<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admins')->insert([
            'email' => 'admin@webmafia.com',
            'password' => Hash::make('admin0000'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
