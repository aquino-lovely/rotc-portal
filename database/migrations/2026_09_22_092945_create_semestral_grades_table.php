<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semestral_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('enrollment_setting_id')->constrained();
            $table->unsignedTinyInteger('percentile')->nullable();
            $table->decimal('grade', 3, 2)->nullable();
            $table->enum('status', ['pass', 'fail'])->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'enrollment_setting_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semestral_grades');
    }
};