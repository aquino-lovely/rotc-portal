<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cadets', function (Blueprint $table) {
            $table->enum('cadet_type', ['private', 'advance'])->default('private')->after('campus');
        });
    }

    public function down(): void
    {
        Schema::table('cadets', function (Blueprint $table) {
            $table->dropColumn('cadet_type');
        });
    }
};