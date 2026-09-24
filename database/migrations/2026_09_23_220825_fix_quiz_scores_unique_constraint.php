<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_scores', function (Blueprint $table) {
            // Drop the old constraint that's missing `term`
            $table->dropUnique('quiz_scores_user_id_enrollment_setting_id_quiz_number_unique');

            // Recreate it correctly, including term
            $table->unique(
                ['user_id', 'enrollment_setting_id', 'term', 'quiz_number'],
                'quiz_scores_user_setting_term_quiznum_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('quiz_scores', function (Blueprint $table) {
            $table->dropUnique('quiz_scores_user_setting_term_quiznum_unique');

            $table->unique(
                ['user_id', 'enrollment_setting_id', 'quiz_number'],
                'quiz_scores_user_id_enrollment_setting_id_quiz_number_unique'
            );
        });
    }
};