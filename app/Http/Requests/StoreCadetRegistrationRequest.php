<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCadetRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            // Account
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],

            // Personal Information
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix'             => ['nullable', 'in:Jr.,Sr.,II,III,IV'],
            'beneficiary_suffix' => ['nullable', 'in:Jr.,Sr.,II,III,IV'],
            'birthday' => ['required', 'date', 'before:today'],
            'age' => ['required', 'integer', 'min:15', 'max:60'],
            'sex' => ['required', 'in:Male,Female'],
            'civil_status' => ['required', 'string', 'max:50'],
            'religion' => ['nullable', 'string', 'max:100'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'height_cm' => ['nullable', 'numeric', 'min:50', 'max:250'],
            'weight_kg' => ['nullable', 'numeric', 'min:20', 'max:300'],
            'skill_1' => ['nullable', 'string', 'max:100'],
            'skill_2' => ['nullable', 'string', 'max:100'],

            // Address
            'barangay' => ['required', 'string', 'max:100'],
            'city_municipality' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'contact_no' => ['required', 'string', 'max:20'],

            // Academic Information
            'student_number' => ['required', 'string', 'max:50', 'unique:cadets,student_number', 'unique:users,username'],
            'course' => ['required', 'string', 'max:100'],
            'year_section' => ['required', 'string', 'max:50'],

            // Upload
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            // Beneficiary
            'beneficiary_surname' => ['required', 'string', 'max:100'],
            'beneficiary_first_name' => ['required', 'string', 'max:100'],
            'beneficiary_middle_initial' => ['nullable', 'string', 'max:5'],
            'beneficiary_contact_no' => ['required', 'string', 'max:20'],
            'beneficiary_relationship' => ['required', 'string', 'max:50'],
            'beneficiary_barangay' => ['required', 'string', 'max:100'],
            'beneficiary_city_municipality' => ['required', 'string', 'max:100'],
            'beneficiary_province' => ['required', 'string', 'max:100'],

            // Certification checkbox
            'certify' => ['accepted'],
        ];

        // Campus vs. School, and COR upload, depend on which type the cadet picked
        if (session('registration_choice.enrollee_type') === 'cross_enrollee') {
            $rules['school'] = ['required', 'string', 'max:100'];
            $rules['cor_document'] = ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'];
        } else {
            $rules['campus'] = ['required', 'string', 'max:100'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'certify' => 'You must certify that the information provided is true and agree to ROTC rules.',
            'photo.required' => 'Please upload a 2x2 photo.',
            'school.required' => 'Please enter your school/university.',
            'campus.required' => 'Please select your campus.',
            'cor_document.required' => 'Please upload your Certificate of Registration (COR).',
        ];
    }
}