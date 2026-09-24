<?php

namespace App\Http\Controllers\Cadet;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\EnrollmentSetting;
use App\Models\ExcuseRequest;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user    = $request->user();
        $setting = EnrollmentSetting::getActive();

        $sessionIds = $setting
            ? AttendanceSession::where('enrollment_setting_id', $setting->id)->pluck('id')
            : collect();

        $records = AttendanceRecord::whereIn('attendance_session_id', $sessionIds)
            ->where('user_id', $user->id)
            ->with('session')
            ->get()
            ->sortByDesc(fn($r) => $r->session->session_date);

        $totalSessions = $sessionIds->count();
        $present = $records->where('status', 'present')->count();
        $absent  = $records->where('status', 'absent')->count();
        $late    = $records->where('status', 'late')->count();
        $excused = $records->where('status', 'excused')->count();

        return view('cadets.attendance', compact(
            'setting', 'records', 'totalSessions',
            'present', 'absent', 'late', 'excused'
        ));
    }

    public function submitExcuse(Request $request)
    {
        $request->validate([
            'training_date' => ['required', 'date'],
            'reason'        => ['required', 'string'],
            'explanation'   => ['nullable', 'string', 'max:1000'],
            'document'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $setting = EnrollmentSetting::getActive();
        if (!$setting) {
            return back()->with('error', 'No active enrollment period.');
        }

        $path = null;
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('excuse_documents', 'public');
        }

        ExcuseRequest::create([
            'user_id'               => $request->user()->id,
            'enrollment_setting_id' => $setting->id,
            'training_date'         => $request->training_date,
            'reason'                => $request->reason,
            'explanation'           => $request->explanation,
            'document_path'         => $path,
            'status'                => 'pending',
        ]);

        return back()->with('success', 'Excuse letter submitted. Waiting for admin approval.');
    }
}