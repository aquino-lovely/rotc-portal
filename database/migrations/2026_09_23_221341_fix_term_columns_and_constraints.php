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
        if (! Schema::hasColumn('quiz_scores', 'term')) {
            Schema::table('quiz_scores', function (Blueprint $table) {
                $table->string('term')->default('midterm');
            });
        }

        // Fix quiz_scores unique constraint to include term
        $scoreIndexes = collect(DB::select("SHOW INDEX FROM quiz_scores"))
            ->pluck('Key_name')->unique();

        // Add the new unique first so the foreign keys always have an index
        if (! $scoreIndexes->contains('quiz_scores_user_setting_term_quiznum_unique')) {
            Schema::table('quiz_scores', function (Blueprint $table) {
                $table->unique(
                    ['user_id', 'enrollment_setting_id', 'term', 'quiz_number'],
                    'quiz_scores_user_setting_term_quiznum_unique'
                );
            });
        }

        if ($scoreIndexes->contains('quiz_scores_user_id_enrollment_setting_id_quiz_number_unique')) {
            Schema::table('quiz_scores', function (Blueprint $table) {
                $table->dropUnique('quiz_scores_user_id_enrollment_setting_id_quiz_number_unique');
            });
        }

        // Fix quiz_settings unique constraint to include term
        $target = 'quiz_settings_setting_term_quiznum_unique';

        // 1. Give the foreign key its own plain index, so no unique index is "needed" by it
        if (! Schema::hasIndex('quiz_settings', 'quiz_settings_enrollment_setting_id_index')) {
            Schema::table('quiz_settings', function (Blueprint $table) {
                $table->index('enrollment_setting_id', 'quiz_settings_enrollment_setting_id_index');
            });
        }

        // 2. Drop any OTHER unique indexes (never the target one)
        $settingIndexes = collect(DB::select("SHOW INDEX FROM quiz_settings"))
            ->pluck('Key_name')->unique();

        foreach ($settingIndexes as $idx) {
            if ($idx !== 'PRIMARY' && $idx !== $target && str_contains($idx, 'unique')) {
                Schema::table('quiz_settings', function (Blueprint $table) use ($idx) {
                    $table->dropUnique($idx);
                });
            }
        }

        // 3. Add the target unique if it is missing
        if (! Schema::hasIndex('quiz_settings', $target)) {
            Schema::table('quiz_settings', function (Blueprint $table) use ($target) {
                $table->unique(['enrollment_setting_id', 'term', 'quiz_number'], $target);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('quiz_scores', 'quiz_scores_user_setting_term_quiznum_unique')) {
            Schema::table('quiz_scores', function (Blueprint $table) {
                $table->dropUnique('quiz_scores_user_setting_term_quiznum_unique');
            });
        }

        if (Schema::hasIndex('quiz_settings', 'quiz_settings_setting_term_quiznum_unique')) {
            Schema::table('quiz_settings', function (Blueprint $table) {
                $table->dropUnique('quiz_settings_setting_term_quiznum_unique');
            });
        }
    }
};