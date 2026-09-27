<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h2 { margin: 0 0 4px; }
        .sub { color: #555; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 5px 6px; text-align: left; }
        th { background: #3d6e3d; color: #fff; }
    </style>
</head>
<body>
    <h2>Attendance Report</h2>
    <div class="sub">
        Date range:
        {{ $from ?: 'Start' }} to {{ $to ?: 'Latest' }}
           </div>

        <table>
        <thead>
            <tr>
                <th>Student No.</th>
                <th>Name</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $r)
                <tr>
                    <td>{{ $r->user->cadet->student_number ?? '—' }}</td>
                    <td>{{ ($r->user->cadet->last_name ?? '') . ', ' . ($r->user->cadet->first_name ?? '') }}</td>
                    <td>{{ $r->session->session_date->format('Y-m-d') }}</td>
                    <td>{{ ucfirst($r->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>