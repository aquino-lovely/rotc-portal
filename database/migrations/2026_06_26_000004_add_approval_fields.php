<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('must_change_password')->default(true)->after('status');
        });

        Schema::table('cadets', function (Blueprint $table) {
            $table->string('rank')->default('Private')->after('year_section');
            $table->string('company')->nullable()->after('rank'); // assigned at approval
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('must_change_password');
        });

        Schema::table('cadets', function (Blueprint $table) {
            $table->dropColumn(['rank', 'company']);
        });
    }
};