<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@rotcportal.test',
                'password' => 'ChangeMe123!', // auto-hashed by the User model's cast
                'role' => 'admin',
                'status' => 'approved',
                'must_change_password' => true,
            ]
        );
    }
}