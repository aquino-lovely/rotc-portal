<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .edit-card { background:#fff; border-radius:8px; padding:28px; box-shadow:0 1px 3px rgba(0,0,0,0.08); }
        .field-group { margin-bottom:16px; }
        .field-group label { display:block; font-size:13px; font-weight:600; color:#444; margin-bottom:5px; }
        .field-group input, .field-group select {
            width:100%; padding:9px 12px; border:1px solid #ddd;
            border-radius:6px; font-size:14px; background:#fff;
        }
        .field-group input:focus, .field-group select:focus {
            outline:none; border-color:#3d6e3d;
        }
        .field-group .locked {
            background:#f5f5f5; color:#888; cursor:not-allowed;
        }
        .section-title { font-size:15px; font-weight:700; color:#3d6e3d; margin:24px 0 12px; }
        .grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .grid-5 { display:grid; grid-template-columns:1fr 1fr 1fr 1fr 1fr; gap:16px; }
        .avatar-preview {
            width:90px; height:90px; border-radius:50%; background:#ccc;
            display:flex; align-items:center; justify-content:center;
            font-size:40px; overflow:hidden; margin-bottom:12px;
        }
        .avatar-preview img { width:100%; height:100%; object-fit:cover; }
        .locked-note { font-size:11px; color:#aaa; margin-top:3px; }
        .action-row { display:flex; justify-content:flex-end; gap:12px; margin-top:24px; }
        .btn-cancel { background:#aaa; color:#fff; border:none; padding:10px 24px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-block; }
    </style>
</head>
<body>

<div class="app-shell" style="flex-direction:column;">

    <div class="top-bar">
        <h1>Alaminos-Infanta ROTC Portal</h1>
        <div class="top-bar-right">
            <div class="user-chip">
                <div class="avatar">&#128100;</div>
                <div>
                    {{ $user->name }}<br>
                    <small style="opacity:0.85;">Cadet {{ $cadet->rank ?? '' }}</small>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-link" style="background:none;border:none;cursor:pointer;">
                    &#8650; Log Out
                </button>
            </form>
        </div>
    </div>

    <div class="body-row">
       @include('partials.sidebar')

        <div class="main-content">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="page-title">Edit Profile</div>
            <div class="page-subtitle">Update your personal information</div>

            <form method="POST" action="{{ route('cadet.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="edit-card">

                    {{-- Photo --}}
                    <div class="section-title">Profile Photo</div>
                    <div style="display:flex; align-items:center; gap:20px; margin-bottom:8px;">
                        <div class="avatar-preview" id="avatarPreview">
                            @if($cadet->photo_path)
                                <img src="{{ asset('storage/' . $cadet->photo_path) }}"
                                     alt="Photo" id="previewImg">
                            @else
                                <span id="previewEmoji">&#128100;</span>
                            @endif
                        </div>
                        <div>
                            <input type="file" name="photo" id="photoInput"
                                   accept="image/jpeg,image/png"
                                   style="font-size:13px;"
                                   onchange="previewPhoto(this)">
                            <div style="font-size:12px; color:#888; margin-top:4px;">JPG or PNG, max 2MB</div>
                            @error('photo')
                                <p style="color:#a33; font-size:12px; margin-top:4px;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Locked fields (read-only) --}}
                    <div class="section-title">Personal Information <span style="font-size:12px; color:#aaa; font-weight:400;">(locked fields cannot be edited)</span></div>

                    <div class="grid-3">
                        <div class="field-group">
                            <label>First Name</label>
                            <input type="text" value="{{ $cadet->first_name }}" class="locked" readonly>
                            <div class="locked-note">🔒 Locked</div>
                        </div>
                        <div class="field-group">
                            <label>Middle Name</label>
                            <input type="text" value="{{ $cadet->middle_name }}" class="locked" readonly>
                            <div class="locked-note">🔒 Locked</div>
                        </div>
                        <div class="field-group">
                            <label>Last Name</label>
                            <input type="text" value="{{ $cadet->last_name }}" class="locked" readonly>
                            <div class="locked-note">🔒 Locked</div>
                        </div>
                    </div>

                    <div class="grid-5">
                        <div class="field-group">
                            <label>Birthday</label>
                            <input type="text" value="{{ $cadet->birthday?->format('m/d/Y') }}" class="locked" readonly>
                            <div class="locked-note">🔒 Locked</div>
                        </div>
                        <div class="field-group">
                            <label>Age</label>
                            <input type="text" value="{{ $cadet->age }}" class="locked" readonly>
                            <div class="locked-note">🔒 Locked</div>
                        </div>
                        <div class="field-group">
                            <label>Sex</label>
                            <input type="text" value="{{ $cadet->sex }}" class="locked" readonly>
                            <div class="locked-note">🔒 Locked</div>
                        </div>
                        <div class="field-group">
                            <label>Civil Status</label>
                            <input type="text" name="civil_status"
                                   value="{{ old('civil_status', $cadet->civil_status) }}">
                            @error('civil_status')
                                <p style="color:#a33; font-size:12px;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="field-group">
                            <label>Religion</label>
                            <input type="text" name="religion"
                                   value="{{ old('religion', $cadet->religion) }}">
                            @error('religion')
                                <p style="color:#a33; font-size:12px;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid-5">
                        <div class="field-group">
                            <label>Blood Type</label>
                            <input type="text" value="{{ $cadet->blood_type }}" class="locked" readonly>
                            <div class="locked-note">🔒 Locked</div>
                        </div>
                        <div class="field-group">
                            <label>Height</label>
                            <input type="text" value="{{ $cadet->height_cm }}cm" class="locked" readonly>
                            <div class="locked-note">🔒 Locked</div>
                        </div>
                        <div class="field-group">
                            <label>Weight</label>
                            <input type="text" value="{{ $cadet->weight_kg }}kg" class="locked" readonly>
                            <div class="locked-note">🔒 Locked</div>
                        </div>
                        <div class="field-group">
                            <label>Skill 1</label>
                            <input type="text" name="skill_1"
                                   value="{{ old('skill_1', $cadet->skill_1) }}">
                        </div>
                        <div class="field-group">
                            <label>Skill 2</label>
                            <input type="text" name="skill_2"
                                   value="{{ old('skill_2', $cadet->skill_2) }}">
                        </div>
                    </div>

                    {{-- Editable address --}}
                    <div class="section-title">Address</div>
                    <div class="grid-3">
                        <div class="field-group">
                            <label>Barangay</label>
                            <input type="text" name="barangay"
                                   value="{{ old('barangay', $cadet->barangay) }}">
                            @error('barangay')
                                <p style="color:#a33; font-size:12px;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="field-group">
                            <label>City/Municipality</label>
                            <input type="text" name="city_municipality"
                                   value="{{ old('city_municipality', $cadet->city_municipality) }}">
                            @error('city_municipality')
                                <p style="color:#a33; font-size:12px;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="field-group">
                            <label>Province</label>
                            <input type="text" name="province"
                                   value="{{ old('province', $cadet->province) }}">
                            @error('province')
                                <p style="color:#a33; font-size:12px;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="section-title">Contact</div>
                    <div class="grid-2">
                        <div class="field-group">
                            <label>CP No.</label>
                            <input type="text" name="contact_no"
                                   value="{{ old('contact_no', $cadet->contact_no) }}">
                            @error('contact_no')
                                <p style="color:#a33; font-size:12px;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="field-group">
                            <label>Campus</label>
                            <input type="text" value="{{ $cadet->campus }}" class="locked" readonly>
                            <div class="locked-note">🔒 Locked</div>
                        </div>
                    </div>

                    <div class="action-row">
                        <a href="{{ route('cadet.profile') }}" class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-green" style="padding:10px 28px; font-size:14px;">
                            Save Changes
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>