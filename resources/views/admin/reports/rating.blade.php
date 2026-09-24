<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 11px; color: #000; }
    .header-table { width: 100%; border-collapse: collapse; border: 2px solid #000; }
    .header-table td { padding: 4px 8px; }
    .logo-cell { width: 70px; text-align: center; vertical-align: middle; border-right: 2px solid #000; }
    .logo-cell img { width: 55px; }
    .title-cell { text-align: center; vertical-align: middle; }
    .title-cell h1 { margin: 0; font-size: 20px; letter-spacing: 1px; }
    .title-cell p { margin: 2px 0 0; font-size: 11px; }
    .meta-row td { border-top: 2px solid #000; padding: 4px 8px; font-size: 11px; }
    .stats-row td { border-top: 2px solid #000; padding: 4px 8px; font-size: 11px; font-weight: bold; }
    .note-row td { border-top: 1px solid #000; text-align: center; font-style: italic; padding: 3px; font-size: 10px; }

    table.students { width: 100%; border-collapse: collapse; margin-top: 0; border-left: 2px solid #000; border-right: 2px solid #000; border-bottom: 2px solid #000; }
    table.students th { background: #f0f0f0; border: 1px solid #000; padding: 4px; font-size: 10px; text-align: center; }
    table.students td { border: 1px solid #000; padding: 3px 5px; font-size: 10.5px; }
    table.students td.num { text-align: center; width: 28px; }
    table.students td.score { text-align: center; width: 60px; }
    table.students td.remarks { text-align: center; width: 70px; }

    .legend-table { width: 100%; border-collapse: collapse; border-left: 2px solid #000; border-right: 2px solid #000; border-bottom: 2px solid #000; }
    .legend-table td { font-size: 9.5px; padding: 3px 8px; font-weight: bold; }

    .sig-block { margin-top: 30px; width: 100%; }
    .sig-block td { width: 50%; font-size: 10.5px; padding-top: 20px; vertical-align: top; }
    .sig-name { font-weight: bold; text-decoration: underline; }
</style>
</head>
<body>

<table class="header-table">
    <tr>
        <td class="logo-cell" rowspan="2">
            {{-- Replace with your actual logo path if available --}}
            {{-- <img src="{{ public_path('images/psu-logo.png') }}"> --}}
        </td>
        <td class="title-cell" rowspan="2">
            <h1>REPORT OF RATING</h1>
            <p>PANGASINAN STATE UNIVERSITY</p>
        </td>
    </tr>
</table>

<table class="header-table" style="border-top:none;">
    <tr class="meta-row">
        <td colspan="2"><strong>SUBJECT CODE:</strong> {{ $subject_code }}</td>
    </tr>
    <tr class="meta-row">
        <td><strong>DESCRIPTIVE TITLE:</strong> {{ $descriptive_title }}</td>
    </tr>
    <tr class="meta-row">
        <td><strong>YEAR &amp; SECTION:</strong> {{ $year_section }}</td>
        <td style="text-align:right;"><strong>PROFESSOR/INSTRUCTOR:</strong> {{ $professor }}</td>
    </tr>
    <tr class="stats-row">
        <td colspan="2">
            TOTAL NO. OF STUDENT PASSED: {{ $passed }} &nbsp;&nbsp;&nbsp;
            FAILED: {{ $failed }} &nbsp;&nbsp;&nbsp;
            CONDITIONAL: 0 &nbsp;&nbsp;&nbsp;
            INCOMPLETE: 0 &nbsp;&nbsp;&nbsp;
            DROPPED: 0
        </td>
    </tr>
    <tr class="note-row">
        <td colspan="2">Please return this form duly accomplished to the Registrar without unnecessary delay</td>
    </tr>
</table>

<table class="students">
    <thead>
        <tr>
            <th style="width:28px;">SEQ.</th>
            <th>NAME OF STUDENTS<br><span style="font-weight:normal;">(Alphabetical Order)</span></th>
            <th style="width:60px;">MID-TERM</th>
            <th style="width:60px;">TENTATIVE<br>FINAL</th>
            <th style="width:60px;">FINAL<br>GRADE</th>
            <th style="width:70px;">REMARKS</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $i => $r)
        <tr>
            <td class="num">{{ $i + 1 }}</td>
            <td>
                {{ $r['last_name'] }}, {{ $r['first_name'] }}
                @if($r['middle_name']) {{ substr($r['middle_name'], 0, 1) }}. @endif
            </td>
          <td class="score">{{ $r['midterm'] !== null ? number_format($r['midterm'], 2) : '—' }}</td>
           <td class="score">{{ $r['tentative_final'] !== null ? number_format($r['tentative_final'], 2) : '—' }}</td>
            <td class="score">{{ $r['final_grade'] !== null ? number_format($r['final_grade'], 2) : '—' }}</td>
            <td class="remarks">{{ $r['status'] === 'pass' ? 'Passed' : ($r['status'] === 'fail' ? 'Failed' : '—') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="legend-table">
    <tr>
        <td>GRADING</td>
        <td>1.00 (97-100)</td>
        <td>1.50 (91-93)</td>
        <td>2.00 (85-87)</td>
        <td>2.50 (79-81)</td>
        <td>3.00 (75)</td>
        <td>5.00 (Failed)</td>
        <td>Drp (Dropped)</td>
    </tr>
    <tr>
        <td>SYSTEM</td>
        <td>1.25 (94-96)</td>
        <td>1.75 (88-90)</td>
        <td>2.25 (82-84)</td>
        <td>2.75 (76-78)</td>
        <td>4.00/Cnd (Conditional)</td>
        <td>Inc (Incomplete)</td>
        <td></td>
    </tr>
</table>

<table class="sig-block">
    <tr>
        <td>
            Submitted by:<br><br><br>
            <span class="sig-name">{{ strtoupper($professor) }}</span><br>
            Signature over Printed Name of Professor / Instructor
        </td>
        <td>
            Certified True and Correct Based on Class Record:<br><br><br>
            <span class="sig-name">&nbsp;</span><br>
            Department Chair
        </td>
    </tr>
</table>

</body>
</html>