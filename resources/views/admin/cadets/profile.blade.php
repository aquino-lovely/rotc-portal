<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .profile-sidebar {
            width: 220px;
            flex-shrink: 0;
        }
        .avatar-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            margin: 0 auto 12px;
            overflow: hidden;
        }
        .avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-name {
            font-size: 16px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2px;
        }
        .profile-label {
            font-size: 12px;
            color: #666;
            text-align: center;
            margin-bottom: 8px;
        }
        .info-pill {
            background: #eaf3fb;
            border: 1px solid #bcd4ec;
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 13px;
            margin-bottom: 8px;
            text-align: center;
        }
        .serial-pill {
            background: #fdf8e1;
            border: 1px solid #e8c440;
            border-radius: 4px;
            padding: 8px 10px;
            font-size: 12px;
            text-align: center;
            color: #9a7b1a;
        }
        .serial-pill .serial-note {
            font-size: 11px;
            color: #b8940a;
            margin-top: 3px;
        }
        .section-card {
            background: #fff;
            border-radius: 8px;
            padding: 24px 28px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            flex: 1;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .section-header h2 {
            font-size: 20px;
            font-weight: 700;
        }
        .field-grid {
            display: grid;
            gap: 10px 16px;
            margin-bottom: 16px;
        }
        .cols-3 { grid-template-columns: 1fr 1fr 1fr; }
        .cols-5 { grid-template-columns: 1fr 1fr 1fr 1fr 1fr; }
        .cols-4 { grid-template-columns: 1fr 1fr 1fr 1fr; }
        .cols-2 { grid-template-columns: 1fr 1fr; }
        .field-item label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #444;
            margin-bottom: 3px;
        }
        .field-value {
            background: #eaf3fb;
            border: 1px solid #bcd4ec;
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 13px;
            min-height: 32px;
        }
        .beneficiary-title {
            color: #3d7a3d;
            font-size: 15px;
            font-weight: 700;
            margin: 20px 0 12px;
        }
        .content-row {
            display: flex;
            gap: 24px;
            align-items: flex-start;
        }
    </style>
</head>
<body>

