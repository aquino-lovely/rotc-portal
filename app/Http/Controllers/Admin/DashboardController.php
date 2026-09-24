<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EnrollmentSetting;
use App\Models\GradeRecord;
use App\Models\QuizScore;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingCount  = User::where('role', 'cadet')->where('status', 'pending')->count();
        $approvedCount = User::where('role', 'cadet')->where('status', 'approved')->count();

        // Determine which semester to show: active, or most recent past
        $setting = EnrollmentSetting::getActive();
        $isActivePeriod = (bool) $setting;

        if (!$setting) {
            $setting = EnrollmentSetting::latest()->first();
        }

        $atRiskCadets = collect();
        $passCount = 0;
        $failCount = 0;
        $classAverageHistory = collect();
        $attendanceWeekly = collect();
        $quizzesWeekly = collect();

        if ($setting) {
            // Pass/Fail counts for current shown semester
            $gradeRecords = GradeRecord::where('enrollment_setting_id', $setting->id)->get();
            $passCount = $gradeRecords->where('status', 'pass')->count();
            $failCount = $gradeRecords->where('status', 'fail')->count();

            // Class average history — across ALL semesters
            $classAverageHistory = GradeRecord::selectRaw('enrollment_setting_id, AVG(final_score) as avg_score')
                ->groupBy('enrollment_setting_id')
                ->with('setting')
                ->get()
                ->map(function ($row) {
                    return [
                        'label' => $row->setting ? $row->setting->semester . ' ' . $row->setting->school_year : 'Unknown',
                        'value' => round($row->avg_score, 2),
                    ];
                });

            // At-Risk cadets: quiz avg < 75 OR attendance < 80% OR PFT failed
            $cadets = User::where('role', 'cadet')->where('status', 'approved')->with('cadet')->get();

            foreach ($cadets as $cadet) {
                $quizAvg = QuizScore::computeAverage($cadet->id, $setting->id);

                $totalSessions = AttendanceSession::where('enrollment_setting_id', $setting->id)->count();
                $presentCount  = AttendanceRecord::whereHas('session', fn($q) => $q->where('enrollment_setting_id', $setting->id))
                    ->where('user_id', $cadet->id)
                    ->whereIn('status', ['present', 'late'])
                    ->count();
                $attendanceRate = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100, 1) : 100;

                $reasons = [];
                if ($quizAvg > 0 && $quizAvg < 75) $reasons[] = 'Low quiz average';
                if ($totalSessions > 0 && $attendanceRate < 80) $reasons[] = 'Low attendance';

                if (!empty($reasons)) {
                    $atRiskCadets->push([
                        'name'       => $cadet->cadet->last_name . ', ' . $cadet->cadet->first_name,
                        'quiz_avg'   => $quizAvg,
                        'attendance' => $attendanceRate,
                        'reasons'    => implode(', ', $reasons),
                    ]);
                }
            }

            // Weekly attendance overview (Present/Absent/Late/Excused per session date)
            $sessions = AttendanceSession::where('enrollment_setting_id', $setting->id)
                ->orderBy('session_date')
                ->get();

            foreach ($sessions as $index => $session) {
                $records = AttendanceRecord::where('attendance_session_id', $session->id)->get();
                $attendanceWeekly->push([
                    'label'   => 'Week ' . ($index + 1),
                    'present' => $records->where('status', 'present')->count(),
                    'absent'  => $records->where('status', 'absent')->count(),
                    'late'    => $records->where('status', 'late')->count(),
                    'excused' => $records->where('status', 'excused')->count(),
                ]);
            }

            // Weekly quiz average (using quiz_number as proxy for week)
            $quizNumbers = QuizScore::where('enrollment_setting_id', $setting->id)
                ->distinct()
                ->pluck('quiz_number')
                ->sort();

            foreach ($quizNumbers as $qn) {
                $scores = QuizScore::where('enrollment_setting_id', $setting->id)
                    ->where('quiz_number', $qn)
                    ->get();

                $avgPercent = $scores->count() > 0
                    ? round($scores->avg(fn($s) => $s->max_score > 0 ? ($s->score / $s->max_score) * 100 : 0), 1)
                    : 0;

                $quizzesWeekly->push([
                    'label' => 'Quiz ' . $qn,
                    'value' => $avgPercent,
                ]);
            }
        }

        return view('admin.dashboard', compact(
            'pendingCount', 'approvedCount', 'setting', 'isActivePeriod',
            'passCount', 'failCount', 'classAverageHistory',
            'atRiskCadets', 'attendanceWeekly', 'quizzesWeekly'
        ));
    }
}