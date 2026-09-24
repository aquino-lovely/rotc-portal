<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\EnrollmentSetting;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ExcuseRequest;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $setting = EnrollmentSetting::getActive();
        $date    = $request->input('date', now()->toDateString());

        $session = $setting
            ? AttendanceSession::where('enrollment_setting_id', $setting->id)
                ->where('session_date', $date)
                ->first()
            : null;

        $cadets = User::where('role', 'cadet')
    ->where('status', 'approved')
    ->whereHas('enrollments', function ($q) use ($setting) {
        $q->where('enrollment_setting_id', $setting?->id ?? 0);
    })
    ->with('cadet')
    ->get();
        $records = $session
            ? AttendanceRecord::where('attendance_session_id', $session->id)
                ->get()->keyBy('user_id')
            : collect();

        $total   = $cadets->count();
        $present = $records->where('status', 'present')->count();
        $absent  = $records->where('status', 'absent')->count();
        $late    = $records->where('status', 'late')->count();
        $excused = $records->where('status', 'excused')->count();

        return view('admin.attendance.index', compact(
            'setting', 'session', 'cadets', 'records', 'date',
            'total', 'present', 'absent', 'late', 'excused'
        ));
    }

    public function updateStatus(Request $request, $userId)
    {
        $request->validate([
            'status' => ['required', 'in:present,absent,late,excused'],
            'date'   => ['required', 'date'],
        ]);

        $setting = EnrollmentSetting::getActive();

        if (!$setting) {
            return back()->with('error', 'No active enrollment period.');
        }

        if ($setting->isFinalized()) {
            return back()->with('error', 'This term has been finalized. Attendance can no longer be changed.');
        }

        $session = AttendanceSession::firstOrCreate(
            ['enrollment_setting_id' => $setting->id, 'session_date' => $request->date],
            ['is_active' => false]
        );

        AttendanceRecord::updateOrCreate(
            ['attendance_session_id' => $session->id, 'user_id' => $userId],
            ['status' => $request->status]
        );

        return back()->with('success', 'Attendance status updated.');
    }

    public function excuseRequests(Request $request)
    {
        $setting = EnrollmentSetting::getActive();

        $requests = ExcuseRequest::with('user.cadet')
            ->when($setting, fn($q) => $q->where('enrollment_setting_id', $setting->id))
            ->latest()
            ->get();

        return view('admin.attendance.excuses', compact('requests', 'setting'));
    }

    public function approveExcuse(ExcuseRequest $excuseRequest)
    {
        $setting = $excuseRequest->setting;

        if ($setting && $setting->isFinalized()) {
            return back()->with('error', 'This term has been finalized. Excuse requests can no longer be processed.');
        }

        $excuseRequest->update(['status' => 'approved']);

        $session = AttendanceSession::firstOrCreate(
            ['enrollment_setting_id' => $setting->id, 'session_date' => $excuseRequest->training_date],
            ['is_active' => false]
        );

        AttendanceRecord::updateOrCreate(
            ['attendance_session_id' => $session->id, 'user_id' => $excuseRequest->user_id],
            ['status' => 'excused', 'excuse_reason' => $excuseRequest->reason]
        );

        return back()->with('success', 'Excuse request approved and attendance updated.');
    }

    public function rejectExcuse(ExcuseRequest $excuseRequest)
    {
        $setting = $excuseRequest->setting;

        if ($setting && $setting->isFinalized()) {
            return back()->with('error', 'This term has been finalized. Excuse requests can no longer be processed.');
        }

        $excuseRequest->update(['status' => 'rejected']);
        return back()->with('success', 'Excuse request rejected.');
    }
}