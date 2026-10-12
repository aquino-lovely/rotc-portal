@php
    $logo = function ($file) {
        $path = public_path('images/' . $file);
        return file_exists($path)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($path))
            : null;
    };
    $leftLogo  = $logo('rotc-logo.png');
    $rightLogo = $logo('psu-logo.png');
    $semestral = $type === 'semestral';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Report</title>
    <style>
        @page { margin: 30px 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: {{ $semestral ? '9px' : '11px' }}; color: #222; }

        table.header { width: 460px; margin: 0 auto 14px auto; border-collapse: collapse; }
        table.header td { vertical-align: middle; padding: 0; }
        .logo-cell { width: 75px; }
        .logo-cell img { width: 65px; height: 65px; }
        .title-cell { text-align: center; padding: 0 8px; }
        .title-cell h2 { margin: 0; font-size: 15px; }
        .title-cell p { margin: 2px 0; }

        table.list { width: 100%; border-collapse: collapse; }
        table.list th, table.list td { border: 1px solid #444; padding: 4px 5px; }
        table.list th { background: #e8f0e8; text-align: center; }
        .left { text-align: left !important; }
        .center { text-align: center; }
        .A { color: #b71c1c; font-weight: bold; }
        .summary { margin-top: 12px; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td class="logo-cell" style="text-align:right;">
                @if ($leftLogo) <img src="{{ $leftLogo }}" alt="ROTC Logo"> @endif
            </td>
            <td class="title-cell">
                <h2>Alaminos-Infanta ROTC</h2>
                <p><strong>{{ $semestral ? 'SEMESTRAL ATTENDANCE REPORT' : 'TRAINING DAY ATTENDANCE REPORT' }}</strong></p>
                <p>{{ $semesterLabel }}</p>
                @if ($trainingDay)
                    <p>Training Day: {{ $trainingDay->format('F d, Y (l)') }}</p>
                @endif
            </td>
            <td class="logo-cell" style="text-align:left;">
                @if ($rightLogo) <img src="{{ $rightLogo }}" alt="PSU Logo"> @endif
            </td>
        </tr>
    </table>

    @if (! $semestral)
        <table class="list">
            <thead>
                <tr>
                    <th style="width:8%;">No.</th>
                    <th class="left">Student Name</th>
                    <th style="width:25%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $r)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ $r['name'] }}</td>
                        <td class="center {{ $r['cells'][0]['status'] === 'absent' ? 'A' : '' }}">
                            {{ ucfirst($r['cells'][0]['status']) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <strong>Summary:</strong>
            Present: {{ $summary['present'] }} &nbsp;|&nbsp;
            Late: {{ $summary['late'] }} &nbsp;|&nbsp;
            Excused: {{ $summary['excused'] }} &nbsp;|&nbsp;
            Absent: {{ $summary['absent'] }}
        </div>
    @else
        <table class="list">
            <thead>
                <tr>
                    <th rowspan="2" style="width:4%;">No.</th>
                    <th rowspan="2" class="left">Student Name</th>
                    <th colspan="{{ count($dates) }}">Training Days (Saturdays)</th>
                    <th colspan="4">Totals</th>
                    <th rowspan="2" style="width:5%;">%</th>
                </tr>
                <tr>
                    @foreach ($dates as $d)
                        <th>{{ \Carbon\Carbon::parse($d)->format('m/d') }}</th>
                    @endforeach
                    <th>P</th><th>L</th><th>E</th><th>A</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $r)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ $r['name'] }}</td>
                        @foreach ($r['cells'] as $cell)
                            <td class="center {{ $cell['code'] === 'A' ? 'A' : '' }}">{{ $cell['code'] }}</td>
                        @endforeach
                        <td class="center">{{ $r['present'] }}</td>
                        <td class="center">{{ $r['late'] }}</td>
                        <td class="center">{{ $r['excused'] }}</td>
                        <td class="center">{{ $r['absent'] }}</td>
                        <td class="center">{{ $r['percent'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <strong>Legend:</strong> P = Present, L = Late, E = Excused, A = Absent.
            Attendance % counts Present and Late.
        </div>
    @endif

</body>
</html>