<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cadet_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('surname');
            $table->string('first_name');
            $table->string('middle_initial')->nullable();
            $table->string('contact_no');
            $table->string('relationship');
            $table->string('barangay');
            $table->string('city_municipality');
            $table->string('province');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};