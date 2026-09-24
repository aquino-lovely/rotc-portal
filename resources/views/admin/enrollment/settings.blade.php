<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrollment Settings - Alaminos-Infanta ROTC Portal</title>
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

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="page-title">Enrollment Settings</div>
            <div class="page-subtitle">Set the active enrollment period for cadets</div>

            <div class="card" style="max-width:500px;">

                @if($setting)
                    <div style="margin-bottom:20px; padding:12px 16px; background:#e3f1e3; border:1px solid #b9dcb9; border-radius:6px; font-size:14px;">
                        <strong>Current Active Period:</strong><br>
                        {{ $setting->semester }} {{ $setting->school_year }}
                        — <span style="color:{{ $setting->is_open ? '#2f5a2f' : '#a33' }}">
                            {{ $setting->is_open ? 'Open' : 'Closed' }}
                          </span>
                    </div>
                @endif
                @if($setting)
    @if($setting->isFinalized())
        <div style="margin:12px 0; padding:10px 16px; background:#fdecec; border:1px solid #f0b8b8; border-radius:6px; font-size:13px; color:#a33;">
            🔒 This term was finalized on {{ $setting->finalized_at->format('M d, Y') }}. No further edits allowed.
        </div>
        <form method="POST" action="{{ route('admin.enrollment.settings.unfinalize', $setting) }}"
              onsubmit="return confirm('Reopen this term for editing?');">
            @csrf
            <button type="submit" class="btn-green" style="background:#a33;">Un-finalize</button>
        </form>
    @else
        <form method="POST" action="{{ route('admin.enrollment.settings.finalize', $setting) }}"
              onsubmit="return confirm('Finalize this term? Attendance and grades will be locked.');">
            @csrf
            <button type="submit" class="btn-green">Finalize Term</button>
        </form>
    @endif
@endif

                <form method="POST" action="{{ route('admin.enrollment.settings.save') }}">
                 @csrf
                     <div style="margin-bottom:16px;">
                        <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px;">
                            School Year
                        </label>
                        <input type="text" disabled
                            value="{{ $suggestedSchoolYear }}"
                            style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px; background:#f5f5f5; color:#555;">
                        <input type="hidden" name="school_year" value="{{ $suggestedSchoolYear }}">
                        <p style="font-size:12px; color:#888; margin-top:4px;">Auto-set based on the academic calendar.</p>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px;">
                            Semester
                        </label>
                        <select disabled
                                style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px; background:#f5f5f5; color:#555;">
                            <option selected>{{ $suggestedSemester }}</option>
                        </select>
                        <input type="hidden" name="semester" value="{{ $suggestedSemester }}">
                        <p style="font-size:12px; color:#888; margin-top:4px;">Auto-set based on the academic calendar (Aug–Dec: 1st Sem, Jan–May: 2nd Sem).</p>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px;">
                            Enrollment Start Date
                        </label>
                        <input type="date" name="start_date"
                               value="{{ old('start_date', $setting->start_date ?? '') }}"
                               style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px;">
                            Enrollment End Date (deadline)
                        </label>
                        <input type="date" name="end_date"
                               value="{{ old('end_date', $setting->end_date ?? '') }}"
                               style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                    </div>

                    <div style="margin-bottom:16px;">
    <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px;">
        Start of Classes
    </label>
    <input type="date" name="class_start_date"
           value="{{ old('class_start_date', $setting->class_start_date ?? '') }}"
           style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
</div>

<div style="margin-bottom:16px;">
    <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px;">
        End of Classes
    </label>
    <input type="date" name="class_end_date"
           value="{{ old('class_end_date', $setting->class_end_date ?? '') }}"
           style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
