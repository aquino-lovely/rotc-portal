<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration - Alaminos-Infanta ROTC Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f4f4;
        }
        .header-bar {
            background: #4f8a4f;
            padding: 28px 40px;
            border-bottom: 6px solid #e8c440;
        }
        .header-bar h1 {
            color: #fff;
            font-size: 26px;
            font-weight: 700;
        }
        .page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 28px 18px 58px;
        }
        h2.section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
        }
        h3.sub-title {
            color: #3d7a3d;
            font-size: 17px;
            font-weight: 700;
            margin: 18px 0 10px;
        }
        .layout {
            display: grid;
            grid-template-columns: 3fr 1fr 1fr;
            gap: 30px;
        }
        .personal-box {
            border: 2px solid #3d8be0;
            border-radius: 6px;
            padding: 16px;
        }
        .row {
            display: grid;
            gap: 12px;
            margin-bottom: 12px;
        }
        .row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
        .row.cols-4 { grid-template-columns: 1fr 1fr 1fr 1fr; }
        .row.cols-5 { grid-template-columns: 1fr 1fr 1fr 1fr 1fr; }
        .row.cols-2 { grid-template-columns: 1fr 1fr; }
        .field label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 4px;
        }
        .field input, .field select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #bcd4ec;
            border-radius: 4px;
            font-size: 13px;
            background: #eaf3fb;
        }
        .academic-box .field input,
        .academic-box .field select {
            background: #eaf3fb;
        }
        .upload-box {
            font-size: 13px;
        }
        .upload-box input[type=file] {
            margin-top: 8px;
        }
        .upload-box .upload-group {
            margin-bottom: 18px;
        }
        .upload-box .upload-hint {
            display: block;
            font-weight: 400;
            font-size: 11.5px;
            color: #666;
            margin-top: 4px;
        }
        .certify-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-top: 24px;
            font-size: 13px;
        }
        .submit-btn {
            margin-top: 18px;
            width: 100%;
            background: #3d6e3d;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }
        .submit-btn:hover { background: #2f5a2f; }
        .alert {
            margin-bottom: 16px;
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 13px;
        }
        .alert-error {
            background: #fdeaea;
            border: 1px solid #e3b9b9;
            color: #a33;
        }
        .alert ul { margin-left: 18px; margin-top: 4px; }
        .login-link {
            margin-top: 16px;
            text-align: center;
            font-size: 13px;
        }
    </style>
</head>

<script>
const PSGC_BASE = "https://psgc.gitlab.io/api";

function initAddressCascade(provinceId, cityId, barangayId) {
    const provinceSelect = document.getElementById(provinceId);
    const citySelect = document.getElementById(cityId);
    const barangaySelect = document.getElementById(barangayId);

    // Load provinces on page load
    fetch(`${PSGC_BASE}/provinces.json`)
        .then(res => res.json())
        .then(data => {
            data.sort((a, b) => a.name.localeCompare(b.name));
            data.forEach(prov => {
                const opt = document.createElement('option');
                opt.value = prov.name;
                opt.dataset.code = prov.code;
                opt.textContent = prov.name;
                provinceSelect.appendChild(opt);
            });
        })
        .catch(err => console.error('Failed to load provinces:', err));

    // Province change → load cities/municipalities
    provinceSelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const provinceCode = selected.dataset.code;

        citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
        barangaySelect.innerHTML = '<option value="">Select City/Municipality First</option>';
        citySelect.disabled = true;
        barangaySelect.disabled = true;

        if (!provinceCode) return;

        fetch(`${PSGC_BASE}/provinces/${provinceCode}/cities-municipalities.json`)
            .then(res => res.json())
            .then(data => {
                data.sort((a, b) => a.name.localeCompare(b.name));
                data.forEach(city => {
                    const opt = document.createElement('option');
                    opt.value = city.name;
                    opt.dataset.code = city.code;
                    opt.textContent = city.name;
                    citySelect.appendChild(opt);
                });
                citySelect.disabled = false;
            })
            .catch(err => console.error('Failed to load cities:', err));
    });

    // City change → load barangays
    citySelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const cityCode = selected.dataset.code;

        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        barangaySelect.disabled = true;

        if (!cityCode) return;

        fetch(`${PSGC_BASE}/cities-municipalities/${cityCode}/barangays.json`)
            .then(res => res.json())
            .then(data => {
                data.sort((a, b) => a.name.localeCompare(b.name));
                data.forEach(brgy => {
                    const opt = document.createElement('option');
                    opt.value = brgy.name;
                    opt.textContent = brgy.name;
                    barangaySelect.appendChild(opt);
                });
                barangaySelect.disabled = false;
            })
            .catch(err => console.error('Failed to load barangays:', err));
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initAddressCascade('province', 'city_municipality', 'barangay');
    initAddressCascade('beneficiary_province', 'beneficiary_city_municipality', 'beneficiary_barangay');
});
</script>

