{{-- resources/views/reports/enrollment-form.blade.php --}}
@php
    // Embed logos as base64 (dompdf is unreliable with URLs).
    // Put your logo files in: public/images/rotc-logo.png and public/images/psu-logo.png
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
    <meta charset="utf-8">
    <title>Enrollment Form</title>
    <style>
        @page { margin: 30px 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td { padding: 0; vertical-align: bottom; }
        .center { text-align: center; }
        .lbl { white-space: nowrap; padding-right: 4px; }
        .val { border-bottom: 1px solid #000; padding: 0 4px; text-align: center; }
        .sub { font-size: 7px; text-align: center; letter-spacing: 0.5px; padding-top: 1px; }
        .row { margin-top: 9px; }
        .grid td, .grid th { border: 1px solid #000; padding: 6px 4px; text-align: center; vertical-align: middle; }
        .grid th { font-weight: bold; }
    </style>
</head>
<body>

@foreach ($enrollments as $enrollment)
    @php
        $cadet      = $enrollment->user->cadet;
        $level      = $enrollment->subject_level ?? '';
        $semester   = $enrollment->setting->semester ?? '';
        $schoolYear = $enrollment->setting->school_year ?? '';
        $yrCourse = collect([$cadet->course ?? null, $cadet->year_section ?? null])->filter()->implode(' ');
        $birth    = !empty($cadet->birthday) ? \Carbon\Carbon::parse($cadet->birthday) : null;

        // Database stores height in cm, but the form asks for feet (e.g. 5'5")
        $height = '';
        if (!empty($cadet->height_cm)) {
            $totalInches = (int) round($cadet->height_cm / 2.54);
            $height = intdiv($totalInches, 12) . "'" . ($totalInches % 12) . '"';
        }
        $blood    = (($cadet->blood_type ?? '') === 'Unknown') ? '' : ($cadet->blood_type ?? '');
        $ben      = $cadet->beneficiary; // hasOne relationship on the Cadet model


    $cadetName = trim(($cadet->last_name ?? '') . ', ' . ($cadet->first_name ?? '') . ' ' . ($cadet->middle_name ?? ''));
    @endphp

    <div @if(!$loop->last) style="page-break-after: always;" @endif>

        {{-- ===== HEADER ===== --}}
        <div class="center" style="font-size:7.5px; font-weight:bold;">
            ARMY 2040: WORLD-CLASS. MULTI-MISSION READY. CROSS-DOMAIN CAPABLE
        </div>

        <table style="margin-top:8px;">
            <tr>
                <td style="width:15%; text-align:right; vertical-align:middle;">
                    @if($leftLogo) <img src="{{ $leftLogo }}" style="width:65px; display:block; margin-left:auto;"> @endif
                </td>
                <td class="center" style="width:70%; vertical-align:middle; line-height:1.4;">
                    <div style="letter-spacing:4px; font-size:9px;">HEADQUARTERS</div>
                    <div style="font-size:11px;">DEPARTMENT OF MILITARY SCIENCE AND TACTICS</div>
                    <div style="font-size:11px;">PANGASINAN STATE UNIVERSITY ALAMINOS-INFANTA ROTC UNIT</div>
                    <div style="font-size:9px;">104TH (PGN-W) COMMUNITY DEFENSE CENTER, 1RCDG, RESCOM, PA</div>
                    <div style="font-size:9px;">Brgy. Bolaney, Alaminos City, Pangasinan</div>
                </td>
                <td style="width:15%; text-align:left; vertical-align:middle;">
                    @if($rightLogo) <img src="{{ $rightLogo }}" style="width:65px; display:block; margin-right:auto;"> @endif
                </td>
            </tr>
        </table>

        <div class="center" style="margin-top:14px;">
            <span style="font-weight:bold; text-decoration:underline; font-size:10.5px;">ENROLLMENT FORM OF NSTP-ROTC CADET</span><br>
            <span style="font-size:9px;">({{ $semester }} SY {{ $schoolYear }})</span>
        </div>

        {{-- ===== NAME / YR & COURSE ===== --}}
        <table class="row" style="margin-top:14px;">
            <tr>
                <td class="lbl" style="width:8%;">Name:</td>
                <td class="val" style="width:20%;">{{ strtoupper($cadet->last_name ?? '') }}</td>
                <td class="val" style="width:22%;">{{ trim(($cadet->first_name ?? '') . ' ' . ($cadet->suffix ?? '')) }}</td>
                <td class="val" style="width:20%;">{{ $cadet->middle_name ?? '' }}</td>
                <td class="lbl" style="width:14%; text-align:right;">Yr &amp; Course:</td>
                <td class="val" style="width:16%;">{{ $yrCourse }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="width:8%;"></td>
                <td class="sub" style="width:20%;">SURNAME</td>
                <td class="sub" style="width:22%;">FIRSTNAME</td>
                <td class="sub" style="width:20%;">MIDDLE NAME</td>
                <td style="width:30%;"></td>
            </tr>
        </table>

        {{-- ===== ADDRESS / CP NO ===== --}}
        <table>
            <tr>
                <td class="lbl" style="width:8%;">Address:</td>
                <td class="val" style="width:20%;">{{ $cadet->barangay ?? '' }}</td>
                <td class="val" style="width:22%;">{{ $cadet->city_municipality ?? '' }}</td>
                <td class="val" style="width:20%;">{{ $cadet->province ?? '' }}</td>
                <td class="lbl" style="width:14%; text-align:right;">CP No:</td>
                <td class="val" style="width:16%;">{{ $cadet->contact_no ?? '' }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="width:8%;"></td>
                <td class="sub" style="width:20%;">BARANGAY</td>
                <td class="sub" style="width:22%;">CITY</td>
                <td class="sub" style="width:20%;">PROVINCE</td>
                <td style="width:30%;"></td>
            </tr>
        </table>

        {{-- ===== BIRTHDATE / AGE / SEX / STATUS / RELIGION ===== --}}
        <table>
            <tr>
                <td class="lbl" style="width:10%;">Birthdate:</td>
                <td class="val" style="width:18%;">{{ $birth ? $birth->format('m/d/Y') : '' }}</td>
                <td class="lbl" style="width:6%; text-align:right;">Age:</td>
                <td class="val" style="width:6%;">{{ $birth ? $birth->age : '' }}</td>
                <td class="lbl" style="width:5%; text-align:right;">Sex:</td>
                <td class="val" style="width:7%;">{{ $cadet->sex ?? '' }}</td>
                <td class="lbl" style="width:8%; text-align:right;">Status:</td>
                <td class="val" style="width:12%;">{{ $cadet->civil_status ?? '' }}</td>
                <td class="lbl" style="width:9%; text-align:right;">Religion:</td>
                <td class="val" style="width:19%;">{{ $cadet->religion ?? '' }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="sub">(MONTH/DAY/YEAR)</td>
                <td colspan="8"></td>
            </tr>
        </table>

        {{-- ===== BLOOD TYPE / HEIGHT / WEIGHT / SKILLS ===== --}}
        <table class="row">
            <tr>
                <td class="lbl" style="width:11%;">Blood Type:</td>
                <td class="val" style="width:9%;">{{ $blood }}</td>
                <td class="lbl" style="width:12%; text-align:right;">Height (ft):</td>
                <td class="val" style="width:7%;">{{ $height }}</td>
                <td class="lbl" style="width:12%; text-align:right;">Weight(kg):</td>
                <td class="val" style="width:7%;">{{ $cadet->weight_kg ?? '' }}</td>
                <td class="lbl" style="width:9%; text-align:right;">Skills: 1.</td>
                <td class="val" style="width:14%;">{{ $cadet->skill_1 ?? '' }}</td>
                <td class="lbl" style="width:3%; text-align:right;">2.</td>
                <td class="val" style="width:16%;">{{ $cadet->skill_2 ?? '' }}</td>
            </tr>
        </table>

        {{-- ===== BENEFICIARY ===== --}}
        <table class="row" style="margin-top:14px;">
            <tr>
                <td class="lbl" style="width:19%;">Beneficiaries Name:</td>
                <td class="val" style="width:20%;">{{ strtoupper($ben->surname ?? '') }}</td>
                <td class="val" style="width:19%;">{{ trim(($ben->first_name ?? '') . ' ' . ($ben->suffix ?? '')) }}</td>
                <td class="val" style="width:8%;">{{ $ben->middle_initial ?? '' }}</td>
                <td class="lbl" style="width:8%; text-align:right;">CP No:</td>
                <td class="val" style="width:26%;">{{ $ben->contact_no ?? '' }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="width:19%;"></td>
                <td class="sub" style="width:20%;">SURNAME</td>
                <td class="sub" style="width:19%;">FIRST NAME</td>
                <td class="sub" style="width:8%;">M.I</td>
                <td style="width:34%;"></td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="lbl" style="width:8%;">Address:</td>
                <td class="val" style="width:19%;">{{ $ben->barangay ?? '' }}</td>
                <td class="val" style="width:20%;">{{ $ben->city_municipality ?? '' }}</td>
                <td class="val" style="width:19%;">{{ $ben->province ?? '' }}</td>
                <td class="lbl" style="width:14%; text-align:right;">Relationship:</td>
                <td class="val" style="width:20%;">{{ $ben->relationship ?? '' }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="width:8%;"></td>
                <td class="sub" style="width:19%;">BARANGAY</td>
                <td class="sub" style="width:20%;">CITY</td>
                <td class="sub" style="width:19%;">PROVINCE</td>
                <td style="width:34%;"></td>
            </tr>
        </table>

        {{-- ===== COURSE TABLE ===== --}}
        <table class="grid" style="margin-top:14px;">
            <tr>
                <th style="width:14%;">Course Code</th>
                <th style="width:14%;">Descriptive Title</th>
                <th style="width:9%;">MS</th>
                <th style="width:8%;">Unit</th>
                <th style="width:15%;">Time</th>
                <th style="width:16%;">Days</th>
                <th style="width:24%;">Room</th>
            </tr>
            <tr>
                <td style="height:60px;">NSTP {{ $level }}</td>
                <td>ROTC {{ $level }}</td>
                <td>MS {{ $level }}</td>
                <td>{{ $enrollment->setting->unit ?? '' }}</td>
                <td>{{ $enrollment->setting->time ?? '' }}</td>
                <td>SATURDAY</td>
                <td>PSU COVERED COURT</td>
            </tr>
        </table>

        <div class="center" style="margin-top:14px; font-size:9px; letter-spacing:0.5px;">
            HONOR. PATRIOTISM. DUTY
        </div>

        {{-- ===== SIGNATURES ===== --}}
<table style="margin-top:55px;">
    <tr>
        <td style="width:38%; text-align:center;">
            <span class="sig-name">{{ strtoupper($professor ?? '') }}</span><br>
            <span style="border-top:1px solid #000; display:block; padding-top:3px;">
                Administering Officer
            </span>
        </td>
        <td style="width:24%;"></td>
        <td style="width:38%; text-align:center;">
    <span class="sig-name">{{ strtoupper($cadetName) }}</span>
    <span style="border-top:1px solid #000; display:block; padding-top:3px;">
        Signature over Printed Name
    </span>
</td>
</table>

    </div>
@endforeach

</body>
</html>