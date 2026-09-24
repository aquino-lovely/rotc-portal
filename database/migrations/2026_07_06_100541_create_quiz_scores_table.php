<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_setting_id')->constrained()->cascadeOnDelete();
            $table->integer('quiz_number');        // 1, 2, 3, 4...
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('max_score', 5, 2);    // max for this quiz
            $table->timestamps();

            $table->unique(['user_id', 'enrollment_setting_id', 'quiz_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_scores');
    }
};