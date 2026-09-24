<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $table = 'enrollment_settings';

    /** column => default. Only columns that don't exist yet are added. */
    private array $columns = [
        'quizzes_weight'          => 0,
        'field_activities_weight' => 30,
        'attendance_weight'       => 40,
        'midterm_exam_weight'     => 30,
        'first_term_weight'       => 40,  // Midterm share of semestral grade
        'second_term_weight'      => 60,  // Final share of semestral grade
    ];

    public function up(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            foreach ($this->columns as $name => $default) {
                if (! Schema::hasColumn($this->table, $name)) {
                    $table->decimal($name, 5, 2)->default($default);
                }
            }
        });
    }

    public function down(): void
    {
        // Only the semestral columns are dropped, in case the other four already existed.
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn(['first_term_weight', 'second_term_weight']);
        });
    }
};