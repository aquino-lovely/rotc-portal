<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .stat-cards { display:flex; gap:16px; margin-bottom:24px; }
        .stat-card {
            background:#fff; border-radius:8px; padding:20px 24px;
            box-shadow:0 1px 3px rgba(0,0,0,0.08); flex:1; text-align:center;
        }
        .stat-value { font-size:30px; font-weight:700; }
        .stat-label { font-size:13px; color:#666; margin-top:4px; }
        .stat-total { color:#333; }
        .stat-approved { color:#2f5a2f; }
        .stat-pending { color:#9a7b1a; }
        .stat-atrisk { color:#a33; }

        .chart-card {
            background:#fff; border-radius:8px; padding:24px;
            box-shadow:0 1px 3px rgba(0,0,0,0.08); margin-bottom:24px;
        }
        .chart-title { font-weight:700; font-size:16px; margin-bottom:16px; }
        .chart-row { display:flex; gap:24px; }
        .chart-row .chart-card { flex:1; }
        .period-banner {
            background:#fdf3d8; border:1px solid #e8c440; border-radius:6px;
            padding:10px 16px; font-size:13px; color:#9a7b1a; margin-bottom:20px;
        }
        .risk-table { width:100%; border-collapse:collapse; font-size:13px; }
        .risk-table th { text-align:left; padding:10px 8px; border-bottom:2px solid #eee; font-size:12px; color:#444; }
        .risk-table td { padding:12px 8px; border-bottom:1px solid #f0f0f0; }
        .risk-badge {
            background:#fdeaea; color:#a33; padding:3px 10px;
            border-radius:12px; font-size:11px; font-weight:600;
        }
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

                <div class="page-title">Dashboard</div>
                <div class="page-subtitle">Overview of ROTC cadet enrollment &amp; performance analytics</div>

                @if($isActivePeriod && $setting)
                    <div class="period-banner" style="background:#e3f1e3; border-color:#b9dcb9; color:#2f5a2f;">
                        &#9989; Active Period: <strong>{{ $setting->semester }} {{ $setting->school_year }}</strong>
                    </div>
                @elseif(!$isActivePeriod && $setting)
                    <div class="period-banner">
                        &#9888; No active enrollment period. Showing data from <strong>{{ $setting->semester }} {{ $setting->school_year }}</strong>.
                    </div>
                @elseif(!$setting)
                    <div class="period-banner">
                        &#9888; No enrollment periods found yet. Analytics will appear once a semester is opened.
                    </div>
                @endif

                <div class="stat-cards">
                    <div class="stat-card">
                        <div class="stat-value stat-pending">{{ $pendingCount }}</div>
                        <div class="stat-label">Pending Approvals</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value stat-approved">{{ $approvedCount }}</div>
                        <div class="stat-label">Approved Cadets</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value stat-total">{{ $passCount + $failCount }}</div>
                        <div class="stat-label">Graded Cadets</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value stat-atrisk">{{ $atRiskCadets->count() }}</div>
                        <div class="stat-label">At-Risk Cadets</div>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-title">Attendance Overview</div>
                    @if($attendanceWeekly->count() > 0)
                        <canvas id="attendanceChart" height="80"></canvas>
                    @else
                        <p style="color:#999; font-size:13px; text-align:center; padding:20px;">No attendance sessions recorded yet.</p>
                    @endif
                </div>

                <div class="chart-card">
                    <div class="chart-title">Quizzes Overview</div>
                    @if($quizzesWeekly->count() > 0)
                        <canvas id="quizzesChart" height="80"></canvas>
                    @else
                        <p style="color:#999; font-size:13px; text-align:center; padding:20px;">No quiz scores recorded yet.</p>
                    @endif
                </div>

                <div class="chart-row">
                    <div class="chart-card">
                        <div class="chart-title">Pass / Fail Ratio</div>
                        @if($passCount + $failCount > 0)
                            <canvas id="passFailChart" height="200"></canvas>
                        @else
                            <p style="color:#999; font-size:13px; text-align:center; padding:20px;">No grade records yet.</p>
                        @endif
                    </div>
                    <div class="chart-card">
                        <div class="chart-title">Class Average (per Semester)</div>
                        @if($classAverageHistory->count() > 0)
                            <canvas id="classAvgChart" height="200"></canvas>
                        @else
                            <p style="color:#999; font-size:13px; text-align:center; padding:20px;">No historical data yet.</p>
                        @endif
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-title">At-Risk Cadets</div>
                    <table class="risk-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Quiz Average</th>
                                <th>Attendance Rate</th>
                                <th>Reason(s)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($atRiskCadets as $cadet)
                            <tr>
                                <td>{{ $cadet['name'] }}</td>
                                <td>{{ $cadet['quiz_avg'] > 0 ? $cadet['quiz_avg'].'%' : '—' }}</td>
                                <td>{{ $cadet['attendance'] }}%</td>
                                <td><span class="risk-badge">{{ $cadet['reasons'] }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="text-align:center; color:#666; padding:20px;">
                                    No at-risk cadets. Everyone is on track! 🎉
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

<script>
@if($attendanceWeekly->count() > 0)
new Chart(document.getElementById('attendanceChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($attendanceWeekly->pluck('label')) !!},
        datasets: [
            { label: 'Present', backgroundColor: '#2f5a2f', data: {!! json_encode($attendanceWeekly->pluck('present')) !!} },
            { label: 'Absent',  backgroundColor: '#a33',     data: {!! json_encode($attendanceWeekly->pluck('absent')) !!} },
            { label: 'Late',    backgroundColor: '#e8c440',  data: {!! json_encode($attendanceWeekly->pluck('late')) !!} },
            { label: 'Excused', backgroundColor: '#3d6ea3',  data: {!! json_encode($attendanceWeekly->pluck('excused')) !!} },
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
});
@endif

@if($quizzesWeekly->count() > 0)
new Chart(document.getElementById('quizzesChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($quizzesWeekly->pluck('label')) !!},
        datasets: [
            {
                label: 'Class Average (%)',
                data: {!! json_encode($quizzesWeekly->pluck('value')) !!},
                borderColor: '#3d6e3d',
                backgroundColor: 'rgba(61,110,61,0.1)',
                fill: true,
                tension: 0.3
            },
            {
                label: 'Pass Threshold',
                data: {!! json_encode(array_fill(0, $quizzesWeekly->count(), 75)) !!},
                borderColor: '#a33',
                borderDash: [6,6],
                pointRadius: 0
            }
        ]
    },
    options: { responsive: true, scales: { y: { min: 0, max: 100 } } }
});
@endif

@if($passCount + $failCount > 0)
new Chart(document.getElementById('passFailChart'), {
    type: 'pie',
    data: {
        labels: ['Pass', 'Fail'],
        datasets: [{
            data: [{{ $passCount }}, {{ $failCount }}],
            backgroundColor: ['#2f5a2f', '#a33']
        }]
    },
    options: { responsive: true }
});
@endif

@if($classAverageHistory->count() > 0)
new Chart(document.getElementById('classAvgChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($classAverageHistory->pluck('label')) !!},
        datasets: [{
            label: 'Class Average',
            data: {!! json_encode($classAverageHistory->pluck('value')) !!},
            backgroundColor: '#4f8a4f'
        }]
    },
    options: { responsive: true, scales: { y: { min: 0, max: 100 } } }
});
@endif
</script>

</body>
</html>