<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $old = 'quiz_scores_user_id_enrollment_setting_id_quiz_number_unique';
    private string $new = 'quiz_scores_user_setting_term_quiznum_unique';

    public function up(): void
    {
        // Add the new unique first, so the foreign keys always have an index to use
        if (! Schema::hasIndex('quiz_scores', $this->new)) {
            Schema::table('quiz_scores', function (Blueprint $table) {
                $table->unique(['user_id', 'enrollment_setting_id', 'term', 'quiz_number'], $this->new);
            });
        }

        // Drop the old one only if it still exists
        if (Schema::hasIndex('quiz_scores', $this->old)) {
            Schema::table('quiz_scores', function (Blueprint $table) {
                $table->dropUnique($this->old);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasIndex('quiz_scores', $this->old)) {
            Schema::table('quiz_scores', function (Blueprint $table) {
                $table->unique(['user_id', 'enrollment_setting_id', 'quiz_number'], $this->old);
            });
        }

        if (Schema::hasIndex('quiz_scores', $this->new)) {
            Schema::table('quiz_scores', function (Blueprint $table) {
                $table->dropUnique($this->new);
            });
        }
    }
};