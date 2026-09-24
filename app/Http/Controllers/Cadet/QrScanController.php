<?php

namespace App\Http\Controllers\Cadet;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;

class QrScanController extends Controller
{
    // Camera scanner page
    public function index(Request $request)
    {
        return view('cadets.scan');
    }

    // Called after camera reads a QR code (via AJAX)
    public function process(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $session = AttendanceSession::where('qr_token', $request->token)
            ->where('is_active', true)
            ->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Invalid or inactive QR code.'], 422);
        }

        if ($session->qr_expires_at && now()->greaterThan($session->qr_expires_at)) {
            return response()->json(['success' => false, 'message' => 'This QR code has expired.'], 422);
        }

        $user = $request->user();
        $now  = now();

        $record = AttendanceRecord::where('attendance_session_id', $session->id)
            ->where('user_id', $user->id)
            ->first();

        // First scan = Time In
        if (!$record) {
            $isLate = $session->cutoff_time && $now->format('H:i:s') > $session->cutoff_time;

            AttendanceRecord::create([
                'attendance_session_id' => $session->id,
                'user_id'                => $user->id,
                'status'                 => $isLate ? 'late' : 'present',
                'time_in'                => $now->format('H:i:s'),
            ]);

            return response()->json([
                'success' => true,
                'message' => $isLate
                    ? 'Attendance recorded — marked as Late.'
                    : 'Attendance recorded — Present. Welcome!',
            ]);
        }

        // Second scan = Time Out
        if (!$record->time_out) {
            $record->update(['time_out' => $now->format('H:i:s')]);
            return response()->json([
                'success' => true,
                'message' => 'Time out recorded. See you next session!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'You have already checked in and out for today.',
        ], 422);
    }
}