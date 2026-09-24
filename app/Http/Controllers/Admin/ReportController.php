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
    $request->validate([
        'report_type'           => ['required', 'in:attendance,grades,enrollment'],
        'format'                => ['nullable', 'in:csv,pdf'],
        'date_from'              => ['nullable', 'date'],
        'date_to'                => ['nullable', 'date', 'after_or_equal:date_from'],
        'enrollment_setting_id'  => ['required_if:report_type,grades', 'integer', 'exists:enrollment_settings,id'],
        'professor'              => ['required_if:report_type,grades', 'string', 'max:255'],
    ]);

    return match ($request->report_type) {
        'attendance' => $this->attendanceReport($request),
        'grades'     => $request->format === 'pdf'
            ? $this->gradesPdf($request)
            : $this->gradesReport($request),
        'enrollment' => $request->format === 'pdf'
            ? $this->enrollmentPdf($request)
            : $this->enrollmentReport($request),
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

    private function attendanceReport(Request $request)
{
    $request->validate([
        'date_from' => 'nullable|date',
        'date_to'   => 'nullable|date|after_or_equal:date_from',
    ]);

    $query = AttendanceRecord::with(['user.cadet', 'session']);

    if ($request->filled('date_from')) {
        $query->whereHas('session', fn($q) => $q->whereDate('session_date', '>=', $request->date_from));
    }
    if ($request->filled('date_to')) {
        $query->whereHas('session', fn($q) => $q->whereDate('session_date', '<=', $request->date_to));
    }

    // Alphabetical: last name, then first name, then session date
    $records = $query->get()->sort(function ($a, $b) {
        return strcasecmp($a->user->cadet->last_name ?? '', $b->user->cadet->last_name ?? '')
            ?: strcasecmp($a->user->cadet->first_name ?? '', $b->user->cadet->first_name ?? '')
            ?: ($a->session->session_date->timestamp <=> $b->session->session_date->timestamp);
    })->values();

    // Filename includes the date range
    $from  = $request->date_from;
    $to    = $request->date_to;
    $range = ($from || $to)
        ? ($from ?: 'start') . '_to_' . ($to ?: 'latest')
        : 'all';
    $baseName = "attendance_report_{$range}_" . now()->format('Y-m-d');

    // PDF
    if ($request->format === 'pdf') {
        return Pdf::loadView('admin.reports.attendance_pdf', [
            'records' => $records,
            'from'    => $from,
            'to'      => $to,
        ])->setPaper('a4', 'portrait')->download($baseName . '.pdf');
    }

    // CSV
        // CSV
    return response()->streamDownload(function () use ($records) {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Student No.', 'Name', 'Date', 'Status']);

        foreach ($records as $r) {
            fputcsv($handle, [
                $r->user->cadet->student_number ?? '—',
                ($r->user->cadet->last_name ?? '') . ', ' . ($r->user->cadet->first_name ?? ''),
                $r->session->session_date->format('Y-m-d'),
                ucfirst($r->status),
            ]);
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

   return Pdf::loadView('admin.reports.enrollment-form', compact('enrollments'))
        ->setPaper('a4', 'portrait')
        ->download('enrollment_forms_' . now()->format('Y-m-d') . '.pdf');
}
}