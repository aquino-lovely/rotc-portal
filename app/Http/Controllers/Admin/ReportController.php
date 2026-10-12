<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\GradeRecord;
use App\Models\Enrollment;
use App\Models\EnrollmentSetting;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\GradeCalculator;
use App\Models\PhysicalFitness;
use App\Models\User;

class ReportController extends Controller
{
   public function index()
{
    $enrollmentSettings = EnrollmentSetting::orderByDesc('school_year')
        ->orderByDesc('semester')
        ->get();

    return view('admin.reports.index', compact('enrollmentSettings'));
}

public function generate(Request $request)
{
    if ($request->user()->role === 'GMA'
    && ! in_array($request->input('report_type'), ['performance', 'classlist'], true)) {
    abort(403, 'You are not allowed to generate this report.');
} 
    $request->validate([
    'report_type'            => ['required', 'in:attendance,grades,enrollment,classlist,performance'],
'performance_setting_id' => ['nullable', 'required_if:report_type,performance', 'integer', 'exists:enrollment_settings,id'],
    'format'                 => ['nullable', 'in:csv,pdf'],
    'date_from'              => ['nullable', 'date'],
    'date_to'                => ['nullable', 'date', 'after_or_equal:date_from'],
    'enrollment_setting_id'  => ['nullable', 'required_if:report_type,grades', 'integer', 'exists:enrollment_settings,id'],
    'attendance_type' => ['nullable', 'required_if:report_type,attendance', 'in:training_day,semestral'],
    'classlist_setting_id'   => ['nullable', 'required_if:report_type,classlist', 'integer', 'exists:enrollment_settings,id'],
    'classlist_professor'    => ['nullable', 'string', 'max:255'],
    'training_day'           => ['nullable', 'date'],
    'professor'              => ['nullable', 'required_if:report_type,grades', 'string', 'max:255'],
]);


    return match ($request->report_type) {
        'attendance' => $this->attendanceReport($request),
        'classlist'  => $request->format === 'pdf'
    ? $this->classlistPdf($request)
    : $this->classlistReport($request),
        'grades'     => $request->format === 'pdf'
            ? $this->gradesPdf($request)
            : $this->gradesReport($request),
        'enrollment' => $request->format === 'pdf'
            ? $this->enrollmentPdf($request)
            : $this->enrollmentReport($request),
        'performance' => $request->format === 'pdf'
            ? $this->performancePdf($request)
            : $this->performanceReport($request),
    };
}

private function loadGradeReportData(Request $request): array
{
    $setting = EnrollmentSetting::findOrFail($request->enrollment_setting_id);

    $records = GradeRecord::where('enrollment_setting_id', $setting->id)
        ->with('user.cadet')
        ->get()
        ->groupBy('user_id');

   $calculator = GradeCalculator::fromSetting($setting);

    $rows = $records->map(function ($termRecords) use ($calculator) {
        $midterm = $termRecords->firstWhere('term', 'midterm');
        $final   = $termRecords->firstWhere('term', 'final');
        $cadet   = ($midterm ?? $final)?->user?->cadet;

        $semestral = $calculator->semestral(
            $midterm->final_score ?? 0,
            $final->final_score ?? 0
        );

        return [
            'student_number'  => $cadet->student_number ?? null,
            'last_name'       => $cadet->last_name ?? '—',
            'first_name'      => $cadet->first_name ?? '',
            'middle_name'     => $cadet->middle_name ?? null,
            'midterm'         => $midterm->grade ?? null,
            'tentative_final' => $final->grade ?? null,
            'final_grade'     => $semestral['numerical'],
            'status'          => $semestral['numerical'] <= 3.00 ? 'pass' : 'fail',
        ];
    })
    ->sortBy(fn ($r) => $r['last_name'])
    ->values()
    ->toArray();

    $subjectCode = $setting->semester === 'First Semester' ? 'NSTP 101' : 'NSTP 102';

    return [
        'subject_code'      => $subjectCode,
        'descriptive_title' => 'National Service Training Program - ROTC',
        'year_section'      => '',
        'professor'         => $request->professor,
        'passed'            => collect($rows)->where('status', 'pass')->count(),
        'failed'            => collect($rows)->where('status', 'fail')->count(),
        'rows'              => $rows,
    ];
}

private function gradesReport(Request $request): StreamedResponse
{
    $data = $this->loadGradeReportData($request);
    $filename = 'grades_report_' . now()->format('Y-m-d') . '.csv';

    return response()->streamDownload(function () use ($data) {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Student No.', 'Name', 'Mid-Term', 'Tentative Final', 'Final Grade', 'Status']);

        foreach ($data['rows'] as $r) {
            fputcsv($handle, [
                $r['student_number'] ?? '—',
                $r['last_name'] . ', ' . $r['first_name'],
                $r['midterm'] !== null ? number_format($r['midterm'], 2) : '—',
                $r['tentative_final'] !== null ? number_format($r['tentative_final'], 2) : '—',
                $r['final_grade'] !== null ? number_format($r['final_grade'], 2) : '—',
                ucfirst($r['status'] ?? '—'),
            ]);
        }
        fclose($handle);
    }, $filename, ['Content-Type' => 'text/csv']);
}



private function gradesPdf(Request $request)
{
    $data = $this->loadGradeReportData($request);

    $pdf = \PDF::loadView('admin.reports.rating', $data);

    return $pdf->download('grades_report_' . now()->format('Y-m-d') . '.pdf');
}

