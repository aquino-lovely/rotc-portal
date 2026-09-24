<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // username is separate from email so cadets can log in with either if you want later
            $table->string('username')->unique()->after('name');

            $table->enum('role', ['super_admin', 'admin', 'cadet'])
                  ->default('cadet')
                  ->after('username');

            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'status']);
        });
    }
};