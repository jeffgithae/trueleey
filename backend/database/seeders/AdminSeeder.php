<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        DB::table('admin')->updateOrInsert(
            ['username' => 'admin'],
            ['password' => Hash::make('admin123456')]
        );
    }
}