  private function loadAttendanceData(Request $request): array
{
    $setting = EnrollmentSetting::findOrFail($request->attendance_setting_id);

    [$from, $to] = $this->semesterWindow($setting);

    $type        = $request->attendance_type;
    $trainingDay = null;

    if ($type === 'training_day') {
        if (! $request->filled('training_day')) {
            throw \Illuminate\Validation\ValidationException::withMessages(['training_day' => 'Please choose a training day.']);
        }
        $trainingDay = \Carbon\Carbon::parse($request->training_day);
        if (! $trainingDay->isSaturday()) {
            throw \Illuminate\Validation\ValidationException::withMessages(['training_day' => 'Training day must be a Saturday.']);
        }
        if ($trainingDay->toDateString() < $from->toDateString() || $trainingDay->toDateString() > $to->toDateString()) {
    throw \Illuminate\Validation\ValidationException::withMessages(['training_day' => 'Training day is outside the selected semester.']);
}
    }

    // Roster: finalized cadets, girls first (A–Z), then boys (A–Z)
    $cadets = Enrollment::with('user.cadet')
        ->where('enrollment_setting_id', $setting->id)
        ->where('status', 'finalized')
        ->get()
        ->filter(fn ($e) => $e->user?->cadet)
        ->map(function ($e) {
            $c  = $e->user->cadet;
            $mi = $c->middle_name ? ' ' . strtoupper(substr($c->middle_name, 0, 1)) . '.' : '';
            return [
                'user_id'   => $e->user_id,
                'is_female' => strtolower(substr($c->sex ?? '', 0, 1)) === 'f',   // 'sex' or 'gender'
                'sort'      => strtolower(($c->last_name ?? '') . ' ' . ($c->first_name ?? '')),
                'name'      => ($c->last_name ?? '') . ', ' . ($c->first_name ?? '') . $mi,
            ];
        });

    $roster = $cadets->where('is_female', true)->sortBy('sort')
        ->concat($cadets->where('is_female', false)->sortBy('sort'))
        ->values();

    // Saturday attendance records within the window
    $records = AttendanceRecord::with('session')
        ->whereIn('user_id', $roster->pluck('user_id'))
        ->whereHas('session', function ($q) use ($from, $to, $trainingDay) {
            if ($trainingDay) {
                $q->whereDate('session_date', $trainingDay->toDateString());
            } else {
                $q->whereBetween('session_date', [$from, $to]);
            }
        })
        ->get()
        ->filter(fn ($r) => $r->session->session_date->isSaturday());

    $dates = $trainingDay
        ? collect([$trainingDay->toDateString()])
        : $records->map(fn ($r) => $r->session->session_date->toDateString())->unique()->sort()->values();

    // [user_id][date] => status
    $lookup = [];
    foreach ($records as $r) {
        $lookup[$r->user_id][$r->session->session_date->toDateString()] = strtolower($r->status);
    }

    $codes = ['present' => 'P', 'late' => 'L', 'excused' => 'E', 'absent' => 'A'];

    $rows = $roster->map(function ($c) use ($dates, $lookup, $codes) {
        $cells  = [];
        $counts = ['present' => 0, 'late' => 0, 'excused' => 0, 'absent' => 0];

        foreach ($dates as $d) {
            $status = $lookup[$c['user_id']][$d] ?? 'absent';   // no record = absent
            if (! isset($counts[$status])) { $status = 'absent'; }
            $counts[$status]++;
            $cells[] = ['status' => $status, 'code' => $codes[$status]];
        }

        $total   = max(count($dates), 1);
        $percent = round((($counts['present'] + $counts['late']) / $total) * 100);

        return [
            'name'    => $c['name'],
            'cells'   => $cells,
            'present' => $counts['present'],
            'late'    => $counts['late'],
            'excused' => $counts['excused'],
            'absent'  => $counts['absent'],
            'percent' => $percent,
        ];
    })->toArray();

    return [
        'type'          => $type,
        'semesterLabel' => "{$setting->semester}, S.Y. {$setting->school_year}",
        'trainingDay'   => $trainingDay,
        'dates'         => $dates->toArray(),
        'rows'          => $rows,
       'summary'       => [
    'present' => collect($rows)->filter(fn ($r) => ($r['cells'][0]['status'] ?? null) === 'present')->count(),
    'late'    => collect($rows)->filter(fn ($r) => ($r['cells'][0]['status'] ?? null) === 'late')->count(),
    'excused' => collect($rows)->filter(fn ($r) => ($r['cells'][0]['status'] ?? null) === 'excused')->count(),
    'absent'  => collect($rows)->filter(fn ($r) => ($r['cells'][0]['status'] ?? null) === 'absent')->count(),
],
    ];
}

private function semesterWindow(EnrollmentSetting $setting): array
{
    // Use the real class dates when they are set
    if ($setting->class_start_date && $setting->class_end_date) {
        return [
            \Carbon\Carbon::parse($setting->class_start_date)->startOfDay(),
            \Carbon\Carbon::parse($setting->class_end_date)->endOfDay(),
        ];
    }

    // Fallback: guess from the school year
    [$startYear] = explode('-', $setting->school_year);
    $startYear = (int) $startYear;

    return $setting->semester === 'First Semester'
        ? [\Carbon\Carbon::create($startYear, 8, 1)->startOfDay(), \Carbon\Carbon::create($startYear, 12, 31)->endOfDay()]
        : [\Carbon\Carbon::create($startYear + 1, 1, 1)->startOfDay(), \Carbon\Carbon::create($startYear + 1, 5, 31)->endOfDay()];
}

private function attendanceReport(Request $request)

{
    $data = $this->loadAttendanceData($request);

    if (empty($data['rows'])) {
        return back()->with('error', 'No enrolled cadets found for the selected semester.');
    }
    if ($data['type'] === 'training_day' && empty($data['dates'])) {
    return back()->with('error', 'No Saturday training sessions found for the selected semester.');
}

    $dayPart  = $data['trainingDay'] ? $data['trainingDay']->format('Y-m-d') : 'semestral';
    $baseName = 'attendance_' . $dayPart . '_' . \Illuminate\Support\Str::slug($data['semesterLabel']);

   if ($request->format === 'pdf') {
    if ($data['type'] === 'semestral') {
        return Pdf::loadView('admin.reports.attendance_semestral', $this->loadSemestralData($request))
            ->setPaper('a4', 'landscape')
            ->download($baseName . '.pdf');
    }

    return Pdf::loadView('admin.reports.attendance_pdf', $data)
        ->setPaper('a4', 'portrait')
        ->download($baseName . '.pdf');
}

    return response()->streamDownload(function () use ($data) {
        $handle = fopen('php://output', 'w');

        if ($data['type'] === 'training_day') {
            fputcsv($handle, ['No.', 'Name', 'Status']);
            foreach ($data['rows'] as $i => $r) {
                fputcsv($handle, [$i + 1, $r['name'], ucfirst($r['cells'][0]['status'])]);
            }
        } else {
            $dateHeads = array_map(fn ($d) => \Carbon\Carbon::parse($d)->format('M d'), $data['dates']);
            fputcsv($handle, array_merge(['No.', 'Name'], $dateHeads, ['Present', 'Late', 'Excused', 'Absent', '%']));
            foreach ($data['rows'] as $i => $r) {
                fputcsv($handle, array_merge(
                    [$i + 1, $r['name']],
                    array_column($r['cells'], 'code'),
                    [$r['present'], $r['late'], $r['excused'], $r['absent'], $r['percent'] . '%']
                ));
            }
        }

        fclose($handle);
    }, $baseName . '.csv', ['Content-Type' => 'text/csv']);
}
    private function enrollmentReport(Request $request): StreamedResponse
    {
        $query = Enrollment::with(['user.cadet', 'setting']);

        if ($request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $records = $query->get();

        $filename = 'enrollment_report_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Student No.', 'Name', 'Course', 'Semester', 'Status', 'Date Submitted']);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->user->cadet->student_number ?? '—',
                    ($r->user->cadet->last_name ?? '') . ', ' . ($r->user->cadet->first_name ?? ''),
                    $r->user->cadet->course ?? '—',
                    $r->setting ? $r->setting->semester . ' ' . $r->setting->school_year : '—',
                    ucfirst($r->status),
                    $r->created_at->format('Y-m-d'),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function enrollmentPdf(Request $request)
{
    $query = Enrollment::with(['user.cadet.beneficiary', 'setting']);

    if ($request->date_from) {
        $query->where('created_at', '>=', $request->date_from);
    }
    if ($request->date_to) {
        $query->where('created_at', '<=', $request->date_to);
    }

    $enrollments = $query->get()->filter(fn ($e) => $e->user?->cadet);

    if ($enrollments->isEmpty()) {
        return back()->with('error', 'No enrollment records found for the selected dates.');
    }

    return Pdf::loadView('admin.reports.enrollment-form', [
        'enrollments' => $enrollments,
        'professor'   => $request->administering_officer,
    ])
        ->setPaper('a4', 'portrait')
        ->download('enrollment_forms_' . now()->format('Y-m-d') . '.pdf');
}

