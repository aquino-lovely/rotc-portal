<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\EnrollmentSetting;
use App\Models\User;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $setting = EnrollmentSetting::getActive();

        $query = Enrollment::with(['user.cadet'])
            ->when($setting, fn($q) => $q->where('enrollment_setting_id', $setting->id));

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->whereHas('cadet', function ($c) use ($search) {
                    $c->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('student_number', 'like', "%{$search}%");
                });
            });
        }

        $enrollments = $query->latest()->paginate(15);

        return view('admin.enrollment.index', compact('enrollments', 'setting'));
    }

    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['user.cadet', 'setting']);
        return view('admin.enrollment.show', compact('enrollment'));
    }

   public function reject(Request $request, Enrollment $enrollment)
   {
    if ($enrollment->status !== 'submitted') {
        return back()->with('error', 'Only submitted enrollments can be rejected.');
    }
    $request->validate([
        'reasons'       => ['required_without:custom_reason', 'array'],
        'reasons.*'     => ['string'],
        'custom_reason' => ['nullable', 'string', 'max:500'],
    ]);

    $reasons = $request->input('reasons', []);

    if ($request->filled('custom_reason')) {
        $reasons[] = $request->custom_reason;
    }

    if (empty($reasons)) {
        return back()->with('error', 'Please select or specify at least one reason.');
    }

    $enrollment->update([
        'status'           => 'rejected',
        'rejection_reason' => implode('; ', $reasons),
    ]);

    return back()->with('success', 'Enrollment rejected.');
}

    /**
     * Settings — set active semester.
     *
     * School Year + Semester are auto-suggested based on the real
     * academic calendar (Aug-Dec = First Sem, Jan-May = Second Sem),
     * not on whatever row happened to be created last. This mirrors
     * how a registrar's office works: the term name is structural
     * and predictable, so the system can safely compute it.
     *
     * Start/End dates, Unit, Time, and grade weights remain fully
     * manual — those come from official instructions each term and
     * should never be guessed by the system.
     */
    public function settings()
{
    $setting = EnrollmentSetting::getActive();

    $today = now();
    $month = $today->month;

    if ($month >= 8) {
        $suggestedSchoolYear = $today->year . '-' . ($today->year + 1);
        $suggestedSemester   = 'First Semester';
    } elseif ($month >= 1 && $month <= 5) {
        $suggestedSchoolYear = ($today->year - 1) . '-' . $today->year;
        $suggestedSemester   = 'Second Semester';
    } else {
        $suggestedSchoolYear = $today->year . '-' . ($today->year + 1);
        $suggestedSemester   = 'First Semester';
    }

    // Keep advancing until we land on a term that doesn't exist yet
    while (
        EnrollmentSetting::where('school_year', $suggestedSchoolYear)
            ->where('semester', $suggestedSemester)
            ->exists()
    ) {
        if ($suggestedSemester === 'First Semester') {
            $suggestedSemester = 'Second Semester';
        } else {
            [$startYear, $endYear] = explode('-', $suggestedSchoolYear);
            $suggestedSchoolYear = ((int)$startYear + 1) . '-' . ((int)$endYear + 1);
            $suggestedSemester   = 'First Semester';
        }
    }

    return view('admin.enrollment.settings', compact('setting', 'suggestedSchoolYear', 'suggestedSemester'));
}

    public function saveSettings(Request $request)
    {
        $validated = $request->validate([
            'school_year' => ['required', 'string', 'max:20', 'regex:/^\d{4}-\d{4}$/'],
            'semester'    => 'required|in:First Semester,Second Semester',
            'unit'        => 'nullable|string|max:50',
            'time'        => 'nullable|string|max:50',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'class_start_date' => 'nullable|date',
            'class_end_date'   => 'nullable|date|after_or_equal:class_start_date',
            'is_open'     => 'sometimes|boolean',

                        // old rules: the form no longer sends these
            'advance_pft_weight'        => 'sometimes|numeric|min:0|max:100',
            'advance_attendance_weight' => 'sometimes|numeric|min:0|max:100',
            'advance_quizzes_weight'    => 'sometimes|numeric|min:0|max:100',
            'advance_exam_weight'       => 'sometimes|numeric|min:0|max:100',

            'private_attendance_weight' => 'sometimes|numeric|min:0|max:100',
            'private_quizzes_weight'    => 'sometimes|numeric|min:0|max:100',
            'private_exam_weight'       => 'sometimes|numeric|min:0|max:100',

            // new rules
            'quizzes_weight'          => 'required|numeric|min:0|max:100',
            'field_activities_weight' => 'required|numeric|min:0|max:100',
            'attendance_weight'       => 'required|numeric|min:0|max:100',
            'midterm_exam_weight'     => 'required|numeric|min:0|max:100',
            'first_term_weight'       => 'required|numeric|min:0|max:100',
            'second_term_weight'      => 'required|numeric|min:0|max:100',
        ]);

                $termTotal = $validated['quizzes_weight']
            + $validated['field_activities_weight']
            + $validated['attendance_weight']
            + $validated['midterm_exam_weight'];

        $semestralTotal = $validated['first_term_weight'] + $validated['second_term_weight'];

        if (abs($termTotal - 100) > 0.001) {
            return back()->withInput()->withErrors([
                'quizzes_weight' => "Cadet grade weights must total 100% (currently {$termTotal}%).",
            ]);
        }

        if (abs($semestralTotal - 100) > 0.001) {
            return back()->withInput()->withErrors([
                'first_term_weight' => "Semestral weights must total 100% (currently {$semestralTotal}%).",
            ]);
        }

        $validated['is_open'] = $request->boolean('is_open');

        // Close every previously open period — only one can be active at a time
        EnrollmentSetting::where('is_open', true)->update(['is_open' => false]);

        // Always create a NEW period instead of overwriting an old row
        EnrollmentSetting::create($validated);

        return redirect()
            ->route('admin.enrollment.settings')
            ->with('success', 'Enrollment settings saved.');
    }
    public function updateSetting(Request $request, EnrollmentSetting $setting)
    {
        $validated = $request->validate([
            'school_year' => ['required', 'string', 'max:20', 'regex:/^\d{4}-\d{4}$/'],
            'semester'    => 'required|in:First Semester,Second Semester',
            'unit'        => 'nullable|string|max:50',
            'time'        => 'nullable|string|max:50',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'class_start_date' => 'nullable|date',
            'class_end_date'   => 'nullable|date|after_or_equal:class_start_date',
            'is_open'     => 'sometimes|boolean',

            // new rules
            'quizzes_weight'          => 'required|numeric|min:0|max:100',
            'field_activities_weight' => 'required|numeric|min:0|max:100',
            'attendance_weight'       => 'required|numeric|min:0|max:100',
            'midterm_exam_weight'     => 'required|numeric|min:0|max:100',
            'first_term_weight'       => 'required|numeric|min:0|max:100',
            'second_term_weight'      => 'required|numeric|min:0|max:100',
        ]);

        $termTotal = $validated['quizzes_weight']
            + $validated['field_activities_weight']
            + $validated['attendance_weight']
            + $validated['midterm_exam_weight'];

        $semestralTotal = $validated['first_term_weight'] + $validated['second_term_weight'];

        if (abs($termTotal - 100) > 0.001) {
            return back()->withInput()->withErrors([
                'quizzes_weight' => "Cadet grade weights must total 100% (currently {$termTotal}%).",
            ]);
        }

        if (abs($semestralTotal - 100) > 0.001) {
            return back()->withInput()->withErrors([
                'first_term_weight' => "Semestral weights must total 100% (currently {$semestralTotal}%).",
            ]);
        }

        $validated['is_open'] = $request->boolean('is_open');

        $setting->update($validated); // edits THIS row in place, no new row created

        return redirect()
            ->route('admin.enrollment.settings')
            ->with('success', 'Enrollment period corrected.');
    }

   public function enrollManually(Request $request, User $user)
{
    $setting = EnrollmentSetting::getActive();

    if (!$setting) {
        return back()->with('error', 'No active enrollment period to enroll into.');
    }

    $existing = Enrollment::where('user_id', $user->id)
        ->where('enrollment_setting_id', $setting->id)
        ->first();

    if ($existing) {
        return back()->with('error', 'This cadet is already enrolled for this period.');
    }

    Enrollment::create([
    'user_id'               => $user->id,
    'enrollment_setting_id' => $setting->id,
    'status'                => 'finalized',
    'enrolled_manually'     => true,
    'enrolled_by'           => $request->user()->id,
    'finalized_at'          => now(),
]);

    return back()->with('success', "Manually enrolled {$user->cadet->first_name} {$user->cadet->last_name}.");
}
    
    public function finalizeTerm(Request $request, EnrollmentSetting $setting)
{
    $setting->update([
        'finalized'    => true,
        'finalized_at' => now(),
        'finalized_by' => $request->user()->id,
    ]);

    return back()->with('success', 'This term has been finalized. Attendance and grades are now locked.');
}

public function unfinalizeTerm(Request $request, EnrollmentSetting $setting)
{
    $setting->update([
        'finalized'    => false,
        'finalized_at' => null,
        'finalized_by' => null,
    ]);

    return back()->with('success', 'This term has been reopened for editing.');
}
}