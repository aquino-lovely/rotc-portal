<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cadets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('birthday');
            $table->unsignedTinyInteger('age');
            $table->enum('sex', ['Male', 'Female']);
            $table->string('civil_status'); // Single / Married / etc. (renamed from "Status")
            $table->string('religion')->nullable();
            $table->string('blood_type')->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->string('skill_1')->nullable();
            $table->string('skill_2')->nullable();

            // Address
            $table->string('barangay');
            $table->string('city_municipality');
            $table->string('province');
            $table->string('contact_no');
            $table->string('campus');

            // Academic Information
            $table->string('student_number')->unique();
            $table->string('course');
            $table->string('year_section');

            // Upload
            $table->string('photo_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cadets');
    }
};