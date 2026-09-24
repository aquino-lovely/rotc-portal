<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grade_records', function (Blueprint $table) {
            $table->foreignId('enrollment_id')
                ->nullable() // nullable for now so existing rows don't break; tighten after backfill
                ->after('id')
                ->constrained('enrollments')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('grade_records', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);
            $table->dropColumn('enrollment_id');
        });
    }
};