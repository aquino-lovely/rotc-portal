<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quiz_settings', function (Blueprint $table) {
            $table->enum('term', ['midterm', 'final'])->default('midterm')->after('enrollment_setting_id');
        });

        Schema::table('quiz_scores', function (Blueprint $table) {
            $table->enum('term', ['midterm', 'final'])->default('midterm')->after('enrollment_setting_id');
        });
    }

    public function down()
    {
        Schema::table('quiz_settings', function (Blueprint $table) {
            $table->dropColumn('term');
        });

        Schema::table('quiz_scores', function (Blueprint $table) {
            $table->dropColumn('term');
        });
    }
};