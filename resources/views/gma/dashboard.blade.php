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

        .period-banner {
            background:#fdf3d8; border:1px solid #e8c440; border-radius:6px;
            padding:10px 16px; font-size:13px; color:#9a7b1a; margin-bottom:20px;
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
                        {{ auth()->user()->name ?? 'GMA' }}<br>
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

            {{-- Main content --}}
            <div class="main-content">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                <div class="page-title">Dashboard</div>
                <div class="page-subtitle">Physical Fitness Test overview</div>

                @if($setting)
                    <div class="period-banner">
                        &#9989; Active Period: <strong>{{ $setting->semester }} {{ $setting->school_year }}</strong>
                    </div>

                    <div class="stat-cards">
                        <div class="stat-card">
                            <div class="stat-value stat-total">{{ $pftTested }} / {{ $totalCadets }}</div>
                            <div class="stat-label">Cadets Tested</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value stat-approved">{{ $pftPassed }}</div>
                            <div class="stat-label">Passed</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value stat-atrisk">{{ $pftFailed }}</div>
                            <div class="stat-label">At-Risk / Failed</div>
                        </div>
                    </div>
                @else
                    <div class="period-banner">
                        &#9888; No active PFT period. Physical Fitness stats will appear once a semester is opened.
                    </div>
                @endif

            </div>

        </div>
    </div>

</body>
</html>