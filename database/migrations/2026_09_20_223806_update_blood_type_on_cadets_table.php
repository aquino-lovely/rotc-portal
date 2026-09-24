<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $allowed = ['Unknown','A+','A-','B+','B-','AB+','AB-','O+','O-'];

        // 1. Loosen the column so any value can be written
        DB::statement("ALTER TABLE cadets MODIFY blood_type VARCHAR(20) NULL");

        // 2. Turn missing or invalid values into 'Unknown'
        DB::table('cadets')
            ->where(function ($q) use ($allowed) {
                $q->whereNull('blood_type')
                  ->orWhereNotIn('blood_type', $allowed);
            })
            ->update(['blood_type' => 'Unknown']);

        // 3. Convert to the enum with the default
        DB::statement("ALTER TABLE cadets MODIFY blood_type ENUM('Unknown','A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL DEFAULT 'Unknown'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE cadets MODIFY blood_type VARCHAR(20) NULL");
    }
};