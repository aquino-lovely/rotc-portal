<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grades - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    @include('partials.pwa-head')
    <style>
        .ms-header {
            background: #d9e8f5;
            padding: 12px 20px;
            font-weight: 700;
            font-size: 15px;
            text-align: center;
            border-radius: 6px;
            margin: 24px 0 16px;
        }
        .ms-header:first-of-type { margin-top: 0; }
        .grade-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .grade-table th {
            text-align: left;
            padding: 10px 8px;
            font-size: 13px;
            color: #444;
            border-bottom: 2px solid #eee;
        }
        .grade-table td {
            padding: 14px 8px;
            border-bottom: 1px solid #f5f5f5;
        }
        .no-value {
            color: #999;
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

            <div class="page-title">Grades</div>

            <div class="card">
                @forelse($militaryScienceGroups as $group)
                    <div class="ms-header">{{ $group['ms_label'] }}</div>
                    <table class="grade-table">
                        <thead>
                            <tr>
                                <th>CODE</th>
                                <th>SUBJECT</th>
                                <th>GRADE</th>
                                <th>REMARKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $group['course_code'] }}</td>
                                <td>{{ $group['subject'] }}</td>
                                <td>{{ $group['grade'] ?? '—' }}</td>
                                <td>{{ $group['remarks'] ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                @empty
                    <p style="color:#666; font-size:14px; text-align:center; padding:30px;">
                        No grades recorded yet.
                    </p>
                @endforelse
            </div>

        </div>
    </div>
</div>

@include('partials.pwa-register')

</body>
</html>