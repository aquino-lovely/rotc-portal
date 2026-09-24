<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    @include('partials.pwa-head')
    <title>Cadet Details - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        .detail-item label {
            display: block;
            font-size: 12px;
            color: #888;
            margin-bottom: 2px;
        }
        .detail-item div {
            font-size: 14px;
            font-weight: 600;
        }
        .section-label {
            color: #3d7a3d;
            font-weight: 700;
            font-size: 15px;
            margin: 20px 0 10px;
        }
        .approve-form {
            background: #f4f4f4;
            border-radius: 8px;
            padding: 16px;
            margin-top: 20px;
        }
        .approve-form select, .approve-form input {
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 13px;
        }
        .btn-reject {
            background: #a33;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-reject:hover { background: #862626; }
    </style>
</head>
@include('partials.pwa-register')
<body>

    <div class="app-shell" style="flex-direction: column;">

        <div class="top-bar">
            <h1>Alaminos-Infanta ROTC Portal</h1>
            <div class="top-bar-right">
                <div class="user-chip">
                    <div class="avatar">&#128100;</div>
                    <div>
                        {{ auth()->user()->name ?? 'Admin' }}<br>
                        <small style="opacity:0.85;">{{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? '')) }}</small>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-link" style="background:none; border:none; cursor:pointer;">
                        &#8650; Log Out
                    </button>
                </form>
            </div>
        </div>

        <div class="body-row">

             @include('partials.sidebar')


            <div class="main-content">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="page-title">Cadet Details</div>
                <div class="page-subtitle">
                    <a href="{{ route('admin.cadets.index') }}" style="color:#3d7a3d;">&larr; Back to Cadet Profile</a>
                </div>

                @php($cadet = $user->cadet)
                @php($beneficiary = $cadet->beneficiary ?? null)

                <div class="card">

                    <div class="section-label">Personal Information</div>
                    <div class="detail-grid">
                        <div class="detail-item"><label>Full Name</label><div>{{ $cadet->first_name }} {{ $cadet->middle_name }} {{ $cadet->last_name }}</div></div>
                        <div class="detail-item"><label>Birthday</label><div>{{ $cadet->birthday?->format('M d, Y') }}</div></div>
                        <div class="detail-item"><label>Age</label><div>{{ $cadet->age }}</div></div>
                        <div class="detail-item"><label>Sex</label><div>{{ $cadet->sex }}</div></div>
                        <div class="detail-item"><label>Civil Status</label><div>{{ $cadet->civil_status }}</div></div>
                        <div class="detail-item"><label>Religion</label><div>{{ $cadet->religion ?? '—' }}</div></div>
                        <div class="detail-item"><label>Blood Type</label><div>{{ $cadet->blood_type ?? '—' }}</div></div>
                        <div class="detail-item"><label>Height / Weight</label><div>{{ $cadet->height_cm ?? '—' }} cm / {{ $cadet->weight_kg ?? '—' }} kg</div></div>
                        <div class="detail-item"><label>Skills</label><div>{{ $cadet->skill_1 ?? '—' }}{{ $cadet->skill_2 ? ', '.$cadet->skill_2 : '' }}</div></div>
                        <div class="detail-item"><label>Address</label><div>{{ $cadet->barangay }}, {{ $cadet->city_municipality }}, {{ $cadet->province }}</div></div>
                        <div class="detail-item"><label>Contact No.</label><div>{{ $cadet->contact_no }}</div></div>
                        <div class="detail-item"><label>Campus</label><div>{{ $cadet->campus }}</div></div>
                    </div>

                    <div class="section-label">Academic Information</div>
                    <div class="detail-grid">
                        <div class="detail-item"><label>Student Number</label><div>{{ $cadet->student_number }}</div></div>
                        <div class="detail-item"><label>Email</label><div>{{ $user->email }}</div></div>
                        <div class="detail-item"><label>Course</label><div>{{ $cadet->course }}</div></div>
                        <div class="detail-item"><label>Year & Section</label><div>{{ $cadet->year_section }}</div></div>
                        <div class="detail-item"><label>Rank</label><div>{{ $cadet->rank }}</div></div>
                        <div class="detail-item"><label>Company</label><div>{{ $cadet->company ?? '—' }}</div></div>
                    </div>

                    @if ($beneficiary)
                        <div class="section-label">Beneficiary</div>
                        <div class="detail-grid">
                            <div class="detail-item"><label>Name</label><div>{{ $beneficiary->surname }}, {{ $beneficiary->first_name }} {{ $beneficiary->middle_initial }}</div></div>
                            <div class="detail-item"><label>Relationship</label><div>{{ $beneficiary->relationship }}</div></div>
                            <div class="detail-item"><label>Contact No.</label><div>{{ $beneficiary->contact_no }}</div></div>
                            <div class="detail-item"><label>Address</label><div>{{ $beneficiary->barangay }}, {{ $beneficiary->city_municipality }}, {{ $beneficiary->province }}</div></div>
                        </div>
                    @endif

                    @if ($cadet->photo_path)
                        <div class="section-label">2x2 Photo</div>
                        <img src="{{ asset('storage/' . $cadet->photo_path) }}" alt="Cadet photo" style="width:120px; height:120px; object-fit:cover; border-radius:6px; border:1px solid #ddd;">
                    @endif

                    @if ($user->status === 'pending')
                        <div class="approve-form">
                            <div class="section-label" style="margin-top:0;">Approve Registration</div>

                            <form method="POST" action="{{ route('admin.cadets.approve', $user) }}" style="display:flex; gap:12px; align-items:end; flex-wrap:wrap;">
                                @csrf
                                <div>
                                    <label style="display:block; font-size:12px; margin-bottom:4px;">Company</label>
                                    <select name="company" required>
                                        <option value="">Select Company</option>
                                        <option value="Alpha">Alpha</option>
                                        <option value="Bravo">Bravo</option>
                                        <option value="Charlie">Charlie</option>
                                        <option value="Delta">Delta</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="display:block; font-size:12px; margin-bottom:4px;">Rank</label>
                                    <input type="text" name="rank" value="Private" placeholder="Private">
                                </div>
                                <button type="submit" class="btn-green">Approve & Send Credentials</button>
                            </form>

                            <form method="POST" action="{{ route('admin.cadets.reject', $user) }}" style="margin-top:12px;" onsubmit="return confirm('Are you sure you want to reject this registration?');">
                                @csrf
                                <button type="submit" class="btn-reject">Reject Registration</button>
                            </form>
                        </div>
                    @elseif ($user->status === 'approved')
                        <div class="alert alert-success" style="margin-top:20px;">
                            This cadet has been approved.
                        </div>
                    @else
                        <div class="alert alert-error" style="margin-top:20px;">
                            This registration was rejected.
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

</body>
</html>