<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCadetRegistrationRequest;
use App\Models\Beneficiary;
use App\Models\Cadet;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CadetRegisterController extends Controller
{
     public function chooseType()
    {
        return view('auth.register-type');
    }

    public function storeType(Request $request)
    {
        $request->validate([
            'enrollee_type' => 'required|in:psu_student,cross_enrollee',
              'suffix'             => ['nullable', 'in:Jr.,Sr.,II,III,IV'],
  'beneficiary_suffix' => ['nullable', 'in:Jr.,Sr.,II,III,IV'],
        ]);

        session([
            'registration_choice.enrollee_type' => $request->enrollee_type
        ]);

        return redirect()->route('register');
    }
    /**
     * Show the ROTC registration form.
     */
    public function create()

    {
         $enrolleeType = session('registration_choice.enrollee_type');

        if (!$enrolleeType) {
            return redirect()->route('register.type');
        }
        return view('auth.register', compact('enrolleeType'));
    }

    /**
     * Handle the registration submission.
     */
    public function store(StoreCadetRegistrationRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request) {
         $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'username' => $validated['student_number'],
            'email' => $validated['email'],
            'password' => bcrypt(Str::random(32)), // placeholder, unusable until approval
            'role' => 'cadet',
            'status' => 'pending',
            'must_change_password' => true,
        ]);

            // 2. Store the uploaded photo
            $photoPath = $request->file('photo')->store('cadet_photos', 'public');

            // 3. Create the cadet profile
            $cadet = Cadet::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                  'suffix' => $validated['suffix'] ?? null,
                'birthday' => $validated['birthday'],
                'age' => $validated['age'],
                'sex' => $validated['sex'],
                'civil_status' => $validated['civil_status'],
                'religion' => $validated['religion'] ?? null,
                'blood_type' => $validated['blood_type'] ?? null,
                'height_cm' => $validated['height_cm'] ?? null,
                'weight_kg' => $validated['weight_kg'] ?? null,
                'skill_1' => $validated['skill_1'] ?? null,
                'skill_2' => $validated['skill_2'] ?? null,
                'barangay' => $validated['barangay'],
                'city_municipality' => $validated['city_municipality'],
                'province' => $validated['province'],
                'contact_no' => $validated['contact_no'],
                'campus' => $validated['campus'],
                'student_number' => $validated['student_number'],
                'course' => $validated['course'],
                'year_section' => $validated['year_section'],
                'photo_path' => $photoPath,
            ]);

            // 4. Create the beneficiary record
            Beneficiary::create([
                'cadet_id' => $cadet->id,
                'surname' => $validated['beneficiary_surname'],
                'first_name' => $validated['beneficiary_first_name'],
                  'suffix' => $validated['beneficiary_suffix'] ?? null,
                'middle_initial' => $validated['beneficiary_middle_initial'] ?? null,
                'contact_no' => $validated['beneficiary_contact_no'],
                'relationship' => $validated['beneficiary_relationship'],
                'barangay' => $validated['beneficiary_barangay'],
                'city_municipality' => $validated['beneficiary_city_municipality'],
                'province' => $validated['beneficiary_province'],
                  'suffix' => $validated['beneficiary_suffix'] ?? null,
            ]);
        });

        return redirect()->route('login')->with(
            'status',
            'Registration submitted! Please wait for admin approval — you will receive your login credentials via email.'
        );
    }
}