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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Worksheet</title>
    <style>
        @page { margin: 25px 35px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #000; }

        table.header { width: 100%; border-collapse: collapse; }
        table.header td { vertical-align: middle; text-align: center; padding: 0; }
        .logo-cell { width: 90px; }
        .logo-cell img { width: 70px; height: 70px; }
        .hq { letter-spacing: 5px; font-size: 12px; }
        .head-line { font-size: 11px; line-height: 1.35; }
        .head-line b { font-size: 12px; }

        .unit { font-size: 10px; margin-top: 10px; }
        .title { text-align: center; margin-top: 4px; }
        .title u { font-weight: bold; font-size: 11px; }
        .group-title { font-weight: bold; font-size: 11px; margin: 12px 0 3px; }

        table.grid { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.grid th, table.grid td { border: 1px solid #000; padding: 2px 1px; text-align: center; }
        table.grid th { font-size: 8px; }
        table.grid td.name { text-align: left; padding-left: 4px; font-size: 10px; }
        table.grid tr { page-break-inside: avoid; }
        .A { color: #e00000; }
        .wk { font-size: 8px; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td class="logo-cell">
                @if ($leftLogo) <img src="{{ $leftLogo }}" alt="ROTC"> @endif
            </td>
            <td>
                <div class="hq">HEADQUARTERS</div>
                <div class="head-line">DEPARTMENT OF MILITARY SCIENCE AND TACTICS</div>
                <div class="head-line"><b>PANGASINAN STATE UNIVERSITY-ALAMINOS CITY CAMPUS ROTC UNIT</b></div>
                <div class="head-line">104<sup>th</sup> (PGN-W) COMMUNITY DEFENSE CENTER, 1RCDG, RESCOM, PA</div>
                <div class="head-line">Brgy. Bolaney, Alaminos City, Pangasinan</div>
                <div class="head-line">PSU-ALAMINOS ROTC</div>
            </td>
            <td class="logo-cell">
                @if ($rightLogo) <img src="{{ $rightLogo }}" alt="PSU"> @endif
            </td>
        </tr>
    </table>

    
        <div class="title">
        <u>WORKSHEET</u><br>
        {{ substr($ordinal, 0, 1) }}<sup>{{ substr($ordinal, 1) }}</sup> Semester SY {{ $schoolYear }}
    </div>

    @foreach ($sections as $section)
        <div class="unit" style="font-weight:bold; font-size:11px; margin-top:12px; @if(!$loop->first) page-break-before: always; @endif">
            {{ $section['title'] }}
        </div>

        @foreach ($section['groups'] as $g)
            <div class="group-title">{{ $g['title'] }}</div>

            <table class="grid">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:3%;">NR</th>
                        <th rowspan="2" style="width:19%;">FULL NAME</th>
                        <th rowspan="2" style="width:6%;">COURSE</th>
                        <th colspan="15">ATTENDANCE</th>
                        <th rowspan="2" style="width:6%;">ATTENDANCE</th>
                        <th rowspan="2" style="width:6%;">APTITUDE</th>
                        <th rowspan="2" style="width:7%;">SUBJECT PROFICIENCY</th>
                        <th rowspan="2" style="width:6%;">RATING</th>
                    </tr>
                    <tr>
                        @for ($w = 1; $w <= 15; $w++)
                            <th class="wk" style="width:2.2%;">{{ $w }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach ($g['rows'] as $i => $r)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="name">{{ $r['name'] }}</td>
                            <td>{{ $r['course'] }}</td>
                            @for ($w = 0; $w < 15; $w++)
                                @php $code = $r['cells'][$w] ?? ''; @endphp
                                <td class="{{ $code === 'A' ? 'A' : '' }}">{{ $code }}</td>
                            @endfor
                            <td></td><td></td><td></td><td></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endforeach

</body>
</html>
