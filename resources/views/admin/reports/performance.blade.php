@php
    $logo = function ($file) {
        $path = public_path('images/' . $file);
        return file_exists($path)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($path))
            : null;
    };
    $leftLogo  = $logo('rotc-logo.png');
    $rightLogo = $logo('psu-logo.png');
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        .header { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .header td { border: none; padding: 0; vertical-align: middle; }
        .header .logo-cell { width: 80px; text-align: center; }
        .header .logo-cell img { width: 70px; height: 70px; }
        .header .title-cell { text-align: center; }
        .header .title-cell .school { font-size: 13px; font-weight: bold; }
        .header .title-cell .report { font-size: 15px; font-weight: bold; margin-top: 4px; }
        .header .title-cell .sem { font-size: 11px; margin-top: 2px; }
        .line { border-bottom: 2px solid #2f6b2f; margin-bottom: 10px; }
        h3 { margin-top: 18px; margin-bottom: 4px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th, table.data td { border: 1px solid #444; padding: 4px 6px; }
        table.data th { background: #e5ede5; }
        td.c { text-align: center; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td class="logo-cell">
                @if($leftLogo)<img src="{{ $leftLogo }}" alt="ROTC Logo">@endif
            </td>
            <td class="title-cell">
                <div class="school">Pangasinan State University</div>
                <div class="report">Physical Fitness Performance Ranking</div>
                <div class="sem">{{ $semesterLabel }}</div>
            </td>
            <td class="logo-cell">
                @if($rightLogo)<img src="{{ $rightLogo }}" alt="PSU Logo">@endif
            </td>
        </tr>
    </table>
    <div class="line"></div>

    @foreach (['Female' => $female, 'Male' => $male] as $label => $rows)
        <h3>{{ $label }}</h3>
        <table class="data">
            <thead>
                <tr>
                    <th>Rank</th><th>Student No.</th><th>Name</th>
                    <th>Run Time (min)</th><th>Push-ups</th><th>Sit-ups</th>
                    <th>Overall</th><th>Rating</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td class="c">{{ $r['rank'] }}</td>
                        <td>{{ $r['student_number'] }}</td>
                        <td>{{ $r['name'] }}</td>
                        <td class="c">{{ $r['run_time'] ?? '—' }}</td>
                        <td class="c">{{ $r['push_ups'] ?? '—' }}</td>
                        <td class="c">{{ $r['sit_ups'] ?? '—' }}</td>
                        <td class="c"><strong>{{ $r['overall'] }}</strong></td>
                        <td class="c">{{ $r['rating'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="c">No records</td></tr>
                @endforelse
            </tbody>
        </table>
    @endforeach
</body>
</html>