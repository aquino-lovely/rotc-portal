<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    @include('partials.pwa-head')
    <title>Enrollment - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <style>
        .cadet-data-table { width: 100%; font-size: 14px; margin-bottom: 24px; }
        .cadet-data-table td { padding: 4px 0; }
        .cadet-data-table td:first-child { font-weight: 600; width: 220px; }
        .cadet-data-table td:last-child { font-style: italic; color: #333; }
        .course-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 8px; }
        .course-table th { background: #3d6e3d; color: #fff; padding: 10px 12px; text-align: left; font-size: 12px; letter-spacing: 0.5px; }
        .course-table td { padding: 12px; border-bottom: 1px solid #eee; }
        .action-row { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
        .btn-cancel { background: #aaa; color: #fff; border: none; padding: 10px 28px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-finalize { background: #3d6e3d; color: #fff; border: none; padding: 10px 28px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .status-box { padding: 12px 16px; border-radius: 6px; font-size: 14px; margin-bottom: 20px; }
        .status-finalized { background: #e3f1e3; border: 1px solid #b9dcb9; color: #2f5a2f; }
        .status-blocked { background: #fdeaea; border: 1px solid #e3b9b9; color: #a33; }
        .retake-note { font-size: 12px; color: #a33; margin: -4px 0 20px; }
        .no-enrollment { text-align: center; padding: 40px; color: #666; font-size: 14px; }
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
                    {{ $user->name }}<br>
                    <small style="opacity:0.85;">Cadet {{ $user->cadet->rank ?? '' }}</small>
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

            <div class="page-title">Enrollment</div>
            <div class="page-subtitle">Submit your enrollment for the current semester</div>

            @if(!$setting)
                <div class="card">
                    <div class="no-enrollment">
                        &#128274; No active enrollment period at this time.<br>
                        <small>Please check back when admin opens enrollment.</small>
                    </div>
                </div>

            @else
                @php
                    $displayLevel = $enrollment->subject_level ?? ($eligibility['subject_level'] ?? null);
                    $isBlocked = !$enrollment && $eligibility && $eligibility['blocked'];
                @endphp

                <div class="card">

                    @if($enrollment)
                        <div class="status-box status-finalized">
                            ✅ Your enrollment for <strong>{{ $setting->semester }} {{ $setting->school_year }}</strong> has been finalized.
                            @if($enrollment->is_retake)
                                <br>⚠ You are retaking ROTC 1 (failed previous semester).
                            @endif
                        </div>
                    @elseif($isBlocked)
                        <div class="status-box status-blocked">
                            {{ $eligibility['reason'] }}
                        </div>
                    @endif

                    {{-- Cadet Data --}}
                    <div style="margin-bottom: 20px;">
                        <div style="font-size:15px; font-weight:700; color:#3d6e3d; margin-bottom:12px;">Cadet Data</div>
                        <table class="cadet-data-table">
                            <tr>
                                <td>Full Name on the Record:</td>
                                <td>{{ $user->cadet->last_name ?? '' }}, {{ $user->cadet->first_name ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Course:</td>
                                <td>{{ $user->cadet->course ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td>Year Level:</td>
                                <td>{{ $user->cadet->year_section ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td>School Year:</td>
                                <td>{{ $setting->school_year }}</td>
                            </tr>
                            <tr>
                                <td>Semester:</td>
                                <td>{{ $setting->semester }}</td>
                            </tr>
                        </table>
                    </div>

                    {{-- Course to Enroll — only shown when we actually know the subject level --}}
                    @if(!$isBlocked && $displayLevel)
                        <div style="font-size:15px; font-weight:700; color:#3d6e3d; margin-bottom:12px;">Course to Enroll</div>
                        <table class="course-table">
                            <thead>
                                <tr>
                                    <th>COURSE CODE</th>
                                    <th>DESCRIPTIVE TITLE</th>
                                    <th>MS</th>
                                    <th>UNIT</th>
                                    <th>TIME</th>
                                    <th>DAYS</th>
                                    <th>ROOM</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $displayLevel == 1 ? 'NSTP 1' : 'NSTP 2' }}</td>
                                    <td>{{ $displayLevel == 1 ? 'ROTC 1' : 'ROTC 2' }}</td>
                                    <td>{{ $displayLevel == 1 ? 'MS 1' : 'MS 2' }}</td>
                                    <td>{{ $setting->unit ?? '-' }}</td>
                                    <td>{{ $setting->time ?? '-' }}</td>
                                    <td>Saturday</td>
                                    <td>PSU Covered Court</td>
                                </tr>
                            </tbody>
                        </table>

                        @if(!$enrollment && $eligibility['is_retake'])
                            <p class="retake-note">⚠ You will be retaking ROTC 1 (failed First Semester).</p>
                        @endif
                    @endif

                    {{-- Action row --}}
                    <div class="action-row">
                        <a href="{{ route('dashboard') }}" class="btn-cancel">
                            {{ $enrollment || $isBlocked ? 'Back to Dashboard' : 'Cancel' }}
                        </a>

                        @if(!$enrollment && !$isBlocked)
                            <form method="POST" action="{{ route('cadet.enrollment.store') }}">
                                @csrf
                                <button type="submit" class="btn-finalize">Submit</button>
                            </form>
                        @endif
                    </div>

                </div>
            @endif

        </div>
    </div>
</div>

</body>
</html>