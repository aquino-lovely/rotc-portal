<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add term to quiz_scores only if missing
        if (!Schema::hasColumn('quiz_scores', 'term')) {
            Schema::table('quiz_scores', function (Blueprint $table) {
                $table->string('term')->default('midterm');
            });
        }

        // Fix quiz_scores unique constraint to include term
        $scoreIndexes = collect(DB::select("SHOW INDEX FROM quiz_scores"))
            ->pluck('Key_name')->unique();

        if ($scoreIndexes->contains('quiz_scores_user_id_enrollment_setting_id_quiz_number_unique')) {
            Schema::table('quiz_scores', function (Blueprint $table) {
                $table->dropUnique('quiz_scores_user_id_enrollment_setting_id_quiz_number_unique');
            });
        }

        if (!$scoreIndexes->contains('quiz_scores_user_setting_term_quiznum_unique')) {
            Schema::table('quiz_scores', function (Blueprint $table) {
                $table->unique(
                    ['user_id', 'enrollment_setting_id', 'term', 'quiz_number'],
                    'quiz_scores_user_setting_term_quiznum_unique'
                );
            });
        }

        // Fix quiz_settings unique constraint to include term (if one exists)
        $settingIndexes = collect(DB::select("SHOW INDEX FROM quiz_settings"))
            ->pluck('Key_name')->unique();

        foreach ($settingIndexes as $idx) {
            if ($idx !== 'PRIMARY' && str_contains($idx, 'unique')) {
                Schema::table('quiz_settings', function (Blueprint $table) use ($idx) {
                    $table->dropUnique($idx);
                });
            }
        }

        if (!collect(DB::select("SHOW INDEX FROM quiz_settings"))->pluck('Key_name')
                ->contains('quiz_settings_setting_term_quiznum_unique')) {
            Schema::table('quiz_settings', function (Blueprint $table) {
                $table->unique(
                    ['enrollment_setting_id', 'term', 'quiz_number'],
                    'quiz_settings_setting_term_quiznum_unique'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::table('quiz_scores', function (Blueprint $table) {
            $table->dropUnique('quiz_scores_user_setting_term_quiznum_unique');
        });
        Schema::table('quiz_settings', function (Blueprint $table) {
            $table->dropUnique('quiz_settings_setting_term_quiznum_unique');
        });
    }
};