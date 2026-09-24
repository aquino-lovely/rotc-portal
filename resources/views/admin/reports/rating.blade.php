<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 11px; color: #000; }

    table.report {
        width: 100%;
        border-collapse: collapse;
        border: 2px solid #000;
    }
    table.report td,
    table.report th {
        border: 1px solid #000;
        padding: 4px 6px;
        vertical-align: middle;
    }

    .title-cell { text-align: center; padding: 8px 6px !important; }
    .title-cell h1 { margin: 0; font-size: 20px; letter-spacing: 1px; line-height: 1.2; }
    .title-cell p  { margin: 3px 0 0; font-size: 11px; }

    .stats-cell { font-weight: bold; }
    .note-cell  { text-align: center; font-style: italic; font-size: 10px; padding: 3px !important; }

    table.report th {
        background: #f0f0f0;
        font-size: 10px;
        text-align: center;
        border-top: 2px solid #000;
        border-bottom: 2px solid #000;
    }
    table.report td.num     { text-align: center; }
    table.report td.score   { text-align: center; }
    table.report td.remarks { text-align: center; }

    /* Grading legend (nested table, no inner lines) */
    td.legend-holder { padding: 0 !important; border-top: 2px solid #000 !important; }
    table.legend { width: 100%; border-collapse: collapse; border: none; }
    table.legend td { border: none !important; padding: 3px 6px !important; font-size: 9px; font-weight: bold; }

    /* Signatures */
    .sig-block { margin-top: 30px; width: 100%; border-collapse: collapse; }
    .sig-block td { width: 50%; font-size: 10.5px; padding-top: 20px; vertical-align: top; }
    .sig-name { font-weight: bold; text-decoration: underline; }
    .sig-line { border-bottom: 1px solid #000; width: 70%; height: 14px; }
</style>
</head>
<body>

<table class="report">
    {{-- TITLE --}}
    <tr>
        <td colspan="6" class="title-cell">
            {{-- Optional logo:
            <img src="{{ public_path('images/psu-logo.png') }}" style="float:left; width:55px;"> --}}
            <h1>REPORT OF RATING</h1>
            <p>PANGASINAN STATE UNIVERSITY</p>
        </td>
    </tr>

    {{-- INFO ROWS --}}
    <tr>
        <td colspan="6"><strong>SUBJECT CODE:</strong> {{ $subject_code }}</td>
    </tr>
    <tr>
        <td colspan="6"><strong>DESCRIPTIVE TITLE:</strong> {{ $descriptive_title }}</td>
    </tr>
    <tr>
        <td colspan="3"><strong>YEAR &amp; SECTION:</strong> {{ $year_section }}</td>
        <td colspan="3" style="text-align:right;"><strong>PROFESSOR/INSTRUCTOR:</strong> {{ $professor }}</td>
    </tr>
    <tr>
        <td colspan="6" class="stats-cell">
            TOTAL NO. OF STUDENT PASSED: {{ $passed }} &nbsp;&nbsp;&nbsp;
            FAILED: {{ $failed }} &nbsp;&nbsp;&nbsp;
            CONDITIONAL: 0 &nbsp;&nbsp;&nbsp;
            INCOMPLETE: 0 &nbsp;&nbsp;&nbsp;
            DROPPED: 0
        </td>
    </tr>
    <tr>
        <td colspan="6" class="note-cell">
            Please return this form duly accomplished to the Registrar without unnecessary delay
        </td>
    </tr>

    {{-- COLUMN HEADERS --}}
    <tr>
        <th style="width:28px;">SEQ.</th>
        <th>NAME OF STUDENTS<br><span style="font-weight:normal;">(Alphabetical Order)</span></th>
        <th style="width:60px;">MID-TERM</th>
        <th style="width:60px;">TENTATIVE<br>FINAL</th>
        <th style="width:60px;">FINAL<br>GRADE</th>
        <th style="width:70px;">REMARKS</th>
    </tr>

    {{-- STUDENTS --}}
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

    {{-- GRADING LEGEND --}}
    <tr>
        <td colspan="6" class="legend-holder">
            <table class="legend">
                <tr>
                    <td style="width:10%;">GRADING</td>
                    <td style="width:11%;">1.00 (97-100)</td>
                    <td style="width:11%;">1.50 (91-93)</td>
                    <td style="width:11%;">2.00 (85-87)</td>
                    <td style="width:11%;">2.50 (79-81)</td>
                    <td style="width:17%;">3.00 (75)</td>
                    <td style="width:17%;">5.00 (Failed)</td>
                    <td style="width:12%;">Drp (Dropped)</td>
                </tr>
                <tr>
                    <td>SYSTEM</td>
                    <td>1.25 (94-96)</td>
                    <td>1.75 (88-90)</td>
                    <td>2.25 (82-84)</td>
                    <td>2.75 (76-78)</td>
                    <td>4.00/Cnd (Conditional)</td>
                    <td>Inc (Incomplete)</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </td>
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
            <div class="sig-line"></div>
            Department Chair
        </td>
    </tr>
</table>

</body>
</html>