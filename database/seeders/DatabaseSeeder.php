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
        $roles = [
            ['id' => 1, 'role_name' => 'Admin'],
            ['id' => 2, 'role_name' => 'Counselor'],
            ['id' => 3, 'role_name' => 'User'],
            ['id' => 4, 'role_name' => 'Supervisor'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['id' => $role['id']],
                ['role_name' => $role['role_name']]
            );
        }

        User::firstOrCreate(
            ['email' => 'superadmin@vida.com'],
            [
                'username' => 'superadmin',
                'password' => Hash::make('superadmin123'),
                'role_id' => 1,
            ]
        );
    }
}
