<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Excuse Requests - Alaminos-Infanta ROTC Portal</title>
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

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <div>
                    <div class="page-title">Excuse Requests</div>
                    <div class="page-subtitle">Review cadet excuse letters</div>
                </div>
                <a href="{{ route('admin.attendance.index') }}" class="btn-green">← Back to Attendance</a>
            </div>

            <div class="card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Training Date</th>
                            <th>Reason</th>
                            <th>Explanation</th>
                            <th>Document</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                        <tr>
                            <td>{{ $req->user->cadet->last_name }}, {{ $req->user->cadet->first_name }}</td>
                            <td>{{ $req->training_date->format('M d, Y') }}</td>
                            <td>{{ $req->reason }}</td>
                            <td style="max-width:200px; font-size:12px;">{{ $req->explanation ?? '—' }}</td>
                            <td>
                                @if($req->document_path)
                                    <a href="{{ asset('storage/' . $req->document_path) }}" target="_blank" style="color:#3d6e3d; font-size:12px;">View</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-{{ $req->status === 'approved' ? 'approved' : ($req->status === 'rejected' ? 'rejected' : 'pending') }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td>
                                @if($req->status === 'pending')
                                <div style="display:flex; gap:6px;">
                                    <form method="POST" action="{{ route('admin.attendance.excuses.approve', $req) }}">
                                        @csrf
                                        <button type="submit" class="btn-green" style="font-size:11px; padding:5px 10px;">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.attendance.excuses.reject', $req) }}">
                                        @csrf
                                        <button type="submit" style="background:#a33; color:#fff; border:none; padding:5px 10px; border-radius:6px; font-size:11px; cursor:pointer;">Reject</button>
                                    </form>
                                </div>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center; color:#666; padding:30px;">
                                No excuse requests yet.
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