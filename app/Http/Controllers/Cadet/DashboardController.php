<?php

namespace App\Http\Controllers\Cadet;

use App\Http\Controllers\Controller;
use App\Models\EnrollmentSetting;
use App\Models\PhysicalFitness;
use App\Models\QuizScore;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user()->load('cadet');
        $cadet = $user->cadet;

        $setting = EnrollmentSetting::getActive();

        $pftScore = null;
        $quizAvg  = 0;
        $attendanceRate = null;
        $atRiskReasons = [];

        if ($setting) {
            // PFT — only for advance cadets
            if ($cadet && $cadet->cadet_type === 'advance') {
                $pft = PhysicalFitness::where('user_id', $user->id)
                    ->where('enrollment_setting_id', $setting->id)
                    ->first();
                $pftScore = $pft?->overall;

                if ($pftScore !== null && $pftScore < 66.66) {
                    $atRiskReasons[] = 'Low PFT score';
                }
            }

            // Quiz average
            $quizAvg = QuizScore::computeAverage($user->id, $setting->id);
            if ($quizAvg > 0 && $quizAvg < 75) {
                $atRiskReasons[] = 'Low quiz average';
            }

            // Attendance rate
            $totalSessions = AttendanceSession::where('enrollment_setting_id', $setting->id)->count();
            $presentCount  = AttendanceRecord::whereHas('session', fn($q) => $q->where('enrollment_setting_id', $setting->id))
                ->where('user_id', $user->id)
                ->whereIn('status', ['present', 'late'])
                ->count();

            $attendanceRate = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100, 1) : null;

            if ($attendanceRate !== null && $attendanceRate < 80) {
                $atRiskReasons[] = 'Low attendance';
            }
        }

        return view('cadets.dashboard', compact(
            'user', 'cadet', 'setting', 'pftScore', 'quizAvg',
            'attendanceRate', 'atRiskReasons'
        ));
    }
}