<?php

namespace App\Http\Controllers\Cadet;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\SemestralGrade;
use Illuminate\Http\Request;
use App\Models\GradeRecord;


class GradeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

       $enrollments = Enrollment::where('user_id', $user->id)
    ->where('status', 'finalized')
    ->with(['setting'])
    ->get()
    ->filter(fn ($e) => $e->setting !== null)
    ->sortBy(fn ($e) => $e->setting->school_year . $e->setting->semester);

     $semestralGrades = SemestralGrade::where('user_id', $user->id)->get()->keyBy('enrollment_setting_id');

        $militaryScienceGroups = [];

        foreach ($enrollments as $enrollment) {
            $setting = $enrollment->setting;
            $sem     = $semestralGrades[$setting->id] ?? null;

            $militaryScienceGroups[] = [
                'ms_label'    => 'Military Science ' . $enrollment->subject_level . ($enrollment->is_retake ? ' (Retake)' : ''),
                'course_code' => $enrollment->subject_level == 1 ? 'NSTP 101' : 'NSTP 102',
                'subject'     => 'NSTP-ROTC',
                'grade'       => $sem->grade ?? null,
                'remarks'     => GradeRecord::computeRemarks($sem->percentile ?? 0) ?? null,
                'semester'    => $setting->semester,
                'school_year' => $setting->school_year,
            ];
        }

        return view('cadets.grades', compact('militaryScienceGroups'));
    }
}