<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment_settings', function (Blueprint $table) {
            $table->date('class_start_date')->nullable()->after('end_date');
            $table->date('class_end_date')->nullable()->after('class_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('enrollment_settings', function (Blueprint $table) {
            $table->dropColumn(['class_start_date', 'class_end_date']);
        });
    }
};