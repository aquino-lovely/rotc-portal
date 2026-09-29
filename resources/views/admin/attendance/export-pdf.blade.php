<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #222; }
        h2 { margin-bottom: 2px; }
        .subtitle { color: #555; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f2f2f2; }
        .summary { margin-bottom: 14px; }
        .summary span { margin-right: 24px; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Alaminos-Infanta ROTC Portal — Attendance Report</h2>
    <div class="subtitle">
        Date: {{ \Carbon\Carbon::parse($date)->format('F j, Y') }} &nbsp;|&nbsp;
        {{ $setting ? $setting->semester . ' ' . $setting->school_year : 'No active period' }}
    </div>

    <div class="summary">
        <span>Total: {{ $total }}</span>
        <span>Present: {{ $present }}</span>
        <span>Absent: {{ $absent }}</span>
        <span>Late: {{ $late }}</span>
        <span>Excused: {{ $excused }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cadets as $cadet)
                @php $record = $records[$cadet->id] ?? null; @endphp
                <tr>
                    <td>{{ $cadet->cadet->student_number ?? '—' }}</td>
                    <td>{{ $cadet->cadet->last_name }}, {{ $cadet->cadet->first_name }}</td>
                    <td>{{ ucfirst($record?->status ?? 'absent') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align:center;">No cadets found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>