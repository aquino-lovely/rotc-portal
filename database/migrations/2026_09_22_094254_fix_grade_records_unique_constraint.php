<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grade_records', function (Blueprint $table) {
            $table->unique(['user_id', 'enrollment_setting_id', 'term'], 'grade_records_user_setting_term_unique');
        });

        Schema::table('grade_records', function (Blueprint $table) {
            $table->dropUnique('grade_records_user_id_enrollment_setting_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('grade_records', function (Blueprint $table) {
            $table->unique(['user_id', 'enrollment_setting_id']);
        });

        Schema::table('grade_records', function (Blueprint $table) {
            $table->dropUnique('grade_records_user_setting_term_unique');
        });
    }
};