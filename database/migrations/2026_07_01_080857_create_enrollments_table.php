<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_setting_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'finalized', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();

            // Prevent duplicate enrollment per semester
            $table->unique(['user_id', 'enrollment_setting_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};