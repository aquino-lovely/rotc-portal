<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cadets', function (Blueprint $table) {
            $table->string('suffix', 10)->nullable()->after('last_name');
        });

        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->string('suffix', 10)->nullable()->after('first_name');
        });
    }

    public function down(): void
    {
        Schema::table('cadets', function (Blueprint $table) {
            $table->dropColumn('suffix');
        });

        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn('suffix');
        });
    }
};