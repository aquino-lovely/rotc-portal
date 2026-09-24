<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    DB::statement("ALTER TABLE enrollments MODIFY status ENUM('pending','approved','rejected','finalized') NOT NULL DEFAULT 'pending'");
}

public function down(): void
{
    DB::statement("ALTER TABLE enrollments MODIFY status ENUM('pending','submitted','approved','rejected','finalized') NOT NULL DEFAULT 'pending'");
}
};
