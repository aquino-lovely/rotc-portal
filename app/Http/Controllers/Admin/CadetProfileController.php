<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CadetApprovedMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CadetProfileController extends Controller
{

public function index(Request $request)
{
    $query = User::where('role', 'cadet')->with('cadet');

    // NCOs only see approved cadets
    if (! $request->user()->isS1()) {
    $query->where('status', 'approved');
}
    if ($search = $request->input('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
              ->orWhereHas('cadet', function ($c) use ($search) {
                  $c->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%");
              });
        });
    }

    $cadets = $query->latest()->paginate(15);

    return view('admin.cadets.index', compact('cadets'));
}

public function show(User $user)
{
    abort_unless($user->role === 'cadet', 404);

    // NCOs can't open pending or rejected cadets, even by typing the URL
    abort_if(! auth()->user()->isS1() && $user->status !== 'approved', 404);
    $user->load('cadet.beneficiary');
    return view('admin.cadets.show', compact('user'));
}

    /**
     * A cadet viewing their OWN profile (self-service, no {id} in the route).
     */
    public function showOwn(Request $request)
    {
        $user = $request->user();
        abort_unless($user->role === 'cadet', 404);
        $user->load('cadet.beneficiary');
        return view('admin.cadets.show', compact('user'));
    }

   public function approve(Request $request, User $user)
{
    abort_unless($request->user()->isS1(), 403);

    if ($user->role !== 'cadet' || $user->status !== 'pending') {
        return back()->with('error', 'This cadet is not pending approval.');
    }

    $temporaryPassword = Str::password(10);

    DB::transaction(function () use ($user, $request, $temporaryPassword) {
        $user->update([
            'password'             => Hash::make($temporaryPassword),
            'status'               => 'approved',
            'must_change_password' => true,
        ]);

        $user->cadet->update([
            'rank'       => $request->input('rank', 'Private'),
            'company'    => $request->input('company'),
            'cadet_type' => $request->input('cadet_type', 'private'),
        ]);
    });

    $user->refresh();

    $emailSent = true;

    try {
        Mail::to($user->email)->send(new CadetApprovedMail($user, $temporaryPassword));
    } catch (\Throwable $e) {
        $emailSent = false;
        Log::error('Cadet approval email failed', [
            'user_id' => $user->id,
            'error'   => $e->getMessage(),
        ]);
    }

    $redirect = redirect()->route('admin.cadets.show', $user);

    if ($emailSent) {
        return $redirect->with('success', "{$user->cadet->full_name} has been approved and notified via email.");
    }

    return $redirect
        ->with('warning', "{$user->cadet->full_name} was approved, but the email could not be sent. Give them the temporary password below in person.")
        ->with('temporary_password', $temporaryPassword);
}

public function updateRankCompany(Request $request, User $user)
{
    abort_unless($request->user()->isS1(), 403);
    abort_unless($user->role === 'cadet', 404);

    $request->validate([
        'rank'        => ['nullable', 'string'],
        'company'     => ['nullable', 'string'],
        'new_company' => ['nullable', 'string', 'max:50', 'required_if:company,__other__'],
        'platoon'     => ['nullable', 'string', 'in:First Platoon,Second Platoon'],
    ]);

    $cadet = $user->cadet;

    // Resolve the actual company name (handles the "Other" free-text case)
    $company = $cadet->company;
    if ($request->filled('company')) {
        $company = $request->input('company') === '__other__'
            ? trim($request->input('new_company'))
            : $request->input('company');
    }

    $platoon = $request->filled('platoon') ? $request->input('platoon') : $cadet->platoon;

    if ($company && !$platoon) {
        return back()->with('error', 'Please select a platoon for this company.')->withInput();
    }

    // 37-cadet cap per company + platoon
    if ($company && $platoon) {
        $isChanging = ($company !== $cadet->company) || ($platoon !== $cadet->platoon);

        if ($isChanging) {
            $count = \App\Models\Cadet::where('company', $company)
                ->where('platoon', $platoon)
                ->where('id', '!=', $cadet->id)
                ->count();

            if ($count >= 37) {
                return back()->with('error', "{$company} {$platoon} is already full (37/37 cadets).")->withInput();
            }
        }
    }

    $changed = [];
    $updates = [];

    if ($request->filled('rank') && $cadet->rank !== $request->input('rank')) {
        $updates['rank'] = $request->input('rank');
        $changed[] = 'Rank';
    }
    if ($cadet->company !== $company) {
        $updates['company'] = $company;
        $changed[] = 'Company';
    }
    if ($cadet->platoon !== $platoon) {
        $updates['platoon'] = $platoon;
        $changed[] = 'Platoon';
    }

    if (empty($updates)) {
        return back()->with('error', 'No changes were made.');
    }

    $cadet->update($updates);

    return redirect()
        ->route('admin.cadets.show', $user)
        ->with('success', implode(', ', $changed) . ' updated successfully.');
}
    public function reject(Request $request, User $user)
{
        
        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        abort_unless($request->user()->isS1(), 403);

        if ($user->role !== 'cadet' || $user->status !== 'pending') {
            return back()->with('error', 'This cadet is not pending approval.');
        }

        $user->update(['status' => 'rejected']);

        return redirect()
            ->route('admin.cadets.index')
            ->with('success', 'Registration has been rejected.');
    }
}