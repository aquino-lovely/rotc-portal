<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cadet Profiles - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
    .btn-view {
        display: inline-block;
        background: #2f5d34;
        color: #fff;
        padding: 7px 16px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1;
        text-decoration: none;
        white-space: nowrap;
        transition: background 0.15s;
    }
    .btn-view:hover {
        background: #244a29;
    }
</style>
</head>
<body>
@php
    $isS1 = in_array(auth()->user()->role, ['S1', 'deputy_s1']);
@endphp

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

            <div class="page-title">Cadet Profiles</div>
            <div class="page-subtitle">Manage cadet registrations and approvals</div>

            <div class="card">
                <div class="search-row">
                    <form method="GET" action="{{ route('admin.cadets.index') }}" style="display:flex; gap:10px; flex:1;">
                        <input type="text" name="search" class="search-input"
                               placeholder="Search by name, username, student number"
                               value="{{ request('search') }}">
                        <button type="submit" class="btn-green">Search</button>
                    </form>
                </div>

   

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>STUDENT NO.</th>
                            <th>NAME</th>
                            <th>USERNAME</th>
                            <th>COURSE</th>
                            @if($isS1)
                                <th>STATUS</th>
                            @endif
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cadets as $cadet)
                        <tr>
                            <td>{{ $cadet->cadet->student_number ?? '—' }}</td>
                            <td>{{ $cadet->cadet->last_name ?? '' }}, {{ $cadet->cadet->first_name ?? '' }}</td>
                            <td>{{ $cadet->username }}</td>
                           <td>{{ $cadet->cadet->course ?? '—' }}</td>
@if($isS1)
<td>
    <span class="status-badge status-{{ $cadet->status }}">
        {{ ucfirst($cadet->status) }}
    </span>
</td>
@endif
<td>
  <a href="{{ route('admin.cadets.show', $cadet) }}" class="btn-view">View</a>
</td>
                        </tr>
                        @empty
                        <tr>
                           <td colspan="{{ $isS1 ? 6 : 5 }}" style="text-align:center; color:#666; padding:30px;">
    No cadets found.
</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div style="margin-top:16px;">
                    {{ $cadets->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>