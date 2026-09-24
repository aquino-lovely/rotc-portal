<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'superadmin_root'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin.root@rotcportal.test',
                'password' => 'ChangeMe123!',
                'role' => 'super_admin',
                'status' => 'approved',
                'must_change_password' => true,
            ]
        );
    }
}