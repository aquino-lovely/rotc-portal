{{-- resources/views/admin/reports/classlist.blade.php --}}
@php
    // Embed logos as base64 (dompdf is unreliable with URLs).
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
    <title>Class List</title>
    <style>
        @page { margin: 30px 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }

                    table.header { width: 460px; margin: 0 auto 14px auto; border-collapse: collapse; }
            table.header td { vertical-align: middle; padding: 0; }
            .logo-cell { width: 75px; }
            .logo-cell img { width: 65px; height: 65px; }
            .title-cell { text-align: center; padding: 0 8px; }
            .title-cell h2 { margin: 0; font-size: 15px; }
            .title-cell p { margin: 2px 0; }

        table.list { width: 100%; border-collapse: collapse; }
        table.list th, table.list td { border: 1px solid #444; padding: 5px 6px; }
        table.list th { background: #e8f0e8; text-align: left; }
        .center { text-align: center; }
    </style>
</head>
<body>

   <table class="header">
    <tr>
        <td class="logo-cell" style="text-align:right;">
            @if ($leftLogo)
                <img src="{{ $leftLogo }}" alt="ROTC Logo">
            @endif
        </td>
        <td class="title-cell">
            <h2>Alaminos-Infanta ROTC</h2>
            <p><strong>CLASS LIST</strong></p>
            <p>{{ $semesterLabel }}</p>
            @if ($professor)
                <p>Instructor: {{ $professor }}</p>
            @endif
        </td>
        <td class="logo-cell" style="text-align:left;">
            @if ($rightLogo)
                <img src="{{ $rightLogo }}" alt="PSU Logo">
            @endif
        </td>
    </tr>
</table>

    <table class="list">
        <thead>
            <tr>
                <th class="center" style="width:8%;">No.</th>
                <th>Student Name</th>
                <th style="width:30%;">Course</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $i => $r)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $r['name'] }}</td>
                    <td>{{ $r['course'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>