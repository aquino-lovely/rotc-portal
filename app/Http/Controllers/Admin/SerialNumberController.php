<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cadet;   // check this matches your cadet model's name
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EnrollmentSetting;

class SerialNumberController extends Controller
{
    public function index(Request $request)
{
    $schoolYears = EnrollmentSetting::select('school_year')
        ->distinct()->orderByDesc('school_year')->pluck('school_year');

    // Defaults to the active school year; "all" shows every batch
    $schoolYear = $request->input('school_year', EnrollmentSetting::getActive()?->school_year);

    $query = User::where('role', 'cadet')
        ->where('status', 'approved')
        ->with('cadet');

    if ($schoolYear && $schoolYear !== 'all') {
        $query->whereHas('enrollments', function ($e) use ($schoolYear) {
            $e->where('status', 'finalized')
              ->whereHas('setting', fn ($s) => $s->where('school_year', $schoolYear));
        });
    }

    if ($search = $request->input('search')) {
        $query->whereHas('cadet', function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('student_number', 'like', "%{$search}%")
              ->orWhere('serial_number', 'like', "%{$search}%");
        });
    }

    $cadets = $query->latest()->paginate(20);

    return view('admin.serial.index', compact('cadets', 'schoolYears', 'schoolYear'));
}
    public function update(Request $request)
    {
        // 1. Normalize: trim + uppercase, empty -> null (keys are preserved)
        $serials = array_map(
            fn ($s) => $s ? strtoupper(trim($s)) : null,
            $request->input('serial', [])
        );
        $request->merge(['serial' => $serials]);

        // 2. Validate the format
        $request->validate([
            'serial'   => ['array'],
            'serial.*' => ['nullable', 'regex:/^PG-R\d{2}-\d{6} PA\(RES\)$/'],
        ], [
            'serial.*.regex' => 'Serial number must be in the format PG-R24-000412 PA(RES).',
        ]);

        // 3. Duplicates inside the submitted list
        $filled = array_filter($serials);
        if (count($filled) !== count(array_unique($filled))) {
            return back()->withInput()->withErrors(['serial' => 'Duplicate serial numbers in the list.']);
        }

        // 4. Load all users once
        $users = User::with('cadet')
            ->where('role', 'cadet')
            ->whereIn('id', array_keys($serials))
            ->get()
            ->keyBy('id');

        // 5. Check for serials already used by other cadets
        $errors = [];
        foreach ($serials as $userId => $serial) {
            $user = $users->get($userId);
            if (!$user || !$user->cadet || !$serial) {
                continue;
            }

            $taken = Cadet::where('serial_number', $serial)
                ->where('id', '!=', $user->cadet->id)
                ->exists();

            if ($taken) {
                $errors["serial.$userId"] = "Serial number $serial is already assigned to another cadet.";
            }
        }

        if ($errors) {
            return back()->withInput()->withErrors($errors);
        }

        // 6. Save everything together
        DB::transaction(function () use ($serials, $users) {
            foreach ($serials as $userId => $serial) {
                $user = $users->get($userId);
                if ($user && $user->cadet) {
                    $user->cadet->update(['serial_number' => $serial]);
                }
            }
        });

        return back()->with('success', 'Serial numbers updated successfully.');
    }
}