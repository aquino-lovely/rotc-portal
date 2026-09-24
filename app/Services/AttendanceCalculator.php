<?php

namespace App\Services;

use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;

class AttendanceCalculator
{
    private const CREDIT = [
        'present' => 1.0,
        'excused' => 1.0,
        'late'    => 2 / 3,
        'absent'  => 0.0,
    ];

    /**
     * Attendance score/max for one cadet, one term, one enrollment period.
     * Returns ['score' => float, 'max' => int].
     */
    public static function forCadet(int $userId, int $enrollmentSettingId, string $term): array
    {
        $sessionIds = AttendanceSession::where('enrollment_setting_id', $enrollmentSettingId)
            ->where('term', $term)
            ->pluck('id');

        $max = $sessionIds->count();

        if ($max === 0) {
            return ['score' => null, 'max' => null];
        }

        $records = AttendanceRecord::where('user_id', $userId)
            ->whereIn('attendance_session_id', $sessionIds)
            ->get();

        $score = $records->sum(fn ($r) => self::CREDIT[$r->status] ?? 0.0);

        return ['score' => round($score, 2), 'max' => $max];
    }
}