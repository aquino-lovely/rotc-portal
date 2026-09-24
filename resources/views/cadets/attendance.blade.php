<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    @include('partials.pwa-head')
    <title>My Attendance - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .stat-cards { display:flex; gap:16px; margin-bottom:24px; }
        .stat-card {
            background:#fff; border-radius:8px; padding:20px 24px;
            box-shadow:0 1px 3px rgba(0,0,0,0.08); flex:1;
        }
        .stat-label { font-size:13px; font-weight:700; margin-bottom:6px; }
        .stat-value { font-size:32px; font-weight:700; }
        .stat-sub { font-size:11px; color:#888; margin-top:2px; }
        .label-present { color:#2f5a2f; }
        .label-absent  { color:#a33; }
        .label-late    { color:#9a7b1a; }
        .label-excused { color:#3d6ea3; }

        .excuse-card { background:#fff; border-radius:8px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08); margin-bottom:24px; }
        .excuse-title { color:#3d7a3d; font-weight:700; font-size:16px; margin-bottom:4px; }
        .excuse-sub { font-size:12px; color:#666; margin-bottom:16px; }
        .excuse-grid { display:grid; grid-template-columns:1fr 1.5fr 1fr; gap:20px; margin-bottom:16px; }
        .excuse-grid label { display:block; font-size:12px; font-weight:600; margin-bottom:6px; }
        .excuse-grid input, .excuse-grid select, .excuse-grid textarea {
            width:100%; padding:8px 10px; border:1px solid #ddd; border-radius:6px; font-size:13px;
        }
        .excuse-grid textarea { resize:vertical; min-height:70px; }
        .upload-btn {
            background:#fff; border:1.5px solid #bbb; border-radius:20px;
            padding:6px 20px; font-size:13px; cursor:pointer; margin-top:6px;
        }

        .hist-table { width:100%; border-collapse:collapse; font-size:13px; }
        .hist-table th { text-align:left; padding:10px 8px; border-bottom:2px solid #eee; font-size:12px; color:#444; }
        .hist-table td { padding:12px 8px; border-bottom:1px solid #f0f0f0; }
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
                    <small style="opacity:0.85;">Cadet {{ auth()->user()->cadet->rank ?? '' }}</small>
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

            <div class="page-title">My Attendance</div>

            <div class="stat-cards">
                <div class="stat-card">
                    <div class="stat-label label-present">Present</div>
                    <div class="stat-value">{{ $present }}</div>
                    <div class="stat-sub">out of {{ $totalSessions }} Training Days</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label label-absent">Absent</div>
                    <div class="stat-value">{{ $absent }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label label-late">Late</div>
                    <div class="stat-value">{{ $late }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label label-excused">Excused</div>
                    <div class="stat-value">{{ $excused }}</div>
                </div>
            </div>

            {{-- Excuse Letter Form --}}
            <div class="excuse-card">
                <div class="excuse-title">Submit Excuse Letter</div>
                <div class="excuse-sub">If you need to miss a training session, submit an excuse letter or leave request form here.</div>

                <form method="POST" action="{{ route('cadet.attendance.excuse') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="excuse-grid">
                        <div>
                            <label>Training Day</label>
                            <input type="date" name="training_date" required>
                        </div>
                        <div>
                            <label>Detailed Explanation</label>
                            <textarea name="explanation" placeholder="Provide details about your excuse..."></textarea>
                        </div>
                        <div>
                            <label>Supporting Document</label>
                            <div style="font-size:12px; color:#666; margin-bottom:6px;">Medical Certificate, etc. (JPG/PDF)</div>
                            <input type="file" name="document" accept=".jpg,.jpeg,.png,.pdf">
                        </div>
                    </div>

                    <div style="margin-bottom:16px; max-width:300px;">
                        <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Reason</label>
                        <select name="reason" required
                                style="width:100%; padding:8px 10px; border:1px solid #ddd; border-radius:6px; font-size:13px;">
                            <option value="Medical/Health Issue">Medical/Health Issue</option>
                            <option value="Family Emergency">Family Emergency</option>
                            <option value="School Activity Conflict">School Activity Conflict</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-green" style="padding:10px 24px;">
                        Submit Excuse Letter
                    </button>
                </form>
            </div>

            {{-- Attendance History --}}
            <div class="card">
                <table class="hist-table">
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $index => $record)
                        <tr>
                            <td>Week {{ $records->count() - $index }}</td>
                            <td>{{ $record->session->session_date->format('Y-m-d') }}</td>
                            <td>{{ $record->time_in ? \Carbon\Carbon::parse($record->time_in)->format('g:i A') : '—' }}</td>
                            <td>{{ $record->time_out ? \Carbon\Carbon::parse($record->time_out)->format('g:i A') : '—' }}</td>
                            <td>
                                <span class="status-badge status-{{ $record->status }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center; color:#666; padding:30px;">
                                No attendance records yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('partials.pwa-register')
</body>
</html>