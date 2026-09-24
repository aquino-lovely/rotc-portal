<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $table = 'grade_records';

    public function up(): void
    {
        if (! Schema::hasColumn($this->table, 'field_activities_score')) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->decimal('field_activities_score', 5, 2)->nullable()->after('attendance_score');
            });
        }
    }

    public function down(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn('field_activities_score');
        });
    }
};