</div>

                    <div style="margin-bottom:16px;">
                        <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px;">
                            Unit
                        </label>
                        <input type="text" name="unit"
                            value="{{ old('unit', $setting->unit ?? '') }}"
                            placeholder="e.g. 3"
                            style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px;">
                            Time
                        </label>
                        <input type="text" name="time"
                            value="{{ old('time', $setting->time ?? '') }}"
                            placeholder="e.g. 7:00 AM - 10:00 AM"
                            style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                    </div>

                    <div style="margin-bottom:24px; display:flex; align-items:center; gap:10px;">
                        <input type="checkbox" name="is_open" id="is_open" value="1"
                               {{ old('is_open', $setting->is_open ?? true) ? 'checked' : '' }}
                               style="width:16px; height:16px;">
                        <label for="is_open" style="font-size:14px; font-weight:500;">
                            Open enrollment (cadets can submit)
                        </label>
                    </div>
              {{-- Replace everything from "Cadet Grade Weights" down to the closing </form> with this block --}}
@php
    $weightGroups = [
        'term' => [
            'title' => 'Cadet Grade Weights',
            'hint'  => '(same for Midterm and Final, must total 100%)',
            'note'  => 'Each component is transmuted (base 50) before weighting: EQ = (score &divide; max score) &times; 50 + 50, then EQ &times; weight %. Term percentile = sum of the weighted components, rounded.',
            'fields' => [
                ['quizzes_weight',          'Quizzes %',                  0],
                ['field_activities_weight', 'Field Activities % (AB)',    30],
                ['attendance_weight',       'Attendance % (BE)',          40],
                ['midterm_exam_weight',     'Exam % (Midterm and Final)', 30],
            ],
        ],
        'semestral' => [
            'title' => 'Semestral Grade Weights',
            'hint'  => '(must total 100%)',
            'note'  => 'Semestral percentile = (Midterm &times; weight %) + (Final &times; weight %), rounded, then converted with the grading scale. Example: 82 and 92 at 40/60 = 88, which is 1.75.',
            'fields' => [
                ['first_term_weight',  'Midterm %', 40],
                ['second_term_weight', 'Final %',    60],
            ],
        ],
    ];
@endphp

@foreach ($weightGroups as $key => $group)
    <div style="margin-bottom:4px; font-weight:700; font-size:14px; color:#3d6e3d; border-top:1px solid #eee; padding-top:16px;">
        {{ $group['title'] }}
        <span style="font-size:12px; color:#888; font-weight:400;">{{ $group['hint'] }}</span>
    </div>
    <div style="font-size:12px; color:#888; margin-bottom:12px;">{!! $group['note'] !!}</div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:8px;">
        @foreach ($group['fields'] as [$name, $label, $default])
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:4px;">{{ $label }}</label>
                <input type="number" name="{{ $name }}" class="js-{{ $key }}-weight" min="0" max="100" step="0.01"
                       value="{{ old($name, $setting->{$name} ?? $default) }}"
                       style="width:100%; padding:8px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                @error($name)
                    <div style="color:#c0392b; font-size:12px; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
        @endforeach
    </div>
    <div style="font-size:13px; margin-bottom:24px;">
        Total: <strong id="{{ $key }}-total">100</strong>%
        <span id="{{ $key }}-msg" style="color:#c0392b;"></span>
    </div>
@endforeach

<button type="submit" class="btn-green" style="width:100%; padding:12px; font-size:15px;">
    Save Enrollment Period
</button>
</form>

<script>
    (function () {
        function bindTotal(key) {
            var inputs = document.querySelectorAll('.js-' + key + '-weight');
            var totalEl = document.getElementById(key + '-total');
            var msgEl = document.getElementById(key + '-msg');

            function update() {
                var total = 0;
                inputs.forEach(function (i) { total += parseFloat(i.value) || 0; });
                total = Math.round(total * 100) / 100;
                var ok = total === 100;
                totalEl.textContent = total;
                totalEl.style.color = ok ? '#3d6e3d' : '#c0392b';
                msgEl.textContent = ok ? '' : '(must equal 100%)';
            }

            inputs.forEach(function (i) { i.addEventListener('input', update); });
            update();
        }

        ['term', 'semestral'].forEach(bindTotal);
    })();
</script>


            </div>
        </div>
    </div>
</div>

</body>
</html>