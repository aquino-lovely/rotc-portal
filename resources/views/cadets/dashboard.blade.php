<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Alaminos-Infanta ROTC Portal</title>
    @include('partials.pwa-head')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
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
                    {{ $user->name }}<br>
                    <small style="opacity:0.85;">Cadet {{ $cadet->rank ?? '' }}</small>
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

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            <div class="page-title">Dashboard</div>
            <div class="page-subtitle">Welcome back, {{ $cadet->first_name ?? $user->name }}!</div>

            {{-- Status cards --}}
            <div style="display: flex; gap: 20px; margin-bottom: 24px;">

                <div class="card" style="flex:1;">
                    <div style="font-size:13px; color:#666; margin-bottom:6px;">Account Status</div>
                    <div style="font-size:20px; font-weight:700; color:#2f5a2f;">
                        {{ ucfirst($user->status) }}
                    </div>
                </div>

                <div class="card" style="flex:1;">
                    <div style="font-size:13px; color:#666; margin-bottom:6px;">Rank</div>
                    <div style="font-size:20px; font-weight:700; color:#2f5a2f;">
                        {{ $cadet->rank ?? 'Not yet assigned' }}
                    </div>
                </div>

                <div class="card" style="flex:1;">
                    <div style="font-size:13px; color:#666; margin-bottom:6px;">Company</div>
                    <div style="font-size:20px; font-weight:700; color:#2f5a2f;">
                        {{ $cadet->company ?? 'Not yet assigned' }}
                    </div>
                </div>

                <div class="card" style="flex:1;">
                    <div style="font-size:13px; color:#666; margin-bottom:6px;">Serial Number</div>
                    <div style="font-size:20px; font-weight:700; color:#9a7b1a;">
                        {{ $cadet->serial_number ?? 'Not yet assigned' }}
                    </div>
                </div>

            </div>

            {{-- At-Risk Warning --}}
            @if(count($atRiskReasons) > 0)
            <div class="card" style="background:#fdeaea; border:1px solid #e3b9b9; margin-bottom:24px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                    <span style="font-size:20px;">⚠️</span>
                    <div style="font-weight:700; color:#a33; font-size:15px;">You are at risk of failing this semester</div>
                </div>
                <ul style="margin:0; padding-left:24px; font-size:13px; color:#a33;">
                    @foreach($atRiskReasons as $reason)
                        <li>{{ $reason }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Performance snapshot --}}
            <div style="display:flex; gap:20px; margin-bottom:24px;">

                @if($cadet && $cadet->cadet_type === 'advance')
                <div class="card" style="flex:1;">
                    <div style="font-size:13px; color:#666; margin-bottom:6px;">PFT Score</div>
                    <div style="font-size:20px; font-weight:700; color:{{ $pftScore !== null && $pftScore < 66.66 ? '#a33' : '#2f5a2f' }};">
                        {{ $pftScore !== null ? $pftScore.'%' : 'Not yet recorded' }}
                    </div>
                </div>
                @endif

                <div class="card" style="flex:1;">
                    <div style="font-size:13px; color:#666; margin-bottom:6px;">Quiz Average</div>
                    <div style="font-size:20px; font-weight:700; color:{{ $quizAvg > 0 && $quizAvg < 75 ? '#a33' : '#2f5a2f' }};">
                        {{ $quizAvg > 0 ? $quizAvg.'%' : 'Not yet recorded' }}
                    </div>
                </div>

                <div class="card" style="flex:1;">
                    <div style="font-size:13px; color:#666; margin-bottom:6px;">Attendance Rate</div>
                    <div style="font-size:20px; font-weight:700; color:{{ $attendanceRate !== null && $attendanceRate < 80 ? '#a33' : '#2f5a2f' }};">
                        {{ $attendanceRate !== null ? $attendanceRate.'%' : 'No sessions yet' }}
                    </div>
                </div>

            </div>

            </div>

        </div>
    </div>
</div>

</body>
@include('partials.pwa-register')
</html>