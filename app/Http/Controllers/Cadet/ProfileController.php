<?php

namespace App\Http\Controllers\Cadet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user  = $request->user()->load('cadet.beneficiary');
        $cadet = $user->cadet;
        return view('cadets.profile', compact('user', 'cadet'));
    }

    public function edit(Request $request)
    {
        $user  = $request->user()->load('cadet');
        $cadet = $user->cadet;
        return view('cadets.profile-edit', compact('user', 'cadet'));
    }

    public function update(Request $request)
    {
        $user  = $request->user()->load('cadet');
        $cadet = $user->cadet;

        $request->validate([
            'contact_no'        => ['required', 'string', 'max:20'],
            'barangay'          => ['required', 'string', 'max:100'],
            'city_municipality' => ['required', 'string', 'max:100'],
            'province'          => ['required', 'string', 'max:100'],
            'religion'          => ['nullable', 'string', 'max:100'],
            'civil_status'      => ['nullable', 'string', 'max:50'],
            'skill_1'           => ['nullable', 'string', 'max:100'],
            'skill_2'           => ['nullable', 'string', 'max:100'],
            'photo'             => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $data = $request->only([
            'contact_no', 'barangay', 'city_municipality',
            'province', 'religion', 'civil_status', 'skill_1', 'skill_2',
        ]);

        if ($request->hasFile('photo')) {
            if ($cadet->photo_path) {
                Storage::disk('public')->delete($cadet->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('cadet_photos', 'public');
        }

        $cadet->update($data);

        return redirect()->route('cadet.profile')->with('success', 'Profile updated successfully.');
    }
}