<div class="app-shell" style="flex-direction: column;">

    {{-- Top bar --}}
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

        {{-- Main --}}
        <div class="main-content">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="page-title">My Profile</div>
            <div class="page-subtitle">Your personal and academic information</div>

            <div class="content-row">

                {{-- Left: photo + quick info --}}
                <div class="profile-sidebar">
                    <div class="card" style="text-align:center; padding: 20px 16px;">

                        <div class="avatar-circle">
                            @if($cadet && $cadet->photo_path)
                                <img src="{{ asset('storage/' . $cadet->photo_path) }}" alt="Photo">
                            @else
                                &#128100;
                            @endif
                        </div>

                        <div class="profile-name">
                            {{ $cadet ? $cadet->last_name . ', ' . $cadet->first_name : $user->name }}
                        </div>
                        <div class="profile-label">Student Number</div>
                        <div class="info-pill">{{ $cadet->student_number ?? '—' }}</div>

                        <div class="profile-label" style="margin-top:8px;">Email</div>
                        <div class="info-pill" style="font-size:11px; word-break:break-all;">
                            {{ $user->email }}
                        </div>

                        <div style="margin-top:14px; font-size:12px; font-weight:600; color:#444; margin-bottom:6px;">
                            Academic Info
                        </div>
                        <div class="profile-label">Course</div>
                        <div class="info-pill">{{ $cadet->course ?? '—' }}</div>

                        <div class="profile-label">Year &amp; Section</div>
                        <div class="info-pill">{{ $cadet->year_section ?? '—' }}</div>

                        <div style="margin-top:14px; font-size:12px; font-weight:600; color:#444; margin-bottom:6px;">
                            Serial Number
                        </div>
                        @if($cadet && $cadet->rank)
                            <div class="info-pill">{{ $cadet->rank }}</div>
                        @else
                            <div class="serial-pill">
                                Not yet assigned
                                <div class="serial-note">Your serial number will be assigned once available</div>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Right: full info --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2>Personal Information</h2>
                        {{-- Edit button placeholder for future --}}
                        {{-- <a href="#" class="btn-green">Edit Info</a> --}}
                    </div>

                    {{-- Name --}}
                    <div class="field-grid cols-3">
                        <div class="field-item">
                            <label>First Name</label>
                            <div class="field-value">{{ $cadet->first_name ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>Middle Name</label>
                            <div class="field-value">{{ $cadet->middle_name ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>Last Name</label>
                            <div class="field-value">{{ $cadet->last_name ?? '—' }}</div>
                        </div>
                    </div>

                    {{-- Birthday / Age / Sex / Status / Religion --}}
                    <div class="field-grid cols-5">
                        <div class="field-item">
                            <label>Birthday</label>
                            <div class="field-value">
                                {{ $cadet && $cadet->birthday ? \Carbon\Carbon::parse($cadet->birthday)->format('m/d/Y') : '—' }}
                            </div>
                        </div>
                        <div class="field-item">
                            <label>Age</label>
                            <div class="field-value">{{ $cadet->age ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>Sex</label>
                            <div class="field-value">{{ $cadet->sex ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>Status</label>
                            <div class="field-value">{{ $cadet->civil_status ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>Religion</label>
                            <div class="field-value">{{ $cadet->religion ?? '—' }}</div>
                        </div>
                    </div>

                    {{-- Blood / Height / Weight / Skills --}}
                    <div class="field-grid cols-5">
                        <div class="field-item">
                            <label>Blood type</label>
                            <div class="field-value">{{ $cadet->blood_type ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>Height</label>
                            <div class="field-value">
                                {{ $cadet && $cadet->height_cm ? $cadet->height_cm . 'cm' : '—' }}
                            </div>
                        </div>
                        <div class="field-item">
                            <label>Weight</label>
                            <div class="field-value">
                                {{ $cadet && $cadet->weight_kg ? $cadet->weight_kg . 'kg' : '—' }}
                            </div>
                        </div>
                        <div class="field-item">
                            <label>Skill 1</label>
                            <div class="field-value">{{ $cadet->skill_1 ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>Skill 2</label>
                            <div class="field-value">{{ $cadet->skill_2 ?? '—' }}</div>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="field-grid cols-3">
                        <div class="field-item">
                            <label>Barangay</label>
                            <div class="field-value">{{ $cadet->barangay ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>City/Municipality</label>
                            <div class="field-value">{{ $cadet->city_municipality ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>Province</label>
                            <div class="field-value">{{ $cadet->province ?? '—' }}</div>
                        </div>
                    </div>

                    {{-- Contact / Campus / Rank --}}
                    <div class="field-grid cols-3">
                        <div class="field-item">
                            <label>CP NO.</label>
                            <div class="field-value">{{ $cadet->contact_no ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>Campus</label>
                            <div class="field-value">{{ $cadet->campus ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>Rank</label>
                            <div class="field-value">{{ $cadet->rank ?? 'Not yet assigned' }}</div>
                        </div>
                    </div>

                    {{-- Beneficiary --}}
                    @if($cadet && $cadet->beneficiary)
                    <div class="beneficiary-title">Beneficiary</div>

                    <div class="field-grid cols-3">
                        <div class="field-item">
                            <label>Surname</label>
                            <div class="field-value">{{ $cadet->beneficiary->surname ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>First Name</label>
                            <div class="field-value">{{ $cadet->beneficiary->first_name ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>M.I.</label>
                            <div class="field-value">{{ $cadet->beneficiary->middle_initial ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="field-grid cols-2">
                        <div class="field-item">
                            <label>CP NO.</label>
                            <div class="field-value">{{ $cadet->beneficiary->contact_no ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>Relationship</label>
                            <div class="field-value">{{ $cadet->beneficiary->relationship ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="field-grid cols-3">
                        <div class="field-item">
                            <label>Barangay</label>
                            <div class="field-value">{{ $cadet->beneficiary->barangay ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>City/Municipality</label>
                            <div class="field-value">{{ $cadet->beneficiary->city_municipality ?? '—' }}</div>
                        </div>
                        <div class="field-item">
                            <label>Province</label>
                            <div class="field-value">{{ $cadet->beneficiary->province ?? '—' }}</div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>