@php $viewOnly = in_array(auth()->user()->role, ['S1', 'deputy_s1']); @endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Performance - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .tab-bar {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
        }
        .tab-btn {
            padding: 10px 24px;
            border-radius: 20px;
            border: 2px solid #3d6e3d;
            background: #fff;
            color: #3d6e3d;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
        .tab-btn.active {
            background: #3d6e3d;
            color: #fff;
        }
        .stat-cards {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            flex: 1;
            text-align: center;
        }
        .stat-card .stat-label {
            font-size: 13px;
            color: #666;
            margin-bottom: 6px;
        }
        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
        }
        .stat-pass { color: #2f5a2f; }
        .stat-fail { color: #a33; }
        .stat-total { color: #333; }
        .perf-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .perf-table th {
            background: #3d6e3d;
            color: #fff;
            padding: 10px 10px;
            text-align: left;
            font-size: 12px;
        }
        .perf-table td {
            padding: 10px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        .perf-table input[type=number] {
            width: 70px;
            padding: 4px 6px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            text-align: center;
        }
        .pass-badge {
            background: #e3f1e3;
            color: #2f5a2f;
            padding: 3px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            
}
        .rating-badge { padding:3px 10px; border-radius:12px; font-size:12px; font-weight:600; }
.rating-excellent { background:#d4f4dd; color:#1a7a3a; }
.rating-very-satisfactory { background:#d9edf7; color:#31708f; }
.rating-satisfactory { background:#fcf3cf; color:#8a6d1a; }
.rating-failed { background:#f8d7da; color:#a94442; }
        .fail-badge {
            background: #fdeaea;
            color: #a33;
            padding: 3px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            gap: 10px;
        }
        .btn-secondary {
            background: #6a9a6a;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-secondary:hover { background: #4f8a4f; }
        .quiz-max-row {
            background: #f9f9f9;
        }
        .quiz-max-row td {
            font-size: 12px;
            color: #666;
            padding: 6px 10px;
        }
        .no-active {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 14px;
        }
    </style>
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
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            <div class="page-title">Performance</div>
            <div class="page-subtitle">
                {{ $setting ? $setting->semester.' '.$setting->school_year : 'No active enrollment period' }}
            </div>

            <div class="tab-bar">
                <a href="{{ route('admin.performance.index', ['tab' => 'physical_fitness']) }}"
                class="tab-btn {{ $tab === 'physical_fitness' ? 'active' : '' }}">
                    Physical Fitness
                </a>
                @if(auth()->user()->role !== 'GMA')
                    <a href="{{ route('admin.performance.index', ['tab' => 'quizzes']) }}"
                    class="tab-btn {{ $tab === 'quizzes' ? 'active' : '' }}">
                        Quizzes
                    </a>
                    <a href="{{ route('admin.performance.index', ['tab' => 'grade']) }}"
                    class="tab-btn {{ $tab === 'grade' ? 'active' : '' }}">
                        Grade
                    </a>
                @endif
            </div>

            @if(!$setting)
                <div class="card">
                    <div class="no-active">
                        &#128274; No active enrollment period.<br>
                        <small>Go to <a href="{{ route('admin.enrollment.settings') }}">Enrollment Settings</a> to open one.</small>
                    </div>
                </div>

            {{-- ==================== PHYSICAL FITNESS TAB ==================== --}}
            @elseif($tab === 'physical_fitness')

                <div class="stat-cards">
                    <div class="stat-card">
                        <div class="stat-label">Total Cadets</div>
                        <div class="stat-value stat-total">{{ $totalCadets }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">PFT Passed</div>
                        <div class="stat-value stat-pass">{{ $pftPassed }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">PFT Failed</div>
                        <div class="stat-value stat-fail">{{ $pftFailed }}</div>
                    </div>
                </div>

                <div class="stat-card">
        <div class="stat-label">Top Male</div>
        <div class="stat-value" style="font-size:16px;">
            @if($topMale)
                {{ $topMale['name'] }}<br>
                <span style="font-size:13px; color:#666;">{{ $topMale['overall'] }} — {{ $topMale['rating'] }}</span>
            @else — @endif
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Top Female</div>
        <div class="stat-value" style="font-size:16px;">
            @if($topFemale)
                {{ $topFemale['name'] }}<br>
                <span style="font-size:13px; color:#666;">{{ $topFemale['overall'] }} — {{ $topFemale['rating'] }}</span>
            @else — @endif
        </div>
    </div>

                <div class="card">
                    <div class="action-bar">
                        <form method="GET" action="{{ route('admin.performance.index') }}"
                              style="display:flex; gap:8px; flex:1;">
                            <input type="hidden" name="tab" value="physical_fitness">
                            <input type="text" name="search" class="search-input"
                                   placeholder="Search by name, ID, course"
                                   value="{{ request('search') }}">
                            <button type="submit" class="btn-green">Search</button>
                        </form>
                        <button class="btn-secondary">&#8679; Import CSV</button>
                    </div>

                   <form method="POST" action="{{ route('admin.performance.savePft') }}">
                    @csrf
                    @if($viewOnly)
                        <p style="color:#666; font-size:13px; margin-bottom:12px;">&#128065; View only — Physical Fitness scores are managed by GMA.</p>
                    @endif
                        @csrf
                        <table class="perf-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Sex</th>
                                    <th>Run Time (min)</th>
                                    <th>Run</th>
                                    <th>Push-ups</th>
                                    <th>P-ups</th>
                                    <th>Sit-ups</th>
                                    <th>Sit-ups</th>
                                    <th>Overall</th>
                                    <th>Rating</th> 
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cadets->filter(fn($c) => $c->cadet?->cadet_type === 'advance') as $cadet)
                                @php $pft = $pftScores[$cadet->id] ?? null; @endphp
                                <tr>
                                    <td>{{ $cadet->cadet->last_name }}, {{ $cadet->cadet->first_name }}</td>
                                    <td>{{ $cadet->cadet->sex === 'Male' ? 'M' : 'F' }}</td>
                                    <input type="hidden" name="pft[{{ $cadet->id }}][sex]"
                                           value="{{ $cadet->cadet->sex === 'Male' ? 'M' : 'F' }}">
                                    <td>
                                    <input type="number" step="0.01"
                                        name="pft[{{ $cadet->id }}][run_time]"
                                        value="{{ $pft?->run_time }}"
                                      @if($viewOnly) disabled @endif
                                </td>
                                    <td>
                                        @if($pft?->run_pass !== null)
                                            <span class="{{ $pft->run_pass ? 'pass-badge' : 'fail-badge' }}">
                                                {{ $pft->run_pass ? 'Pass' : 'Fail' }}
                                            </span>
                                        @else <span style="color:#ccc;">—</span> @endif
                                    </td>
                                    <td>
                                        <input type="number"
                                            name="pft[{{ $cadet->id }}][push_ups]"
                                            value="{{ $pft?->push_ups }}"
                                            @if(auth()->user()->role === 'S1') disabled @endif>
                                    </td>
                                    <td>
                                        @if($pft?->push_ups_pass !== null)
                                            <span class="{{ $pft->push_ups_pass ? 'pass-badge' : 'fail-badge' }}">
                                                {{ $pft->push_ups_pass ? 'Pass' : 'Fail' }}
                                            </span>
                                        @else <span style="color:#ccc;">—</span> @endif
                                    </td>
                                    <td>
                                        <input type="number"
                                            name="pft[{{ $cadet->id }}][sit_ups]"
                                            value="{{ $pft?->sit_ups }}"
                                            @if(auth()->user()->role === 'S1') disabled @endif>
                                    </td>
                                    <td>
                                        @if($pft?->sit_ups_pass !== null)
                                            <span class="{{ $pft->sit_ups_pass ? 'pass-badge' : 'fail-badge' }}">
                                                {{ $pft->sit_ups_pass ? 'Pass' : 'Fail' }}
                                            </span>
                                        @else <span style="color:#ccc;">—</span> @endif
                                        </td>
                                   <td><strong>{{ $pft?->overall ?? '—' }}</strong></td>
<td>
    @if($pft?->rating)
        <span class="rating-badge rating-{{ Str::slug($pft->rating) }}">{{ $pft->rating }}</span>
    @else <span style="color:#ccc;">—</span> @endif
</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" style="text-align:center; color:#666; padding:30px;">
                                        No advance cadets found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                @if(! $viewOnly)
                    <div style="display:flex; justify-content:flex-end; margin-top:16px;">
                        <button type="submit" class="btn-green">Save Changes</button>
                    </div>
                @endif

                    </form>
                </div>

            {{-- ==================== QUIZZES TAB ==================== --}}
            @elseif($tab === 'quizzes')

                @php
        $term = request('term', 'midterm');
    @endphp

    {{-- MIDTERM / FINAL --}}
    <div class="tab-bar" style="margin-bottom:16px;">
        <a href="{{ route('admin.performance.index', ['tab' => 'quizzes', 'term' => 'midterm']) }}"
           class="tab-btn {{ $term === 'midterm' ? 'active' : '' }}">
            Midterm
        </a>
        <a href="{{ route('admin.performance.index', ['tab' => 'quizzes', 'term' => 'final']) }}"
           class="tab-btn {{ $term === 'final' ? 'active' : '' }}">
            Final
        </a>
    </div>

                    <div class="stat-cards">
                    <div class="stat-card">
                        <div class="stat-label">Total Cadets</div>
                        <div class="stat-value stat-total">{{ $totalCadets }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Passed</div>
                        <div class="stat-value stat-pass">{{ $quizPassed }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Failed</div>
                        <div class="stat-value stat-fail">{{ $quizFailed }}</div>
                    </div>
                </div>

    <div class="card">
        <div class="action-bar">
    
            <form method="GET" action="{{ route('admin.performance.index') }}" style="display:flex; gap:8px; flex:1;">
                <input type="hidden" name="tab" value="quizzes">
                <input type="hidden" name="term" value="{{ $term }}">
                <input type="text" name="search" class="search-input"
                       placeholder="Search by name, ID, course"
                       value="{{ request('search') }}">
                <button type="submit" class="btn-green">Search</button>
            </form>
            <button class="btn-secondary">&#8679; Import CSV</button>

            <form method="POST" action="{{ route('admin.performance.addQuiz') }}">
                @csrf
                <input type="hidden" name="term" value="{{ $term }}">
                <button type="submit" class="btn-green">+ Add Quiz</button>
            </form>
        </div>

        <form method="POST" action="{{ route('admin.performance.saveQuizzes') }}">
            @csrf
            <input type="hidden" name="term" value="{{ $term }}">
            
                        <table class="perf-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    @foreach($quizSettings as $qs)
                                        <th>
                                            <input type="text"
                                                   name="quiz_label[{{ $qs->quiz_number }}]"
                                                   value="{{ $qs->quiz_label }}"
                                                   style="width:70px; background:transparent; border:none; color:#fff; font-weight:700; font-size:12px;">
                                        </th>
                                    @endforeach
                                    <th>Average</th>
                                    <th>Status</th>
                                </tr>
                                {{-- Max scores row --}}
                                <tr class="quiz-max-row">
                                    <td>Max Score</td>
                                    @foreach($quizSettings as $qs)
                                        <td>
                                            <input type="number" step="0.01"
                                                   name="quiz_max[{{ $qs->quiz_number }}]"
                                                   value="{{ $qs->max_score }}"
                                                   style="width:60px;">
                                        </td>
                                    @endforeach
                                    <td colspan="2"></td>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cadets as $cadet)
                                @php
                                    $cadetQuizzes = $quizScores[$cadet->id] ?? collect();
                                    $cadetQuizMap = $cadetQuizzes->keyBy('quiz_number');
                                    $avg = \App\Models\QuizScore::computeAverage($cadet->id, $setting->id, $term);
                                @endphp
                                <tr>
                                    <td>{{ $cadet->cadet->last_name }}, {{ $cadet->cadet->first_name }}</td>
                                    @foreach($quizSettings as $qs)
                                    <td>
                                        <input type="number" step="0.01"
                                               name="quiz[{{ $cadet->id }}][{{ $qs->quiz_number }}]"
                                               value="{{ $cadetQuizMap[$qs->quiz_number]?->score ?? '' }}">
                                    </td>
                                    @endforeach
                                    <td><strong>{{ $avg > 0 ? $avg.'%' : '—' }}</strong></td>
                                    <td>
                                        @if($avg > 0)
                                            <span class="{{ $avg >= 75 ? 'pass-badge' : 'fail-badge' }}">
                                                {{ $avg >= 75 ? 'Pass' : 'Fail' }}
                                            </span>
                                        @else
                                            <span style="color:#ccc;">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ $quizSettings->count() + 3 }}"
                                        style="text-align:center; color:#666; padding:30px;">
                                        No cadets found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div style="display:flex; justify-content:flex-end; margin-top:16px;">
                            <button type="submit" class="btn-green">Save Changes</button>
                        </div>
                    </form>
                </div>

               @elseif($tab === 'grade')

              
                    <div class="tab-bar" style="margin-bottom:16px;">
                        <a href="{{ route('admin.performance.index', [
                            'tab' => 'grade',
                            'term' => 'midterm'
                        ]) }}"
                        class="tab-btn {{ $term === 'midterm' ? 'active' : '' }}">
                            Midterm
                        </a>

                        <a href="{{ route('admin.performance.index', [
                            'tab' => 'grade',
                            'term' => 'final'
                        ]) }}"
                        class="tab-btn {{ $term === 'final' ? 'active' : '' }}">
                            Final
                        </a>
                    </div>

    <div class="stat-cards">

        <div class="stat-card">
            <div class="stat-label">Total Cadets</div>
            <div class="stat-value stat-total">
                {{ $totalCadets }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Passed</div>
            <div class="stat-value stat-pass">
                {{ $gradePassed }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Failed</div>
            <div class="stat-value stat-fail">
                {{ $gradeFailed }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Class Ave.</div>
            <div class="stat-value stat-total">
                {{ round($classAverage, 2) }}%
            </div>
        </div>

    </div>

    <div class="card">

        <div class="action-bar">

            <form method="GET"
                  action="{{ route('admin.performance.index') }}"
                  style="display:flex; gap:8px; flex:1;">

                <input type="hidden"
                       name="tab"
                       value="grade">

                <input type="hidden"
                       name="term"
                       value="{{ $term }}">

                <input type="text"
                       name="search"
                       class="search-input"
                       placeholder="Search by name, ID, course"
                       value="{{ request('search') }}">

                <button type="submit"
                        class="btn-green">
                    Search
                </button>

            </form>

            <button class="btn-secondary">
                &#8681; Export CSV
            </button>

        </div>

        <form method="POST"
              action="{{ route('admin.performance.saveGrades') }}">

            @csrf

            <input type="hidden"
                   name="term"
                   value="{{ $term }}">

            <table class="perf-table">

<thead>
    <tr>

        <th>Name</th>

        <th>Quizzes ({{ $setting->quizzes_weight }}%)</th>

        <th>Field Activities ({{ $setting->field_activities_weight }}%)</th>

        <th>Attendance ({{ $setting->attendance_weight }}%)</th>

        <th>{{ ucfirst($term) }} Exam ({{ $setting->midterm_exam_weight }}%) - 50pts</th>

        <th>Percentile</th>

        <th>Grade</th>

        <th>Status</th>

        <th>Remarks</th> 
       @if($term === 'final')
            <th>Semestral Grade</th>
            <th>Semestral Status</th>
        @endif

    </tr>
</thead>

                <tbody>

                    @forelse($cadets as $cadet)
                     @php
                        $avg = \App\Models\QuizScore::computeAverage($cadet->id, $setting?->id ?? 0);
                        $gr  = $gradeRecords[$cadet->id] ?? null;
                        $sem = $semestralGrades[$cadet->id] ?? null;
                    @endphp
                    <tr>
                            {{-- NAME --}}
                            <td>
                                {{ $cadet->cadet->last_name }},
                                {{ $cadet->cadet->first_name }}
                            </td>
                        <td>
                            @php $quizAvg = \App\Models\QuizScore::computeAverage($cadet->id, $setting?->id ?? 0); @endphp
                            {{ $quizAvg > 0 ? number_format($quizAvg, 2) : '—' }}
                        </td>


                                                       {{-- FIELD ACTIVITIES --}}
<td>
    <input
        type="number"
        step="0.01"
        min="0"
        max="100"
        name="grade[{{ $cadet->id }}][field_activities_score]"
        value="{{ $gr?->field_activities_score ?? '' }}"
        style="width:70px;"
    >
</td>

{{-- ATTENDANCE --}}
<td>
    @php $att = \App\Services\AttendanceCalculator::forCadet($cadet->id, $setting->id, $term); @endphp
    @if($att['max'])
        <strong>{{ $att['score'] }}</strong> / {{ $att['max'] }}
    @else
        <span style="color:#ccc;">No sessions yet</span>
    @endif
</td>

{{-- EXAM --}}
<td>
    <input
        type="number"
        step="0.01"
        min="0"
        max="50"
        name="grade[{{ $cadet->id }}][exam_score]"
        value="{{ $gr?->exam_score ?? '' }}"
        style="width:70px;"
    >
</td>

                            {{-- PERCENTILE --}}
                            <td>
                                <strong>
                                    {{ $gr?->final_score ?? '—' }}
                                </strong>
                            </td>

                            {{-- GRADE --}}
                            <td>
                                <strong>
                                    {{ $gr?->grade ?? '—' }}
                                </strong>
                            </td>

                            {{-- STATUS --}}
                            <td>

                                @if($gr?->status)

                                    <span class="{{
                                        $gr->status === 'pass'
                                            ? 'pass-badge'
                                            : 'fail-badge'
                                    }}">

                                        {{ ucfirst($gr->status) }}

                                    </span>

                                @else

                                    <span style="color:#ccc;">
                                        —
                                    </span>

                                @endif

                            </td>

                                        {{-- REMARKS --}}
                            <td>
                                {{ $gr?->remarks ?? '—' }}
                            </td>

                            @if($term === 'final')
                                {{-- SEMESTRAL GRADE --}}
                                <td>
                                    @if($sem)
                                        <strong>{{ $sem->grade }}</strong>
                                        <span style="font-size:11px; color:#888;">({{ $sem->percentile }}%)</span>
                                    @else
                                        <span style="color:#ccc;">—</span>
                                    @endif
                                </td>

                                {{-- SEMESTRAL STATUS --}}
                                <td>
                                    @if($sem?->status)
                                        <span class="{{ $sem->status === 'pass' ? 'pass-badge' : 'fail-badge' }}">
                                            {{ ucfirst($sem->status) }}
                                        </span>
                                    @else
                                        <span style="color:#ccc;">—</span>
                                    @endif
                                </td>
                            @endif

                        </tr>
                @empty
    <tr>
        <td colspan="{{ $term === 'final' ? 11 : 9 }}"
            style="text-align:center; color:#666; padding:30px;">
            No cadets found.
        </td>
    </tr>
@endforelse

                </tbody>

            </table>

            <div style="
                display:flex;
                justify-content:flex-end;
                margin-top:16px;
            ">

                <button type="submit"
                        class="btn-green">

                    Save Changes

                </button>

            </div>

        </form>

            {{-- END GRADE TAB --}}

        @endif

        </div>
    </div>
</div>

</body>
</html>

    

