<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('quiz_settings', 'term')) {
            Schema::table('quiz_settings', function (Blueprint $table) {
                $table->string('term')->default('midterm');
            });
        }

        if (!Schema::hasColumn('quiz_scores', 'term')) {
            Schema::table('quiz_scores', function (Blueprint $table) {
                $table->string('term')->default('midterm');
            });
        }

        // ---- Dedupe quiz_scores (keep lowest id per group) ----
        $this->dedupe('quiz_scores', ['user_id', 'enrollment_setting_id', 'term', 'quiz_number']);

        // ---- Dedupe quiz_settings (keep lowest id per group) ----
        $this->dedupe('quiz_settings', ['enrollment_setting_id', 'term', 'quiz_number']);

        // quiz_scores: create new index, then drop old one
        $scoreIndexes = collect(DB::select("SHOW INDEX FROM quiz_scores"))
            ->pluck('Key_name')->unique();

        if (!$scoreIndexes->contains('quiz_scores_user_setting_term_quiznum_unique')) {
            Schema::table('quiz_scores', function (Blueprint $table) {
                $table->unique(
                    ['user_id', 'enrollment_setting_id', 'term', 'quiz_number'],
                    'quiz_scores_user_setting_term_quiznum_unique'
                );
            });
        }

        $scoreIndexes = collect(DB::select("SHOW INDEX FROM quiz_scores"))
            ->pluck('Key_name')->unique();

        if ($scoreIndexes->contains('quiz_scores_user_id_enrollment_setting_id_quiz_number_unique')) {
            Schema::table('quiz_scores', function (Blueprint $table) {
                $table->dropUnique('quiz_scores_user_id_enrollment_setting_id_quiz_number_unique');
            });
        }

        // quiz_settings: create new index, then drop old ones
        $settingIndexes = collect(DB::select("SHOW INDEX FROM quiz_settings"))
            ->pluck('Key_name')->unique();

        if (!$settingIndexes->contains('quiz_settings_setting_term_quiznum_unique')) {
            Schema::table('quiz_settings', function (Blueprint $table) {
                $table->unique(
                    ['enrollment_setting_id', 'term', 'quiz_number'],
                    'quiz_settings_setting_term_quiznum_unique'
                );
            });
        }

        $settingIndexes = collect(DB::select("SHOW INDEX FROM quiz_settings"))
            ->pluck('Key_name')->unique();

        foreach ($settingIndexes as $idx) {
            if ($idx !== 'PRIMARY'
                && $idx !== 'quiz_settings_setting_term_quiznum_unique'
                && str_contains($idx, 'unique')) {
                Schema::table('quiz_settings', function (Blueprint $table) use ($idx) {
                    $table->dropUnique($idx);
                });
            }
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

    /**
     * Delete duplicate rows in $table, keeping only the lowest id
     * for each unique combination of $columns.
     */
    private function dedupe(string $table, array $columns): void
    {
        $cols = implode(',', $columns);

        DB::statement("
            DELETE t1 FROM {$table} t1
            INNER JOIN {$table} t2
            ON t1.id > t2.id
            AND " . implode(' AND ', array_map(
                fn($c) => $c === 'term'
                    ? "(t1.{$c} <=> t2.{$c})"
                    : "t1.{$c} = t2.{$c}",
                $columns
            )));
    }
};