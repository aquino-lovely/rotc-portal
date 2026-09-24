<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cadets', function (Blueprint $table) {
            $table->string('beneficiary_name')->nullable();
            $table->string('beneficiary_contact_no', 20)->nullable();
            $table->string('beneficiary_barangay')->nullable();
            $table->string('beneficiary_city_municipality')->nullable();
            $table->string('beneficiary_province')->nullable();
            $table->string('beneficiary_relationship')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cadets', function (Blueprint $table) {
            $table->dropColumn([
                'beneficiary_name',
                'beneficiary_contact_no',
                'beneficiary_barangay',
                'beneficiary_city_municipality',
                'beneficiary_province',
                'beneficiary_relationship',
            ]);
        });
    }
};