<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_setting_id')->constrained()->cascadeOnDelete();
            $table->integer('quiz_number');        // 1, 2, 3...
            $table->string('quiz_label');          // e.g. "Quiz 1", "Midterm Quiz"
            $table->decimal('max_score', 5, 2);    // e.g. 20, 30, 25
            $table->timestamps();

            $table->unique(['enrollment_setting_id', 'quiz_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_settings');
    }
};