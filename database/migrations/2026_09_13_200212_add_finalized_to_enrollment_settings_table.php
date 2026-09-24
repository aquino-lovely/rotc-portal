<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment_settings', function (Blueprint $table) {
            $table->boolean('finalized')->default(false)->after('class_end_date');
            $table->timestamp('finalized_at')->nullable()->after('finalized');
            $table->foreignId('finalized_by')->nullable()->constrained('users')->after('finalized_at');
        });
    }

    public function down(): void
    {
        Schema::table('enrollment_settings', function (Blueprint $table) {
            $table->dropForeign(['finalized_by']);
            $table->dropColumn(['finalized', 'finalized_at', 'finalized_by']);
        });
    }
};