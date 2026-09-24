<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove the foreign key first because MySQL is
        // currently using the unique index for it.
        Schema::table('quiz_settings', function (Blueprint $table) {
            $table->dropForeign(['enrollment_setting_id']);
        });

        // Now remove the old unique constraint.
        Schema::table('quiz_settings', function (Blueprint $table) {
            $table->dropUnique(
                'quiz_settings_enrollment_setting_id_quiz_number_unique'
            );
        });

        // Add the foreign key back.
        Schema::table('quiz_settings', function (Blueprint $table) {
            $table->foreign('enrollment_setting_id')
                ->references('id')
                ->on('enrollment_settings')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the foreign key first.
        Schema::table('quiz_settings', function (Blueprint $table) {
            $table->dropForeign(['enrollment_setting_id']);
        });

        // Restore the unique constraint.
        Schema::table('quiz_settings', function (Blueprint $table) {
            $table->unique(
                ['enrollment_setting_id', 'quiz_number'],
                'quiz_settings_enrollment_setting_id_quiz_number_unique'
            );
        });

        // Restore the foreign key.
        Schema::table('quiz_settings', function (Blueprint $table) {
            $table->foreign('enrollment_setting_id')
                ->references('id')
                ->on('enrollment_settings')
                ->onDelete('cascade');
        });
    }
};