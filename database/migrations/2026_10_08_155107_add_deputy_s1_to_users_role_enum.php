<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','cadet','S1','GMA','deputy_s1') NOT NULL DEFAULT 'cadet'");
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'deputy_s1')->delete();

        DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','cadet','S1','GMA') NOT NULL DEFAULT 'cadet'");
    }
};