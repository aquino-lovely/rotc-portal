<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrollment Processing - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
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

            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px;">
                <div>
                    <div class="page-title">Enrollment Processing</div>
                    <div class="page-subtitle">Review and process cadet enrollment applications</div>
                </div>
                @if(auth()->user()->role === 'S1')
                <a href="{{ route('admin.enrollment.settings') }}" class="btn-green">
                    ⚙ Enrollment Settings
                </a>
                @endif
            </div>

            @if($setting)
                <div style="margin-bottom:16px; font-size:13px; color:#2f5a2f; font-weight:600;">
                    Active Period: {{ $setting->semester }} {{ $setting->school_year }}
                    <span style="margin-left:10px; padding:2px 10px; border-radius:10px; background:#e3f1e3; font-size:12px;">
                        {{ $setting->is_open ? 'Open' : 'Closed' }}
                    </span>
                </div>
            @else
                <div class="alert alert-error" style="margin-bottom:16px;">
                    No active enrollment period. Go to Enrollment Settings to open one.
                </div>
            @endif

            <div class="card">
                <div class="search-row">
                    <form method="GET" action="{{ route('admin.enrollment.index') }}" style="display:flex; gap:10px; flex:1;">
                        <input type="text" name="search" class="search-input"
                               placeholder="Search by name, ID, course"
                               value="{{ request('search') }}">
                        <button type="submit" class="btn-green">Search</button>
                    </form>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>STUDENT ID</th>
                            <th>NAME</th>
                            <th>COURSE</th>
                            <th>YEAR</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enrollment)
                        <tr>
                            <td>{{ $enrollment->user->cadet->student_number ?? '—' }}</td>
                            <td>{{ $enrollment->user->cadet->last_name ?? '' }}, {{ $enrollment->user->cadet->first_name ?? '' }}</td>
                            <td>{{ $enrollment->user->cadet->course ?? '—' }}</td>
                            <td>{{ $enrollment->user->cadet->year_section ?? '—' }}</td>
                            <td>
                                <span class="status-badge status-{{ $enrollment->status }}">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                                @if($enrollment->enrolled_manually)
                                    <span style="margin-left:6px; padding:2px 8px; border-radius:8px; background:#fff0c2; color:#7a5c00; font-size:11px; font-weight:600;">
                                        Late/Manual
                                    </span>
                                @endif
                            </td>
                            <td style="display:flex; gap:8px;">
                                <a href="{{ route('admin.enrollment.show', $enrollment) }}"
                                   class="btn-green" style="font-size:12px; padding:6px 12px;">
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:#666; padding:30px;">
                                No enrollment submissions yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div style="margin-top:16px;">
                    {{ $enrollments->withQueryString()->links() }}
                </div>
            </div>

            {{-- Manual / Late Enrollment (collapsed by default, sits below the main table) --}}
            @if($setting)
            <details style="margin-top:24px;" {{ request('late_search') ? 'open' : '' }}>
                <summary style="cursor:pointer; padding:10px 16px; background:#fff8e6; border:1px solid #f0d98c; border-radius:8px; font-weight:700; font-size:14px; color:#7a5c00; list-style:none;">
                    ⚠ Late Enrollee? Click here to enroll a cadet manually
                </summary>

            <div style="padding:16px; background:#fff8e6; border:1px solid #f0d98c; border-top:none; border-radius:0 0 8px 8px;">

                <form method="GET" action="{{ route('admin.enrollment.index') }}" style="display:flex; gap:8px; margin-bottom:12px;">
                    <input type="text" name="late_search" value="{{ request('late_search') }}"
                           placeholder="Search cadet by name or student ID"
                           style="flex:1; padding:8px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                    <button type="submit" class="btn-green" style="padding:8px 16px;">Search</button>
                </form>

                @if(request('late_search'))
                    @php
                        $lateSearch = request('late_search');
                        $lateCandidates = \App\Models\User::whereHas('cadet', function($q) use ($lateSearch) {
                                $q->where('first_name', 'like', "%{$lateSearch}%")
                                  ->orWhere('last_name', 'like', "%{$lateSearch}%")
                                  ->orWhere('student_number', 'like', "%{$lateSearch}%");
                            })
                            ->whereDoesntHave('enrollments', function($eq) use ($setting) {
                                $eq->where('enrollment_setting_id', $setting->id);
                            })
                            ->with('cadet')
                            ->get();
                    @endphp

                    @if($lateCandidates->isEmpty())
                        <p style="font-size:13px; color:#888;">No matching cadet found, or they're already enrolled this term.</p>
                    @else
                        @foreach($lateCandidates as $candidate)
                            <form method="POST" action="{{ route('admin.enrollment.manual', ['user' => $candidate->id]) }}"
                                  style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid #eee;"
                                  onsubmit="return confirm('Manually enroll {{ $candidate->cadet->first_name ?? '' }} {{ $candidate->cadet->last_name ?? '' }}? This bypasses the closed enrollment window.');">
                                @csrf
                                <span style="font-size:14px;">
                                    {{ $candidate->cadet->first_name ?? '' }} {{ $candidate->cadet->last_name ?? '' }}
                                    <small style="color:#888;">({{ $candidate->cadet->student_number ?? 'no ID' }})</small>
                                </span>
                                <button type="submit" class="btn-green" style="padding:6px 14px; font-size:13px;">
                                    Enroll Manually
                                </button>
                            </form>
                        @endforeach
                    @endif
                @endif
            </div>
            </details>
            @endif

        </div>
    </div>
</div>

</body>
</html>