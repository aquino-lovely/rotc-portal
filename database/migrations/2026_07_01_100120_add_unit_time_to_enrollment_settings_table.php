<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment_settings', function (Blueprint $table) {
            $table->string('unit')->nullable()->after('is_open');
            $table->string('time')->nullable()->after('unit');
        });
    }

    public function down(): void
    {
        Schema::table('enrollment_settings', function (Blueprint $table) {
            $table->dropColumn(['unit', 'time']);
        });
    }
};