<?php

namespace App\Http\Controllers\Cadet;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\EnrollmentSetting;
use Illuminate\Http\Request;
use App\Models\GradeRecord;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('cadet');
        $setting = EnrollmentSetting::getActive();

        $enrollment = null;
        $eligibility = null;

        if ($setting) {
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('enrollment_setting_id', $setting->id)
                ->first();

            if (!$enrollment) {
                $eligibility = $this->resolveEligibility($user, $setting);
            }
        }

        return view('cadets.enrollment', compact('user', 'setting', 'enrollment', 'eligibility'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $setting = EnrollmentSetting::getActive();

        if (!$setting) {
            return back()->with('error', 'There is no active enrollment period right now.');
        }

        // Prevent enrolling twice within the same term/setting
        $existing = Enrollment::where('user_id', $user->id)
            ->where('enrollment_setting_id', $setting->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'You already have an enrollment for this term.');
        }

        $eligibility = $this->resolveEligibility($user, $setting);

        if ($eligibility['blocked']) {
            return back()->with('error', $eligibility['reason']);
        }

        Enrollment::create([
            'user_id'               => $user->id,
            'enrollment_setting_id' => $setting->id,
            'status'                => 'finalized',
            'subject_level'         => $eligibility['subject_level'],
            'is_retake'             => $eligibility['is_retake'],
            'finalized_at'          => now(),
        ]);

        return redirect()->route('cadet.enrollment')->with('success', $eligibility['is_retake']
            ? 'Enrollment submitted. You are retaking ROTC 1 this semester.'
            : 'Enrollment submitted successfully.');
    }

    private function resolveEligibility($user, $setting): array
    {
        return Enrollment::resolveEligibilityFor($user, $setting);
    }
}