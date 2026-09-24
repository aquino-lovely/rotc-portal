<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\EnrollmentSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QrAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $setting = EnrollmentSetting::getActive();

        $today = now()->toDateString();

        $session = $setting
            ? AttendanceSession::where('enrollment_setting_id', $setting->id)
                ->where('session_date', $today)
                ->first()
            : null;

        $scannedRecords = $session
            ? AttendanceRecord::where('attendance_session_id', $session->id)
                ->whereIn('status', ['present', 'late'])
                ->with('user.cadet')
                ->orderByDesc('updated_at')
                ->get()
            : collect();

        return view('admin.attendance.qr', compact('setting', 'session', 'scannedRecords'));
    }

    public function generate(Request $request)
{
    $setting = EnrollmentSetting::getActive();
    if (!$setting) {
        return back()->with('error', 'No active enrollment period.');
    }

    if ($setting->isFinalized()) {
        return back()->with('error', 'This term has been finalized. No new attendance sessions can be created.');
    }

    $request->validate([
        'cutoff_time'    => ['nullable', 'date_format:H:i'],
        'expiry_minutes' => ['nullable', 'integer', 'min:1', 'max:180'],
        'term'           => ['required', 'in:midterm,final'],
    ]);

    $today = now()->toDateString();
    $expiryMinutes = (int) $request->input('expiry_minutes', 30);

    $session = AttendanceSession::updateOrCreate(
        ['enrollment_setting_id' => $setting->id, 'session_date' => $today],
        [
            'cutoff_time'   => $request->input('cutoff_time', '08:00'),
            'qr_token'      => Str::random(32),
            'qr_expires_at' => now()->addMinutes($expiryMinutes),
            'is_active'     => true,
            'term'          => $request->input('term'),
        ]
    );

    return back()->with('success', 'QR code generated successfully.');
}
    // Close the current QR session
    public function close(Request $request)
    {
        $setting = EnrollmentSetting::getActive();

        if (!$setting) {
            return back()->with('error', 'No active enrollment period.');
        }

        if ($setting->isFinalized()) {
            return back()->with('error', 'This term has been finalized. Attendance sessions can no longer be modified.');
        }

        $today   = now()->toDateString();

        $session = AttendanceSession::where('enrollment_setting_id', $setting->id)
            ->where('session_date', $today)
            ->first();

        if ($session) {
            $session->update(['is_active' => false]);

            // Mark everyone who didn't scan as absent
            $allCadetIds = User::where('role', 'cadet')
    ->where('status', 'approved')
    ->whereHas('enrollments', fn($q) => $q->where('enrollment_setting_id', $setting->id))
    ->pluck('id');
            $scannedIds  = AttendanceRecord::where('attendance_session_id', $session->id)->pluck('user_id');
            $missingIds  = $allCadetIds->diff($scannedIds);

            foreach ($missingIds as $userId) {
                AttendanceRecord::create([
                    'attendance_session_id' => $session->id,
                    'user_id'                => $userId,
                    'status'                 => 'absent',
                ]);
            }
        }

        return back()->with('success', 'Attendance session closed. Absent cadets marked automatically.');
    }

    // Poll for live scan updates (AJAX)
    public function scans(Request $request)
    {
        $setting = EnrollmentSetting::getActive();
        $today   = now()->toDateString();

        $session = $setting
            ? AttendanceSession::where('enrollment_setting_id', $setting->id)
                ->where('session_date', $today)
                ->first()
            : null;

        if (!$session) {
            return response()->json(['records' => [], 'count' => 0]);
        }

        $records = AttendanceRecord::where('attendance_session_id', $session->id)
            ->whereIn('status', ['present', 'late'])
            ->with('user.cadet')
            ->orderByDesc('updated_at')
            ->get()
            ->map(function ($r) {
                return [
                    'name'   => $r->user->cadet->last_name . ', ' . $r->user->cadet->first_name,
                    'status' => ucfirst($r->status),
                    'time'   => $r->time_in ? \Carbon\Carbon::parse($r->time_in)->format('g:i A') : '',
                ];
            });

        return response()->json(['records' => $records, 'count' => $records->count()]);
    }
}