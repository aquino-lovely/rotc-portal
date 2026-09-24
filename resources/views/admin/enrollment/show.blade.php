<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Enrollment - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .cadet-data-table { width: 100%; font-size: 14px; margin-bottom: 24px; }
        .cadet-data-table td { padding: 4px 0; }
        .cadet-data-table td:first-child { font-weight: 600; width: 220px; }
        .course-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 28px; }
        .course-table th { background: #3d6e3d; color: #fff; padding: 10px 12px; text-align: left; font-size: 12px; }
        .course-table td { padding: 12px; border-bottom: 1px solid #eee; }
        .doc-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 28px; }
        .doc-card { border: 1.5px solid #ddd; border-radius: 8px; padding: 16px; text-align: center; }
        .doc-icon { font-size: 32px; margin-bottom: 8px; }
        .doc-name { font-weight: 600; font-size: 14px; margin-bottom: 2px; }
        .btn-view-doc { background: #3d6e3d; color: #fff; border: none; border-radius: 20px; padding: 6px 20px; font-size: 13px; text-decoration: none; display: inline-block; margin-top: 8px; }
        .btn-not-uploaded { background: #eee; color: #888; border: none; border-radius: 20px; padding: 6px 20px; font-size: 13px; }
        .action-row { display: flex; justify-content: flex-end; gap: 12px; margin-top: 8px; }
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

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <div>
                    <div class="page-title">Enrollment Details</div>
                    <div class="page-subtitle">Review cadet enrollment submission</div>
                </div>
                <a href="{{ route('admin.enrollment.index') }}" class="btn-green">← Back to List</a>
            </div>

            <div class="card">

            {{-- Status --}}
        <div style="margin-bottom:20px;">
    <span class="status-badge status-{{ $enrollment->status }}" style="font-size:14px; padding:6px 16px;">
        {{ ucfirst($enrollment->status) }}
    </span>
    @if($enrollment->rejection_reason)
        <div style="margin-top:10px; padding:10px 14px; background:#fdeaea; border:1px solid #e3b9b9; border-radius:6px; font-size:13px; color:#a33;">
            <strong>Rejection Reason:</strong> {{ $enrollment->rejection_reason }}
        </div>
    @endif
</div>

                {{-- Cadet Data --}}
                <div style="font-size:15px; font-weight:700; color:#3d6e3d; margin-bottom:12px;">Cadet Data</div>
                <table class="cadet-data-table">
                    <tr>
                        <td>Full Name on the Record:</td>
                        <td>{{ $enrollment->user->cadet->last_name ?? '' }}, {{ $enrollment->user->cadet->first_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>Course:</td>
                        <td>{{ $enrollment->user->cadet->course ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Year Level:</td>
                        <td>{{ $enrollment->user->cadet->year_section ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>School Year:</td>
                        <td>{{ $enrollment->setting->school_year }}</td>
                    </tr>
                    <tr>
                        <td>Semester:</td>
                        <td>{{ $enrollment->setting->semester }}</td>
                    </tr>
                </table>

                        {{-- Course to Enroll --}}
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
                            <td>{{ $enrollment->setting->semester === 'First Semester' ? 'NSTP 1' : 'NSTP 2' }}</td>
                            <td>{{ $enrollment->setting->semester === 'First Semester' ? 'ROTC 1' : 'ROTC 2' }}</td>
                            <td>{{ $enrollment->setting->semester === 'First Semester' ? 'MS 1' : 'MS 2' }}</td>
                            <td>{{ $enrollment->setting->unit ?? '—' }}</td>
                            <td>{{ $enrollment->setting->time ?? '—' }}</td>
                            <td>Saturday</td>
                            <td>PSU Covered Court</td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

</body>
</html>