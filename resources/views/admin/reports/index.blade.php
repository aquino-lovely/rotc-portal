<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
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
                    <small style="opacity:0.85;">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</small>
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

            <div class="page-title">Report Generation</div>
            <div class="page-subtitle">Generate and download various reports</div>

            @if (session('error'))
                <div style="background:#fdecea; color:#b71c1c; padding:10px 14px; border-radius:6px; margin-bottom:16px; max-width:500px;">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div style="background:#fdecea; color:#b71c1c; padding:10px 14px; border-radius:6px; margin-bottom:16px; max-width:500px;">
                    @foreach ($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            @endif

            <div class="card" style="max-width:500px;">
                <div style="font-size:16px; font-weight:700; color:#3d6e3d; margin-bottom:20px;">
                    Generate New Report
                </div>

                <form method="POST" action="{{ route('admin.reports.generate') }}">
                    @csrf

                    {{-- Report type --}}
                    <div style="margin-bottom:16px;">
                        <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px;">
                            Report Type
                        </label>
                        <select name="report_type" required
                                style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                            <option value="attendance" @selected(old('report_type') == 'attendance')>Attendance Report</option>
                            <option value="grades" @selected(old('report_type') == 'grades')>Grades Report</option>
                            <option value="enrollment" @selected(old('report_type') == 'enrollment')>Enrollment Report</option>
                        </select>
                    </div>

                    {{-- Attendance: semester + Saturday training day --}}
                    <div style="margin-bottom:16px;" id="attendance-field">
                        <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px;">
                            Semester / School Year
                        </label>
                        <select name="attendance_setting_id"
                                style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                            <option value="">— Select —</option>
                            @foreach($enrollmentSettings as $s)
                                <option value="{{ $s->id }}" @selected(old('attendance_setting_id') == $s->id)>
                                    {{ $s->school_year }} - {{ $s->semester }}
                                </option>
                            @endforeach
                        </select>

                        <label style="display:block; font-weight:600; font-size:13px; margin:12px 0 6px;">
                            Training Day (Saturday)
                        </label>
                        <input type="date" name="training_day" id="training_day" value="{{ old('training_day') }}"
                               style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                        <small style="color:#777;">Leave blank to include all Saturdays in the semester.</small>
                    </div>

                    {{-- Enrollment: date range --}}
                    <div style="margin-bottom:16px; display:none;" id="date-range-field">
                        <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px;">
                            Date Range
                        </label>
                        <div style="display:flex; gap:10px;">
                            <input type="date" name="date_from" value="{{ old('date_from') }}"
                                   style="flex:1; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                            <input type="date" name="date_to" value="{{ old('date_to') }}"
                                   style="flex:1; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                        </div>
                    </div>

                    {{-- Grades: semester + professor --}}
                    <div style="margin-bottom:16px; display:none;" id="setting-field">
                        <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px;">
                            Semester / School Year
                        </label>
                        <select name="enrollment_setting_id"
                                style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                            <option value="">— Select —</option>
                            @foreach($enrollmentSettings as $s)
                                <option value="{{ $s->id }}" @selected(old('enrollment_setting_id') == $s->id)>
                                    {{ $s->school_year }} - {{ $s->semester }}
                                </option>
                            @endforeach
                        </select>

                        <label style="display:block; font-weight:600; font-size:13px; margin:12px 0 6px;">
                            Professor / Instructor
                        </label>
                        <input type="text" name="professor" value="{{ old('professor') }}" placeholder="e.g. Juan Dela Cruz"
                               style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                    </div>

                    {{-- Enrollment: administering officer --}}
                    <div style="margin-bottom:16px; display:none;" id="administering-officer-field">
                        <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px;">
                            Administering Officer
                        </label>
                        <input type="text" name="administering_officer" value="{{ old('administering_officer') }}" placeholder="e.g. Juan Dela Cruz"
                               style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                    </div>

                    {{-- Format --}}
                    <div style="margin-bottom:24px;">
                        <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px;">
                            Format
                        </label>
                        <select name="format" id="format"
                                style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                            <option value="csv" @selected(old('format') == 'csv')>CSV</option>
                            <option value="pdf" @selected(old('format') == 'pdf')>PDF</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-green" style="width:100%; padding:12px; font-size:15px;">
                        GENERATE
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    const reportType      = document.querySelector('[name="report_type"]');
    const settingField    = document.getElementById('setting-field');
    const attendanceField = document.getElementById('attendance-field');
    const dateField       = document.getElementById('date-range-field');
    const officerField    = document.getElementById('administering-officer-field');
    const trainingDay     = document.getElementById('training_day');

    function toggleFields() {
        const type = reportType.value;
        settingField.style.display    = type === 'grades' ? 'block' : 'none';
        attendanceField.style.display = type === 'attendance' ? 'block' : 'none';
        dateField.style.display       = type === 'enrollment' ? 'block' : 'none';
        officerField.style.display    = type === 'enrollment' ? 'block' : 'none';
    }

    // Saturday only
    trainingDay.addEventListener('change', function () {
        if (this.value && new Date(this.value + 'T00:00:00').getDay() !== 6) {
            alert('Please choose a Saturday.');
            this.value = '';
        }
    });

    reportType.addEventListener('change', toggleFields);
    toggleFields();
</script>

</body>
</html>