    private function loadClasslistData(Request $request): array
{
    $setting = EnrollmentSetting::findOrFail($request->classlist_setting_id);

    $cadets = Enrollment::with('user.cadet')
        ->where('enrollment_setting_id', $setting->id)   // adjust if your column differs
        ->where('status', 'finalized')                    // adjust/remove if needed
        ->get()
        ->filter(fn ($e) => $e->user?->cadet)
        ->map(function ($e) {
            $c = $e->user->cadet;
            $isFemale = strtolower(substr($c->sex ?? '', 0, 1)) === 'f';   // change 'sex' to 'gender' if needed

            return [
                'is_female'  => $isFemale,
                'last_name'  => $c->last_name ?? '',
                'first_name' => $c->first_name ?? '',
                'middle'     => $c->middle_name ?? '',
                'course'     => $c->course ?? '—',
            ];
        });

    $sortAlpha = fn ($r) => strtolower($r['last_name'] . ' ' . $r['first_name']);

    // Girls first (A–Z), then boys (A–Z)
    $rows = $cadets->where('is_female', true)->sortBy($sortAlpha)
        ->concat($cadets->where('is_female', false)->sortBy($sortAlpha))
        ->values()
        ->map(function ($r) {
            $mi = $r['middle'] ? ' ' . strtoupper(substr($r['middle'], 0, 1)) . '.' : '';
            $r['name'] = $r['last_name'] . ', ' . $r['first_name'] . $mi;
            return $r;
        })
        ->toArray();

    return [
        'semesterLabel' => "{$setting->semester}, S.Y. {$setting->school_year}",
        'professor'     => $request->classlist_professor,
        'total'         => count($rows),
        'rows'          => $rows,
    ];
}
private function classlistReport(Request $request)
{
    $data = $this->loadClasslistData($request);

    if (empty($data['rows'])) {
        return back()->with('error', 'No enrolled cadets found for the selected semester.');
    }

    $filename = 'class_list_' . \Illuminate\Support\Str::slug($data['semesterLabel']) . '_' . now()->format('Y-m-d') . '.csv';

    return response()->streamDownload(function () use ($data) {
        $handle = fopen('php://output', 'w');

        fputcsv($handle, ['No.', 'Name', 'Course']);

        foreach ($data['rows'] as $i => $r) {
            fputcsv($handle, [$i + 1, $r['name'], $r['course']]);
        }

        fclose($handle);
    }, $filename, ['Content-Type' => 'text/csv']);
}

private function classlistPdf(Request $request)
{
    $data = $this->loadClasslistData($request);

    if (empty($data['rows'])) {
        return back()->with('error', 'No enrolled cadets found for the selected semester.');
    }

    return Pdf::loadView('admin.reports.classlist', $data)
        ->setPaper('a4', 'portrait')
        ->download('class_list_' . now()->format('Y-m-d') . '.pdf');
}
private function loadSemestralData(Request $request): array
{
    $setting = EnrollmentSetting::findOrFail($request->attendance_setting_id);

    [$from, $to] = $this->semesterWindow($setting);

    $enrollments = Enrollment::with('user.cadet')
        ->where('enrollment_setting_id', $setting->id)
        ->where('status', 'finalized')
        ->get()
        ->filter(fn ($e) => $e->user?->cadet);

    $records = AttendanceRecord::with('session')
        ->whereIn('user_id', $enrollments->pluck('user_id'))
        ->whereHas('session', fn ($q) => $q->whereBetween('session_date', [$from, $to]))
        ->get()
        ->filter(fn ($r) => $r->session->session_date->isSaturday());

    // CHANGE 1: week columns = every Saturday from class start to class end
    $dates = collect();
    $d = $from->copy()->startOfDay();
    if (! $d->isSaturday()) {
        $d->next(\Carbon\Carbon::SATURDAY);
    }
    while ($d->lte($to)) {
        $dates->push($d->toDateString());
        $d->addWeek();
    }
    $dates = $dates->take(15)->values();

    // Saturdays that actually had a session
    $heldDates = $records->map(fn ($r) => $r->session->session_date->toDateString())->unique()->flip();

    $lookup = [];
    foreach ($records as $r) {
        $lookup[$r->user_id][$r->session->session_date->toDateString()] = strtolower($r->status);
    }

    $codes = ['present' => 'P', 'late' => 'L', 'excused' => 'E', 'absent' => 'A'];

    // CHANGE 2: $heldDates added to use (...)
    $people = $enrollments->map(function ($e) use ($dates, $lookup, $codes, $heldDates) {
        $c  = $e->user->cadet;
        $mi = $c->middle_name ? ' ' . strtoupper(substr($c->middle_name, 0, 1)) : '';

        // CHANGE 3: blank when no session was held that Saturday
        $cells = [];
        foreach ($dates as $d) {
            if (! isset($heldDates[$d])) {
                $cells[] = '';
                continue;
            }
            $status  = $lookup[$e->user_id][$d] ?? 'absent';
            $cells[] = $codes[$status] ?? 'A';
        }

        $school = trim($c->school ?? '');
        $campus = trim($c->campus ?? '');

        if ($school !== '') {
            $section = strtoupper($school) . ' (CROSS ENROLLED)';
            $rank    = 3;
        } elseif (stripos($campus, 'Infanta') !== false) {
            $section = 'PSU-INFANTA ROTCU';
            $rank    = 2;
        } else {
            $section = 'PSU-ALAMINOS ROTCU';
            $rank    = 1;
        }

        return [
            'section'   => $section,
            'rank'      => $rank,
            'level'     => $e->subject_level ?? 1,
            'is_female' => strtolower(substr($c->sex ?? '', 0, 1)) === 'f',   // 'sex' or 'gender'
            'sort'      => strtolower(($c->last_name ?? '') . ' ' . ($c->first_name ?? '')),
            'name'      => ($c->last_name ?? '') . ', ' . ($c->first_name ?? '') . $mi,
            'course'    => $c->course ?? '',
            'cells'     => $cells,
        ];
    });

    $sections = [];
    foreach ($people->sortBy(fn ($p) => $p['rank'] . '|' . $p['section'])->groupBy('section') as $sectionTitle => $inSection) {
        $groups = [];
        foreach ($inSection->groupBy('level')->sortKeys() as $level => $byLevel) {
            foreach (['FEMALE' => true, 'MALE' => false] as $label => $isFemale) {
                $rows = $byLevel->where('is_female', $isFemale)->sortBy('sort')->values()->toArray();
                if ($rows) {
                    $groups[] = ['title' => "MS-{$level} {$label}", 'rows' => $rows];
                }
            }
        }
        $sections[] = ['title' => $sectionTitle, 'groups' => $groups];
    }

    return [
        'ordinal'    => $setting->semester === 'First Semester' ? '1st' : '2nd',
        'schoolYear' => $setting->school_year,
        'sections'   => $sections,
    ];

}

private function loadPerformanceData(Request $request): array
{
    $setting = EnrollmentSetting::findOrFail($request->performance_setting_id);

    $scores = PhysicalFitness::where('enrollment_setting_id', $setting->id)
        ->whereNotNull('overall')
        ->get()
        ->keyBy('user_id');

    $users = User::with('cadet')
        ->whereIn('id', $scores->keys())
        ->get();

    $people = $users
        ->filter(fn ($u) => $u->cadet && $u->cadet->cadet_type === 'advance') // same as the PFT tab
        ->map(function ($u) use ($scores) {
            $c   = $u->cadet;
            $pft = $scores[$u->id];
            $mi  = $c->middle_name ? ' ' . strtoupper(substr($c->middle_name, 0, 1)) . '.' : '';

            return [
                'student_number' => $c->student_number ?? '—',
                'name'           => ($c->last_name ?? '') . ', ' . ($c->first_name ?? '') . $mi,
                'is_male'        => $c->sex === 'Male',
                'run_time'       => $pft->run_time,
                'push_ups'       => $pft->push_ups,
                'sit_ups'        => $pft->sit_ups,
                'overall'        => $pft->overall,
                'rating'         => $pft->rating ?? '—',
            ];
        });

    // Highest overall first, with rank numbers
    $rank = fn ($group) => $group
        ->sortBy([['overall', 'desc'], ['name', 'asc']])
        ->values()
        ->map(function ($r, $i) { $r['rank'] = $i + 1; return $r; })
        ->toArray();

    return [
        'semesterLabel' => "{$setting->semester}, S.Y. {$setting->school_year}",
        'female'        => $rank($people->where('is_male', false)),
        'male'          => $rank($people->where('is_male', true)),
    ];
}

private function performanceReport(Request $request)
{
    $data = $this->loadPerformanceData($request);

    if (empty($data['female']) && empty($data['male'])) {
        return back()->with('error', 'No physical fitness scores found for the selected semester.');
    }

    $filename = 'performance_' . \Illuminate\Support\Str::slug($data['semesterLabel']) . '_' . now()->format('Y-m-d') . '.csv';

    return response()->streamDownload(function () use ($data) {
        $handle = fopen('php://output', 'w');

        foreach (['FEMALE' => 'female', 'MALE' => 'male'] as $label => $key) {
            fputcsv($handle, [$label]);
            fputcsv($handle, ['Rank', 'Student No.', 'Name', 'Run Time (min)', 'Push-ups', 'Sit-ups', 'Overall', 'Rating']);

            foreach ($data[$key] as $r) {
                fputcsv($handle, [
                    $r['rank'], $r['student_number'], $r['name'],
                    $r['run_time'] ?? '—', $r['push_ups'] ?? '—', $r['sit_ups'] ?? '—',
                    $r['overall'], $r['rating'],
                ]);
            }
            fputcsv($handle, []);
        }

        fclose($handle);
    }, $filename, ['Content-Type' => 'text/csv']);
}

private function performancePdf(Request $request)
{
    $data = $this->loadPerformanceData($request);

    if (empty($data['female']) && empty($data['male'])) {
        return back()->with('error', 'No physical fitness scores found for the selected semester.');
    }

    return Pdf::loadView('admin.reports.performance', $data)
        ->setPaper('a4', 'portrait')
        ->download('performance_' . now()->format('Y-m-d') . '.pdf');
}
}