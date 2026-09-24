<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Tracking - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .stat-cards { display:flex; gap:16px; margin-bottom:24px; }
        .stat-card {
            background:#fff; border-radius:8px; padding:20px 24px;
            box-shadow:0 1px 3px rgba(0,0,0,0.08); flex:1; text-align:center;
        }
        .stat-card .stat-value { font-size:30px; font-weight:700; }
        .stat-card .stat-label { font-size:13px; color:#666; margin-top:4px; }
        .stat-total { color:#333; }
        .stat-present { color:#2f5a2f; }
        .stat-absent { color:#a33; }
        .stat-late { color:#9a7b1a; }
        .stat-excused { color:#3d6ea3; }

        .filter-bar {
            display:flex; justify-content:space-between; align-items:center;
            margin-bottom:20px; gap:12px;
        }
        .filter-bar input[type=date], .filter-bar select {
            padding:8px 12px; border:1px solid #ddd; border-radius:6px; font-size:13px;
        }

        .att-table { width:100%; border-collapse:collapse; font-size:13px; }
        .att-table th {
            text-align:left; padding:10px 8px; border-bottom:2px solid #eee;
            font-size:12px; color:#444;
        }
        .att-table td { padding:14px 8px; border-bottom:1px solid #f0f0f0; }
        .status-select {
            padding:6px 10px; border:1px solid #ddd; border-radius:6px; font-size:12px;
        }
        .status-select.present { background:#e3f1e3; color:#2f5a2f; }
        .status-select.absent  { background:#fdeaea; color:#a33; }
        .status-select.late    { background:#fdf3d8; color:#9a7b1a; }
        .status-select.excused { background:#e3ecf7; color:#3d6ea3; }
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

            <div class="page-title">Attendance Tracking</div>
            <div class="page-subtitle">Monitor cadet attendance and punctuality</div>

            <div class="stat-cards">
                <div class="stat-card">
                    <div class="stat-value stat-total">{{ $total }}</div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value stat-present">{{ $present }}</div>
                    <div class="stat-label">Present</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value stat-absent">{{ $absent }}</div>
                    <div class="stat-label">Absent</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value stat-late">{{ $late }}</div>
                    <div class="stat-label">Late</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value stat-excused">{{ $excused }}</div>
                    <div class="stat-label">Excused</div>
                </div>
            </div>

            <div class="card">
                <div class="filter-bar">
                <form method="GET" action="{{ route('admin.attendance.index') }}" style="display:flex; gap:10px;">
                    <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()">
                    <span style="font-size:13px; color:#666; align-self:center;">
                        {{ $setting ? $setting->semester . ' ' . $setting->school_year : 'No active period' }}
                    </span>
                </form>
                <div style="display:flex; gap:10px;">
                    <a href="{{ route('admin.attendance.excuses') }}" class="btn-green" style="background:#6a9a6a;">
                        📋 Excuse Requests
                    </a>
                    <button class="btn-green">+ Export Report</button>
                </div>
</div>

                <table class="att-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Rank</th>
                            <th>Time In (A.M.)</th>
                            <th>Time Out (P.M.)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cadets as $cadet)
                        @php $record = $records[$cadet->id] ?? null; @endphp
                        <tr>
                            <td>{{ $cadet->cadet->student_number ?? '—' }}</td>
                            <td>{{ $cadet->cadet->last_name }}, {{ $cadet->cadet->first_name }}</td>
                            <td>{{ $cadet->cadet->rank ?? '—' }}</td>
                            <td>{{ $record?->time_in ? \Carbon\Carbon::parse($record->time_in)->format('g:i A') : '—' }}</td>
                            <td>{{ $record?->time_out ? \Carbon\Carbon::parse($record->time_out)->format('g:i A') : '—' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.attendance.updateStatus', $cadet->id) }}">
                                    @csrf
                                    <input type="hidden" name="date" value="{{ $date }}">
                                    <select name="status" class="status-select {{ $record?->status ?? 'absent' }}"
                                            onchange="this.form.submit()">
                                        <option value="present" {{ ($record?->status ?? '') === 'present' ? 'selected' : '' }}>Present</option>
                                        <option value="absent"  {{ ($record?->status ?? 'absent') === 'absent' ? 'selected' : '' }}>Absent</option>
                                        <option value="late"    {{ ($record?->status ?? '') === 'late' ? 'selected' : '' }}>Late</option>
                                        <option value="excused" {{ ($record?->status ?? '') === 'excused' ? 'selected' : '' }}>Excused</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:#666; padding:30px;">
                                No cadets found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>