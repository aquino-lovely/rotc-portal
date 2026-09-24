<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('grade_records', function (Blueprint $table) {
        $table->enum('term', ['midterm', 'final'])->default('midterm');
    });
}

public function down()
{
    Schema::table('grade_records', function (Blueprint $table) {
        $table->dropColumn('term');
    });
}
};
