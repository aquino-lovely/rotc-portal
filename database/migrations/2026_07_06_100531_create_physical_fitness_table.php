<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_fitness', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_setting_id')->constrained()->cascadeOnDelete();
            $table->decimal('run_time', 5, 2)->nullable();   // in minutes
            $table->integer('push_ups')->nullable();
            $table->integer('sit_ups')->nullable();
            $table->boolean('run_pass')->nullable();
            $table->boolean('push_ups_pass')->nullable();
            $table->boolean('sit_ups_pass')->nullable();
            $table->decimal('overall', 5, 2)->nullable();    // computed
            $table->timestamps();

            $table->unique(['user_id', 'enrollment_setting_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_fitness');
    }
};