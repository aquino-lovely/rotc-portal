<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cadet Details - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 24px; margin-bottom: 20px; }
        .info-item label { display: block; font-size: 12px; font-weight: 600; color: #666; margin-bottom: 3px; }
        .info-value { background: #eaf3fb; border: 1px solid #bcd4ec; border-radius: 4px; padding: 7px 10px; font-size: 13px; }
        .section-title { font-size: 15px; font-weight: 700; color: #3d6e3d; margin: 20px 0 12px; }
        .cols-3 { grid-template-columns: 1fr 1fr 1fr; }
        .cols-5 { grid-template-columns: 1fr 1fr 1fr 1fr 1fr; }
        .avatar-circle { width: 80px; height: 80px; border-radius: 50%; background: #ccc; display: flex; align-items: center; justify-content: center; font-size: 40px; overflow: hidden; margin-bottom: 12px; }
        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
    </style>
</head>
<body>

<div class="app-shell" style="flex-direction: column;">

    <div class="top-bar">
        <h1>Alaminos-Infanta ROTC Portal</h1>
        <div class="top-bar-right">
            <div class="user-chip">
                <div class="avatar">&#128100;</div>
                <div>
                    {{ auth()->user()->name }}<br>
                    <small style="opacity:0.85;">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</small>
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
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @if (session('warning'))
    <div style="background:#fef3c7; border:1px solid #f59e0b; color:#92400e; padding:12px 16px; border-radius:8px; margin-bottom:16px;">
        {{ session('warning') }}
        @if (session('temporary_password'))
            <br>Temporary password: <strong>{{ session('temporary_password') }}</strong>
        @endif
    </div>
@endif

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <div>
                    <div class="page-title">Cadet Details</div>
                    <div class="page-subtitle">Full profile and approval actions</div>
                </div>
                <a href="{{ route('admin.cadets.index') }}" class="btn-green">← Back to List</a>
            </div>

            <div style="display:flex; gap:24px; align-items:flex-start;">

                {{-- Left: photo + quick info --}}
                <div style="width:200px; flex-shrink:0;">
                    <div class="card" style="text-align:center; padding:20px 16px;">
                        <div class="avatar-circle" style="margin:0 auto 12px;">
                            @if($user->cadet?->photo_path)
                                <img src="{{ asset('storage/' . $user->cadet->photo_path) }}" alt="Photo">
                            @else
                                &#128100;
                            @endif
                        </div>
                        <div style="font-weight:700; font-size:15px;">
                            {{ $user->cadet?->last_name }}, {{ $user->cadet?->first_name }}
                        </div>
                        <div style="font-size:12px; color:#666; margin:4px 0;">{{ $user->username }}</div>
                        <span class="status-badge status-{{ $user->status }}" style="margin-top:8px; display:inline-block;">
                            {{ ucfirst($user->status) }}
                        </span>

                        <div style="margin-top:16px; font-size:12px; color:#666;">Student No.</div>
                        <div style="font-size:13px; font-weight:600;">{{ $user->cadet?->student_number ?? '—' }}</div>

                        <div style="margin-top:8px; font-size:12px; color:#666;">Rank</div>
                        <div style="font-size:13px; font-weight:600;">{{ $user->cadet?->rank ?? '—' }}</div>

                        <div style="margin-top:8px; font-size:12px; color:#666;">Company</div>
                        <div style="font-size:13px; font-weight:600;">{{ $user->cadet?->company ?? '—' }}</div>

                        <div style="margin-top:8px; font-size:12px; color:#666;">Cadet Type</div>
                        <div style="font-size:13px; font-weight:600;">{{ ucfirst($user->cadet?->cadet_type ?? '—') }}</div>
                    </div>
                </div>

                {{-- Right: full details --}}
                <div style="flex:1;">
                    <div class="card">

                        {{-- Approve/Reject form (only if pending) --}}
                        @if($user->status === 'pending')
                        <div style="background:#fdf8e1; border:1px solid #e8c440; border-radius:8px; padding:16px; margin-bottom:20px;">
                            <div style="font-weight:700; font-size:14px; margin-bottom:12px; color:#9a7b1a;">
                                ⏳ Pending Approval
                            </div>
                            <form method="POST" action="{{ route('admin.cadets.approve', $user) }}">
                                @csrf
                                <button type="submit" class="btn-green">✔ Approve</button>
                            </form>

                            <form method="POST" action="{{ route('admin.cadets.reject', $user) }}"
                                  style="margin-top:10px; display:flex; gap:8px;">
                                @csrf
                                <input type="text" name="reason" placeholder="Rejection reason (optional)"
                                       style="flex:1; padding:8px; border:1px solid #ddd; border-radius:6px; font-size:13px;">
                                <button type="submit"
                                        style="background:#a33; color:#fff; border:none; padding:8px 16px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer;">
                                    ✖ Reject
                                </button>
                            </form>
                        </div>
                        @endif

                        {{-- Personal Info --}}
                        <div class="section-title">Personal Information</div>
                        <div class="info-grid cols-3">
                            <div class="info-item"><label>First Name</label><div class="info-value">{{ $user->cadet?->first_name ?? '—' }}</div></div>
                            <div class="info-item"><label>Middle Name</label><div class="info-value">{{ $user->cadet?->middle_name ?? '—' }}</div></div>
                            <div class="info-item"><label>Last Name</label><div class="info-value">{{ $user->cadet?->last_name ?? '—' }}</div></div>
                        </div>
                        <div class="info-grid cols-5">
                            <div class="info-item"><label>Birthday</label><div class="info-value">{{ $user->cadet?->birthday?->format('m/d/Y') ?? '—' }}</div></div>
                            <div class="info-item"><label>Age</label><div class="info-value">{{ $user->cadet?->age ?? '—' }}</div></div>
                            <div class="info-item"><label>Sex</label><div class="info-value">{{ $user->cadet?->sex ?? '—' }}</div></div>
                            <div class="info-item"><label>Status</label><div class="info-value">{{ $user->cadet?->civil_status ?? '—' }}</div></div>
                            <div class="info-item"><label>Religion</label><div class="info-value">{{ $user->cadet?->religion ?? '—' }}</div></div>
                        </div>
                        <div class="info-grid cols-5">
                            <div class="info-item"><label>Blood Type</label><div class="info-value">{{ $user->cadet?->blood_type ?? '—' }}</div></div>
                            <div class="info-item"><label>Height</label><div class="info-value">{{ $user->cadet?->height_cm ? $user->cadet->height_cm.'cm' : '—' }}</div></div>
                            <div class="info-item"><label>Weight</label><div class="info-value">{{ $user->cadet?->weight_kg ? $user->cadet->weight_kg.'kg' : '—' }}</div></div>
                            <div class="info-item"><label>Skill 1</label><div class="info-value">{{ $user->cadet?->skill_1 ?? '—' }}</div></div>
                            <div class="info-item"><label>Skill 2</label><div class="info-value">{{ $user->cadet?->skill_2 ?? '—' }}</div></div>
                        </div>
                        <div class="info-grid cols-3">
                            <div class="info-item"><label>Barangay</label><div class="info-value">{{ $user->cadet?->barangay ?? '—' }}</div></div>
                            <div class="info-item"><label>City/Municipality</label><div class="info-value">{{ $user->cadet?->city_municipality ?? '—' }}</div></div>
                            <div class="info-item"><label>Province</label><div class="info-value">{{ $user->cadet?->province ?? '—' }}</div></div>
                        </div>
                        <div class="info-grid cols-3">
                            <div class="info-item"><label>CP No.</label><div class="info-value">{{ $user->cadet?->contact_no ?? '—' }}</div></div>
                            <div class="info-item"><label>Campus</label><div class="info-value">{{ $user->cadet?->campus ?? '—' }}</div></div>
                            <div class="info-item"><label>Email</label><div class="info-value">{{ $user->email }}</div></div>
                        </div>

                        {{-- Academic Info --}}
                        <div class="section-title">Academic Information</div>
                        <div class="info-grid cols-3">
                            <div class="info-item"><label>Student Number</label><div class="info-value">{{ $user->cadet?->student_number ?? '—' }}</div></div>
                            <div class="info-item"><label>Course</label><div class="info-value">{{ $user->cadet?->course ?? '—' }}</div></div>
                            <div class="info-item"><label>Year & Section</label><div class="info-value">{{ $user->cadet?->year_section ?? '—' }}</div></div>
                        </div>

                        {{-- Beneficiary --}}
                        @if($user->cadet?->beneficiary)
                        <div class="section-title">Beneficiary</div>
                        <div class="info-grid cols-3">
                            <div class="info-item"><label>Surname</label><div class="info-value">{{ $user->cadet->beneficiary->surname ?? '—' }}</div></div>
                            <div class="info-item"><label>First Name</label><div class="info-value">{{ $user->cadet->beneficiary->first_name ?? '—' }}</div></div>
                            <div class="info-item"><label>M.I.</label><div class="info-value">{{ $user->cadet->beneficiary->middle_initial ?? '—' }}</div></div>
                        </div>
                        <div class="info-grid cols-2">
                            <div class="info-item"><label>CP No.</label><div class="info-value">{{ $user->cadet->beneficiary->contact_no ?? '—' }}</div></div>
                            <div class="info-item"><label>Relationship</label><div class="info-value">{{ $user->cadet->beneficiary->relationship ?? '—' }}</div></div>
                        </div>
                        <div class="info-grid cols-3">
                            <div class="info-item"><label>Barangay</label><div class="info-value">{{ $user->cadet->beneficiary->barangay ?? '—' }}</div></div>
                            <div class="info-item"><label>City/Municipality</label><div class="info-value">{{ $user->cadet->beneficiary->city_municipality ?? '—' }}</div></div>
                            <div class="info-item"><label>Province</label><div class="info-value">{{ $user->cadet->beneficiary->province ?? '—' }}</div></div>
                        </div>

                           <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:12px;"> 

                                                </div>
                        @endif  {{-- closes beneficiary @if --}}

                                               {{-- Rank & Company assignment --}}
                        <div class="section-title">Rank &amp; Company Assignment</div>
                        <form method="POST" action="{{ route('admin.cadets.rank-company', $user) }}">
                            @csrf
                            <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:12px;">

                                <div style="flex:1;">
                                    <label style="font-size:12px; font-weight:600; display:block; margin-bottom:4px;">Rank</label>
                                    <select name="rank" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; font-size:13px;">
                                        <option value="">— No change —</option>
                                        @foreach(['Cdt. 2nd LT','Cdt. 1st LT'] as $r)
                                            <option value="{{ $r }}" @selected($user->cadet?->rank === $r)>{{ $r }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div style="flex:1;">
                                    <label style="font-size:12px; font-weight:600; display:block; margin-bottom:4px;">Company</label>
                                    @php $knownCompanies = ['Alpha','Bravo','Charlie','Delta']; @endphp
                                    <select name="company" id="companySelect" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; font-size:13px;">
                                        <option value="">— No change —</option>
                                        @foreach($knownCompanies as $c)
                                            <option value="{{ $c }}" @selected($user->cadet?->company === $c)>{{ $c }}</option>
                                        @endforeach
                                        <option value="__other__" @selected($user->cadet?->company && !in_array($user->cadet->company, $knownCompanies))>
                                            Other (new company)
                                        </option>
                                    </select>
                                </div>

                                <div style="flex:1; display:none;" id="newCompanyWrapper">
                                    <label style="font-size:12px; font-weight:600; display:block; margin-bottom:4px;">New Company Name</label>
                                    <input type="text" name="new_company" id="newCompanyInput"
                                           placeholder="e.g. Echo"
                                           value="{{ old('new_company', ($user->cadet?->company && !in_array($user->cadet->company, $knownCompanies)) ? $user->cadet->company : '') }}"
                                           style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; font-size:13px;">
                                </div>

                                <div style="flex:1;" id="platoonWrapper">
                                    <label style="font-size:12px; font-weight:600; display:block; margin-bottom:4px;">Platoon</label>
                                    <select name="platoon" id="platoonSelect" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; font-size:13px;">
                                        <option value="">— No change —</option>
                                        <option value="First Platoon" @selected($user->cadet?->platoon === 'First Platoon')>First Platoon</option>
                                        <option value="Second Platoon" @selected($user->cadet?->platoon === 'Second Platoon')>Second Platoon</option>
                                    </select>
                                    <div id="platoonHint" style="font-size:11px; color:#888; margin-top:3px;"></div>
                                </div>

                            </div>

                            <button type="submit" class="btn-green">Save Rank &amp; Company</button>
                        </form>

                        <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const companySelect     = document.getElementById('companySelect');
                            const newCompanyWrapper = document.getElementById('newCompanyWrapper');
                            const newCompanyInput   = document.getElementById('newCompanyInput');
                            const platoonWrapper    = document.getElementById('platoonWrapper');
                            const platoonSelect     = document.getElementById('platoonSelect');
                            const platoonHint       = document.getElementById('platoonHint');

                            function refresh() {
                                const isOther = companySelect.value === '__other__';
                                const hasCompany = companySelect.value !== '' && companySelect.value !== '__other__';

                                newCompanyWrapper.style.display = isOther ? 'block' : 'none';
                                newCompanyInput.required = isOther;

                                const needsPlatoon = hasCompany || isOther;
                                platoonWrapper.style.display = needsPlatoon ? 'block' : 'none';
                                platoonSelect.required = needsPlatoon;
                                platoonHint.textContent = needsPlatoon ? 'Required. Max 37 cadets per platoon.' : '';

                                if (!needsPlatoon) platoonSelect.value = '';
                            }

                            companySelect.addEventListener('change', refresh);
                            refresh();
                        });
                        </script>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>