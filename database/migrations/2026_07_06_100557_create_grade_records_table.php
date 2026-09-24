<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_setting_id')->constrained()->cascadeOnDelete();
            $table->decimal('attendance_score', 5, 2)->nullable();  // percentage 0-100
            $table->decimal('exam_score', 5, 2)->nullable();        // out of 50
            $table->decimal('final_score', 5, 2)->nullable();       // computed
            $table->decimal('grade', 4, 2)->nullable();             // 1.0-5.0
            $table->enum('status', ['pass', 'fail'])->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'enrollment_setting_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_records');
    }
};