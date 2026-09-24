<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_year'); // e.g. 2025-2026
            $table->enum('semester', ['First Semester', 'Second Semester']);
            $table->boolean('is_open')->default(false); // enrollment open/closed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_settings');
    }
};