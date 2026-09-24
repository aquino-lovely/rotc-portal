<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ChangePasswordController extends Controller
{
    public function show(): View
    {
        return view('auth.change-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = $request->user();

        // Prevent reusing the temp password
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Your new password cannot be the same as your temporary password.',
            ]);
        }

        $user->update([
            'password'            => $request->password,
            'must_change_password' => false,
        ]);

        return redirect()->intended(route('dashboard'))->with('status', 'Password updated successfully.');
    }
}