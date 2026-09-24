<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('enrollments', function (Blueprint $table) {
        $table->boolean('enrolled_manually')->default(false)->after('status');
        $table->foreignId('enrolled_by')->nullable()->constrained('users')->after('enrolled_manually');
    });
}

public function down(): void
{
    Schema::table('enrollments', function (Blueprint $table) {
        $table->dropForeign(['enrolled_by']);
        $table->dropColumn(['enrolled_manually', 'enrolled_by']);
    });
}
};
