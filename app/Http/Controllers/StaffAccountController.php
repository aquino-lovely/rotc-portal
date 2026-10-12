<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffAccountController extends Controller
{
    public function index()
    {
        $staffAccounts = User::whereIn('role', ['deputy_s1', 'S1', 'GMA'])->get();
        return view('superadmin.staff-index', compact('staffAccounts'));
    }

    public function create()
    {
        return view('superadmin.staff-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', 'in:deputy_s1,S1,GMA'],
        ]);

        $tempPassword = Str::random(10);

        User::create([
            'name'                 => $validated['name'],
            'username'             => $validated['username'],
            'email'                => $validated['email'],
            'role'                 => $validated['role'],
            'password'             => Hash::make($tempPassword),
            'status'               => 'approved',
            'must_change_password' => true,
        ]);

        return redirect()->route('staff.index')
            ->with('success', "Account created. Temporary password: {$tempPassword}");
    }

    public function destroy(User $staff)
    {
        if ($staff->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $staff->delete();

        return back()->with('success', 'Staff account deleted successfully.');
    }
}