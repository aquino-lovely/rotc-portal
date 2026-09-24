<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('cadets', 'cor_document_path')) {
            Schema::table('cadets', function (Blueprint $table) {
                $table->string('cor_document_path')
                    ->nullable()
                    ->after('enrollee_type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cadets', 'cor_document_path')) {
            Schema::table('cadets', function (Blueprint $table) {
                $table->dropColumn('cor_document_path');
            });
        }
    }
};