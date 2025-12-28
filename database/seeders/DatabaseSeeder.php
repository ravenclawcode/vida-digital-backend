<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'role_name' => 'Admin'],
            ['id' => 2, 'role_name' => 'Counselor'],
            ['id' => 3, 'role_name' => 'User'],
        ]);

        User::create([
            'username' => 'superadmin',
            'email' => 'admin@vida.com',
            'password' => Hash::make('admin123'),
            'role_id' => 1,
        ]);

        $this->call([
            MoodLogSeeder::class,
        ]);
    }
}

