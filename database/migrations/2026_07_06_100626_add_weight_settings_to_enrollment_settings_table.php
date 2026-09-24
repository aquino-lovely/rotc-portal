<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment_settings', function (Blueprint $table) {
            // Advance cadet weights
            $table->decimal('advance_pft_weight', 5, 2)->default(20)->after('time');
            $table->decimal('advance_attendance_weight', 5, 2)->default(20)->after('advance_pft_weight');
            $table->decimal('advance_quizzes_weight', 5, 2)->default(30)->after('advance_attendance_weight');
            $table->decimal('advance_exam_weight', 5, 2)->default(30)->after('advance_quizzes_weight');

            // Private cadet weights (no PFT)
            $table->decimal('private_attendance_weight', 5, 2)->default(25)->after('advance_exam_weight');
            $table->decimal('private_quizzes_weight', 5, 2)->default(40)->after('private_attendance_weight');
            $table->decimal('private_exam_weight', 5, 2)->default(35)->after('private_quizzes_weight');

        });
    }

    public function down(): void
    {
        Schema::table('enrollment_settings', function (Blueprint $table) {
            $table->dropColumn([
                'advance_pft_weight', 'advance_attendance_weight',
                'advance_quizzes_weight', 'advance_exam_weight',
                'private_attendance_weight', 'private_quizzes_weight',
                'private_exam_weight',
            ]);
        });
    }
};