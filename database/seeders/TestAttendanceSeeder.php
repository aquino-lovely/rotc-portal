<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $settingId = 8;                           // the setting you are testing with
        $saturdays = ['2026-09-26', '2026-10-03']; // past Saturdays inside its class dates

        $userIds = Enrollment::where('enrollment_setting_id', $settingId)
            ->where('status', 'finalized')
            ->pluck('user_id');

        // Mostly present, some absent, late and excused
        $pool = ['present', 'present', 'present', 'present', 'absent', 'late', 'excused'];

        foreach ($saturdays as $date) {
            $session = AttendanceSession::where('enrollment_setting_id', $settingId)
                ->whereDate('session_date', $date)
                ->first();

            if (! $session) {
                $session = AttendanceSession::forceCreate([
                    'enrollment_setting_id' => $settingId,
                    'session_date'          => $date,
                    'term'                  => 'midterm',
                    'cutoff_time'           => '08:00:00',
                    'qr_token'              => Str::random(32),
                    'qr_expires_at'         => now()->subDays(1),
                    'is_active'             => false,   // so it won't interfere with the QR page
                ]);
            }

            foreach ($userIds as $uid) {
                if (AttendanceRecord::where('attendance_session_id', $session->id)->where('user_id', $uid)->exists()) {
                    continue;
                }

                $status = $pool[array_rand($pool)];

                AttendanceRecord::forceCreate([
                    'attendance_session_id' => $session->id,
                    'user_id'               => $uid,
                    'status'                => $status,
                    'time_in'               => in_array($status, ['present', 'late']) ? ($status === 'late' ? '08:20:00' : '07:45:00') : null,
                    'excuse_reason'         => $status === 'excused' ? 'Test excuse' : null,
                ]);
            }
        }
    }
}