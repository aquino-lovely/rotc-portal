<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment_settings', function (Blueprint $table) {
            $table->dropColumn([
                'advance_pft_weight',
                'advance_attendance_weight',
                'advance_quizzes_weight',
                'advance_exam_weight',
                'private_attendance_weight',
                'private_quizzes_weight',
                'private_exam_weight',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('enrollment_settings', function (Blueprint $table) {
            $table->decimal('advance_pft_weight', 5, 2)->nullable();
            $table->decimal('advance_attendance_weight', 5, 2)->nullable();
            $table->decimal('advance_quizzes_weight', 5, 2)->nullable();
            $table->decimal('advance_exam_weight', 5, 2)->nullable();
            $table->decimal('private_attendance_weight', 5, 2)->nullable();
            $table->decimal('private_quizzes_weight', 5, 2)->nullable();
            $table->decimal('private_exam_weight', 5, 2)->nullable();
        });
    }
};