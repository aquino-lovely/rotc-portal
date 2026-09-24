<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    @include('partials.pwa-head')
    <title>Cadet Profile - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
@include('partials.pwa-register')
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

                <div class="page-title">Cadet Profile Management</div>
                <div class="page-subtitle">Manage cadet information and records</div>

                <div class="card">
                    <form method="GET" action="{{ route('admin.cadets.index') }}" class="search-row">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or school id" class="search-input">
                        <a href="#" class="btn-green">+ Add Cadet</a>
                    </form>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Rank</th>
                                <th>Company</th>
                                <th>Year</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cadets as $user)
                                @php($cadet = $user->cadet)
                                <tr>
                                    <td>{{ $cadet->student_number ?? '—' }}</td>
                                    <td>{{ $cadet->last_name ?? '' }}, {{ $cadet->first_name ?? '' }}</td>
                                    <td>{{ $cadet->rank ?? '—' }}</td>
                                    <td>{{ $cadet->company ?? '—' }}</td>
                                    <td>{{ $cadet->year_section ?? '—' }}</td>
                                    <td>{{ $cadet->contact_no ?? '—' }}</td>
                                    <td>
                                        @if ($user->status === 'pending')
                                            <span class="status-badge status-pending">Pending</span>
                                        @elseif ($user->status === 'approved')
                                            <span class="status-badge status-approved">Approved</span>
                                        @else
                                            <span class="status-badge status-rejected">Rejected</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.cadets.show', $user) }}" class="btn-green">More Details</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align:center; color:#888; padding:30px;">No cadets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div style="margin-top:16px;">
                        {{ $cadets->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>