<body>

    @php
        // Determines which flavor of the form to render.
        // 'enrollee_type' is stored in session by the /register/type page
        // (values: 'psu_student' or 'cross_enrollee').
        // Falls back to old('cadet_source_type') on validation errors, then defaults to 'psu_student'.
        $cadetSourceType = old('cadet_source_type', session('enrollee_type', 'psu_student'));
        $isCrossEnrollee = $cadetSourceType === 'cross_enrollee';
    @endphp

    <div class="header-bar">
        <h1>Alaminos-Infanta ROTC Portal</h1>
    </div>

    <div class="page">
        <h2 class="section-title">ROTC REGISTRATION</h2>

        @if ($errors->any())
            <div class="alert alert-error">
                Please fix the following:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Carries the PSU Student / Cross Enrollee choice through submission --}}
            <input type="hidden" name="cadet_source_type" value="{{ $cadetSourceType }}">

            <div class="layout">

                {{-- Personal Information --}}
                <div>
                    <h3 class="sub-title">Personal Information</h3>
                    <div class="personal-box">

                        <div class="row cols-4">
                            <div class="field">
                                <label>First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                            </div>
                            <div class="field">
                                <label>Middle Name</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name') }}">
                            </div>
                            <div class="field">
                                <label>Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                            </div>
                            <div class="field">
                                <label>Suffix</label>
                                <select name="suffix">
                                    <option value="">None</option>
                                    <option value="Jr." @selected(old('suffix')=='Jr.')>Jr.</option>
                                    <option value="Sr." @selected(old('suffix')=='Sr.')>Sr.</option>
                                    <option value="II" @selected(old('suffix')=='II')>II</option>
                                    <option value="III" @selected(old('suffix')=='III')>III</option>
                                    <option value="IV" @selected(old('suffix')=='IV')>IV</option>
                                </select>
                            </div>
                        </div>

                        <div class="row cols-4">
                            <div class="field"><label>Birthday</label><input type="date" name="birthday" value="{{ old('birthday') }}" required></div>
                            <div class="field"><label>Age</label><input type="number" name="age" value="{{ old('age') }}" required></div>
                            <div class="field">
                                <label>Sex</label>
                                <select name="sex" required>
                                    <option value="">Select</option>
                                    <option value="Male" @selected(old('sex')=='Male')>Male</option>
                                    <option value="Female" @selected(old('sex')=='Female')>Female</option>
                                </select>
                            </div>
                            <div class="field"><label>Status</label><input type="text" name="civil_status" value="{{ old('civil_status') }}" placeholder="Single"></div>
                        </div>

                        <div class="row cols-2">
                            <div class="field"><label>Religion</label><input type="text" name="religion" value="{{ old('religion') }}"></div>
                        </div>

                        <div class="row cols-5">
                            <div class="field">
                                <label>Blood type</label>
                                <select name="blood_type">
                                    <option value="">Select</option>
                                    <option value="A+" @selected(old('blood_type')=='A+')>A+</option>
                                    <option value="A-" @selected(old('blood_type')=='A-')>A-</option>
                                    <option value="B+" @selected(old('blood_type')=='B+')>B+</option>
                                    <option value="B-" @selected(old('blood_type')=='B-')>B-</option>
                                    <option value="AB+" @selected(old('blood_type')=='AB+')>AB+</option>
                                    <option value="AB-" @selected(old('blood_type')=='AB-')>AB-</option>
                                    <option value="O+" @selected(old('blood_type')=='O+')>O+</option>
                                    <option value="O-" @selected(old('blood_type')=='O-')>O-</option>
                                </select>
                            </div>
                            <div class="field"><label>Height</label><input type="number" step="0.1" name="height_cm" value="{{ old('height_cm') }}" placeholder="cm"></div>
                            <div class="field"><label>Weight</label><input type="number" step="0.1" name="weight_kg" value="{{ old('weight_kg') }}" placeholder="kg"></div>
                            <div class="field"><label>Skill 1</label><input type="text" name="skill_1" value="{{ old('skill_1') }}"></div>
                            <div class="field"><label>Skill 2</label><input type="text" name="skill_2" value="{{ old('skill_2') }}"></div>
                        </div>

                        <div class="row cols-3">
                            <div class="field">
                                <label>Province</label>
                                <select name="province" id="province" required>
                                    <option value="">Select Province</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>City/Municipality</label>
                                <select name="city_municipality" id="city_municipality" disabled required>
                                    <option value="">Select Province First</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Barangay</label>
                                <select name="barangay" id="barangay" disabled required>
                                    <option value="">Select City/Municipality First</option>
                                </select>
                            </div>
                        </div>

                        <div class="row cols-2">
                           <div class="field">
                                <label>CP NO.</label>
                                <input
                                    type="text"
                                    name="contact_no"
                                    value="{{ old('contact_no') }}"
                                    maxlength="11"
                                    pattern="^09\d{9}$"
                                    inputmode="numeric"
                                    placeholder="09XXXXXXXXX"
                                    title="Enter an 11-digit number starting with 09"
                                    required
                                >
                            </div>

                            @if ($isCrossEnrollee)
                                {{-- Cross enrollees aren't tied to a PSU campus, so this
                                     collects the name of their home school/university instead. --}}
                                <div class="field">
                                    <label>School/University</label>
                                    <input
                                        type="text"
                                        name="school_university"
                                        value="{{ old('school_university') }}"
                                        placeholder="Enter your school/university"
                                        required
                                    >
                                </div>
                            @else
                                <div class="field">
                                    <label>Campus</label>
                                    <select name="campus" required>
                                        <option value="">Select Campus</option>
                                        <option value="Infanta" @selected(old('campus')=='Infanta')>Infanta</option>
                                        <option value="Alaminos City" @selected(old('campus')=='Alaminos City')>Alaminos City</option>
                                    </select>
                                </div>
                            @endif
                        </div>

                        <h3 class="sub-title" style="margin-top:14px;">Beneficiary</h3>

                        <div class="row cols-4">
                            <div class="field"><label>Surname</label><input type="text" name="beneficiary_surname" value="{{ old('beneficiary_surname') }}"></div>
                            <div class="field"><label>First Name</label><input type="text" name="beneficiary_first_name" value="{{ old('beneficiary_first_name') }}"></div>
                            <div class="field"><label>M.I.</label><input type="text" name="beneficiary_middle_initial" value="{{ old('beneficiary_middle_initial') }}"></div>
                            <div class="field">
                                <label>Suffix</label>
                                <select name="beneficiary_suffix">
                                    <option value="">None</option>
                                    <option value="Jr." @selected(old('beneficiary_suffix')=='Jr.')>Jr.</option>
                                    <option value="Sr." @selected(old('beneficiary_suffix')=='Sr.')>Sr.</option>
                                    <option value="II" @selected(old('beneficiary_suffix')=='II')>II</option>
                                    <option value="III" @selected(old('beneficiary_suffix')=='III')>III</option>
                                    <option value="IV" @selected(old('beneficiary_suffix')=='IV')>IV</option>
                                </select>
                            </div>
                        </div>

                        <div class="row cols-2">
                            <div class="field">
                                <label>CP NO.</label>
                                <input
                                    type="text"
                                    name="beneficiary_contact_no"
                                    value="{{ old('beneficiary_contact_no') }}"
                                    maxlength="11"
                                    pattern="^09\d{9}$"
                                    inputmode="numeric"
                                    placeholder="09XXXXXXXXX"
                                    title="Enter an 11-digit number starting with 09"
                                >
                            </div>
                            <div class="field"><label>Relationship</label><input type="text" name="beneficiary_relationship" value="{{ old('beneficiary_relationship') }}"></div>
                        </div>

                        <div class="row cols-3">
                            <div class="field">
                                <label>Province</label>
                                <select name="beneficiary_province" id="beneficiary_province">
                                    <option value="">Select Province</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>City/Municipality</label>
                                <select name="beneficiary_city_municipality" id="beneficiary_city_municipality" disabled>
                                    <option value="">Select Province First</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Barangay</label>
                                <select name="beneficiary_barangay" id="beneficiary_barangay" disabled>
                                    <option value="">Select City/Municipality First</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Academic Information --}}
                <div class="academic-box">
                    <h3 class="sub-title">Academic Information</h3>

                    <div class="field" style="margin-bottom:12px;">
                        <label>Student Number</label>
                        <input type="text" name="student_number" value="{{ old('student_number') }}" required>
                    </div>

                    <div class="field" style="margin-bottom:12px;">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required>
                    </div>

                    <div class="field" style="margin-bottom:12px;">
                        <label>Course</label>
                        <input type="text" name="course" value="{{ old('course') }}">
                    </div>

                    <div class="field" style="margin-bottom:12px;">
                        <label>Year & Section</label>
                        <input type="text" name="year_section" value="{{ old('year_section') }}">
                    </div>

                    <div class="field">
                        <label>Cadet Type</label>
                        <select name="cadet_type" required>
                            <option value="">Select</option>
                            <option value="private" @selected(old('cadet_type')=='private')>Basic (Private)</option>
                            <option value="advance" @selected(old('cadet_type')=='advance')>Advance</option>
                        </select>
                    </div>
                </div>

                {{-- Upload --}}
                <div class="upload-box">
                    <h3 class="sub-title">Upload Requirements</h3>

                    <div class="upload-group">
                        <label style="font-weight:600;">2&times;2 Photo (JPG/PNG)</label><br>
                        <input type="file" name="photo" accept="image/jpeg,image/png" required>
                    </div>

                    @if ($isCrossEnrollee)
                        <div class="upload-group">
                            <label style="font-weight:600;">Certificate of Registration (COR)</label><br>
                            <input type="file" name="cor" accept="image/jpeg,image/png,application/pdf" required>
                            <span class="upload-hint">
                                Must be certified/signed by your school's Registrar. JPG, PNG, or PDF.
                            </span>
                        </div>
                    @endif

                    <div class="certify-row">
                        <input type="checkbox" name="certify" id="certify" required style="margin-top:3px;">
                        <label for="certify">
                            I certify that all information provided is true and correct.
                            I agree to ROTC rules and regulations.
                        </label>
                    </div>

                    <button type="submit" class="submit-btn">Submit Registration</button>

                    <div class="login-link">
                        Already have an account? <a href="{{ route('login') }}">Log in</a>
                    </div>
                </div>

            </div>
        </form>
    </div>

</body>
</html>