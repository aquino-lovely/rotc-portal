<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
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
                        {{ auth()->user()->name ?? 'S1' }}<br>
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

                <div class="page-title">Dashboard</div>
                <div class="page-subtitle">Cadet enrollment overview</div>

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
                        <div class="stat-value stat-atrisk">{{ $rejectedCount }}</div>
                        <div class="stat-label">Rejected</div>
                    </div>
                </div>

                @if($recentPending->count())
                    <div class="chart-card" style="background:#fff; border-radius:8px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
                        <div class="chart-title" style="font-weight:700; font-size:16px; margin-bottom:16px;">Recent Pending Registrations</div>
                        <table class="risk-table" style="width:100%; border-collapse:collapse; font-size:13px;">
                            <tr><th style="text-align:left; padding:10px 8px; border-bottom:2px solid #eee;">Name</th><th style="text-align:left; padding:10px 8px; border-bottom:2px solid #eee;">Submitted</th></tr>
                            @foreach($recentPending as $cadet)
                                <tr>
                                    <td style="padding:12px 8px; border-bottom:1px solid #f0f0f0;">{{ $cadet->cadet->full_name ?? $cadet->username }}</td>
                                    <td style="padding:12px 8px; border-bottom:1px solid #f0f0f0;">{{ $cadet->created_at->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif

            </div>

        </div>
    </div>

</body>
</html>