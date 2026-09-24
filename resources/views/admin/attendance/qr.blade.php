<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Attendance - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .qr-layout { display:flex; gap:24px; align-items:flex-start; }
        .qr-box { flex:1; max-width:380px; }
        .qr-image-wrap {
            background:#f4f4f4; border-radius:12px; padding:30px;
            display:flex; align-items:center; justify-content:center;
            min-height:280px; margin-bottom:16px;
        }
        .qr-image-wrap img { width:220px; height:220px; }
        .qr-placeholder { color:#999; font-size:14px; text-align:center; }
        .scan-box { flex:1; }
        .scan-item {
            display:flex; justify-content:space-between; align-items:center;
            padding:14px 0; border-bottom:1px solid #eee;
        }
        .scan-item:last-child { border-bottom:none; }
        .scan-name { font-weight:600; font-size:14px; }
        .scan-time { font-size:12px; color:#666; }
        .scan-status {
            padding:4px 12px; border-radius:12px; font-size:12px; font-weight:600;
        }
        .status-present { background:#e3f1e3; color:#2f5a2f; }
        .status-late { background:#fdf3d8; color:#9a7b1a; }
        .scanned-count {
            display:flex; align-items:center; gap:6px;
            background:#e3f1e3; color:#2f5a2f; padding:6px 14px;
            border-radius:20px; font-size:13px; font-weight:600;
        }
        .qr-form label { display:block; font-size:12px; font-weight:600; margin-bottom:4px; }
        .qr-form input {
            width:100%; padding:8px 10px; border:1px solid #ddd; border-radius:6px;
            font-size:13px; margin-bottom:12px;
        }
        .expiry-note { font-size:12px; color:#a33; margin-top:8px; }
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

            <div class="page-title">QR Code Attendance</div>
            <div class="page-subtitle">{{ now()->format('l, F j, Y') }}</div>

            @if(!$setting)
                <div class="card">
                    <p style="color:#666; font-size:14px; text-align:center; padding:30px;">
                        No active enrollment period. Open one in Enrollment Settings first.
                    </p>
                </div>
            @else
            <div class="qr-layout">

                {{-- QR generate box --}}
                <div class="qr-box">
                    <div class="card">
                        <div style="font-weight:700; font-size:15px; margin-bottom:16px;">Generate QR Code</div>

                        <div class="qr-image-wrap">
                            @if($session && $session->is_active && $session->qr_expires_at && now()->lessThan($session->qr_expires_at))
                                <img id="qrImage" src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($session->qr_token) }}" alt="QR Code">
                            @else
                                <div class="qr-placeholder">
                                    &#128274;<br>No Active QR Code Session
                                </div>
                            @endif
                        </div>

                        @if($session && $session->is_active)
                            <div style="text-align:center; margin-bottom:12px; font-size:13px; color:#666;">
                                Expires: <span id="expiryCountdown">{{ $session->qr_expires_at?->format('g:i A') }}</span>
                            </div>
                            <form method="POST" action="{{ route('admin.qr.close') }}">
                                @csrf
                                <button type="submit" class="btn-green" style="width:100%; background:#a33;">
                                    Close Session
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.qr.generate') }}" class="qr-form">
                                @csrf
                                <label>Term</label>
                                <div style="display:flex; gap:16px; margin-bottom:12px;">
                                    <label style="display:flex; align-items:center; gap:6px; font-weight:400;">
                                        <input type="radio" name="term" value="midterm" checked style="width:auto; margin:0;"> Midterm
                                    </label>
                                    <label style="display:flex; align-items:center; gap:6px; font-weight:400;">
                                        <input type="radio" name="term" value="final" style="width:auto; margin:0;"> Final
                                    </label>
                                </div>

                                <label>Cutoff Time (Late after)</label>
                                <input type="time" name="cutoff_time" value="08:00">

                                <label>QR Expires In (minutes)</label>
                                <input type="number" name="expiry_minutes" value="30" min="1" max="180">

                                <button type="submit" class="btn-green" style="width:100%;">
                                    Generate QR Code
                                </button>
                             </form>
                        @endif
                    </div>
                </div>

                {{-- Real-time scans --}}
                <div class="scan-box">
                    <div class="card">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                            <div style="font-weight:700; font-size:15px;">Real-time Scans</div>
                            <div class="scanned-count">
                                ✓ <span id="scanCount">{{ $scannedRecords->count() }}</span> Scanned
                            </div>
                        </div>

                        <div id="scanList">
                            @forelse($scannedRecords as $record)
                            <div class="scan-item">
                                <div>
                                    <div class="scan-name">{{ $record->user->cadet->last_name }}, {{ $record->user->cadet->first_name }}</div>
                                    <div class="scan-time">{{ $record->time_in ? \Carbon\Carbon::parse($record->time_in)->format('g:i A') : '' }}</div>
                                </div>
                                <span class="scan-status status-{{ $record->status }}">{{ ucfirst($record->status) }}</span>
                            </div>
                            @empty
                            <p style="color:#999; font-size:13px; text-align:center; padding:20px;">
                                No scans yet.
                            </p>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
            @endif

        </div>
    </div>
</div>

<script>
// Poll for live scan updates every 5 seconds
@if($session && $session->is_active)
setInterval(function() {
    fetch("{{ route('admin.qr.scans') }}")
        .then(res => res.json())
        .then(data => {
            document.getElementById('scanCount').textContent = data.count;
            const list = document.getElementById('scanList');
            if (data.records.length === 0) {
                list.innerHTML = '<p style="color:#999; font-size:13px; text-align:center; padding:20px;">No scans yet.</p>';
                return;
            }
            list.innerHTML = data.records.map(r => `
                <div class="scan-item">
                    <div>
                        <div class="scan-name">${r.name}</div>
                        <div class="scan-time">${r.time}</div>
                    </div>
                    <span class="scan-status status-${r.status.toLowerCase()}">${r.status}</span>
                </div>
            `).join('');
        });
}, 5000);
@endif
</script>

</body>
</html>