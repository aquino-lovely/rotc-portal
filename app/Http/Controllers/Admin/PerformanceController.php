<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnrollmentSetting;
use App\Models\Enrollment;
use App\Models\GradeRecord;
use App\Models\PhysicalFitness;
use App\Models\QuizScore;
use App\Models\QuizSetting;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\GradeCalculator;
use App\Models\SemestralGrade;
use App\Services\AttendanceCalculator;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $setting  = EnrollmentSetting::getActive();
        $tab      = $request->get('tab', 'physical_fitness');
          $term     = $request->get('term', 'midterm'); 
        $search   = $request->get('search');

    $cadets = User::where('role', 'cadet')
    ->where('status', 'approved')
    ->whereHas('enrollments', function ($q) use ($setting) {
        $q->where('enrollment_setting_id', $setting?->id ?? 0);
    })
    ->with('cadet')
    ->when($search, function ($q) use ($search) {
        $q->whereHas('cadet', function ($c) use ($search) {
            $c->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('student_number', 'like', "%{$search}%");
        });
    })
    ->get();

       $quizSettings = $setting
    ? QuizSetting::where('enrollment_setting_id', $setting->id)
        ->where('term', $term)
        ->orderBy('quiz_number')
        ->get()
    : collect();

        // Load existing scores
        $pftScores   = $setting
            ? PhysicalFitness::where('enrollment_setting_id', $setting->id)
                ->get()->keyBy('user_id')
            : collect();

        $quizScores  = $setting
    ? QuizScore::where('enrollment_setting_id', $setting->id)
        ->where('term', $term)
        ->get()->groupBy('user_id')
    : collect();

      $gradeRecords = $setting
    ? GradeRecord::where('enrollment_setting_id', $setting->id)
        ->where('term', $term)
        ->get()->keyBy('user_id')
    : collect();

    $semestralGrades = $setting
    ? SemestralGrade::where('enrollment_setting_id', $setting->id)->get()->keyBy('user_id')
    : collect();

        // Stats
$totalCadets = $cadets->count();

$scored    = $pftScores->filter(fn($p) => $p->overall !== null);
$pftPassed = $scored->filter(fn($p) => $p->run_pass && $p->push_ups_pass && $p->sit_ups_pass)->count();
$pftFailed = $scored->count() - $pftPassed;

$topMale = null;
$topFemale = null;

foreach ($cadets as $cadet) {
    $pft = $pftScores[$cadet->id] ?? null;
    if (!$pft || $pft->overall === null) continue;

    $sex = $cadet->cadet->sex === 'Male' ? 'M' : 'F';
    $entry = [
        'name'    => $cadet->cadet->last_name . ', ' . $cadet->cadet->first_name,
        'overall' => $pft->overall,
        'rating'  => $pft->rating,
    ];

    if ($sex === 'M' && (!$topMale || $pft->overall > $topMale['overall'])) {
        $topMale = $entry;
    }
    if ($sex === 'F' && (!$topFemale || $pft->overall > $topFemale['overall'])) {
        $topFemale = $entry;
    }
} 
    $quizPassed = 0;
    $quizFailed = 0;

foreach ($cadets as $cadet) {
    $avg = QuizScore::computeAverage($cadet->id, $setting?->id ?? 0, $term);
    if ($avg >= 75) $quizPassed++;
    else $quizFailed++;
}

        $gradePassed  = $gradeRecords->where('status', 'pass')->count();
         $gradeFailed  = $gradeRecords->where('status', 'fail')->count();
 $classAverage = $gradeRecords->avg('final_score') ?? 0;

  return view('admin.performance.index', compact(
    'setting', 'tab', 'term', 'cadets', 'quizSettings',
     'pftScores', 'quizScores', 'gradeRecords',
    'totalCadets', 'pftPassed', 'pftFailed',
    'topMale', 'topFemale',
    'quizPassed', 'quizFailed',
    'gradePassed', 'gradeFailed', 'classAverage','semestralGrades'
));
    }

    public function savePft(Request $request)
    {
        $request->validate([
            'pft' => 'required|array',
            'pft.*.sex' => 'nullable|in:M,F',
            'pft.*.run_time' => 'nullable|numeric|min:0',
            'pft.*.push_ups' => 'nullable|integer|min:0',
            'pft.*.sit_ups' => 'nullable|integer|min:0',
        ]);

        $setting = EnrollmentSetting::getActive();
        if (!$setting) return back()->with('error', 'No active enrollment period.');

        if ($setting->isFinalized()) {
            return back()->with('error', 'This term has been finalized. PFT scores can no longer be changed.');
        }
         $term = $request->get('term', 'midterm'); 

        $data = $request->input('pft', []);

        foreach ($data as $userId => $row) {
            $sex       = $row['sex'] ?? 'M';
            $runTime   = isset($row['run_time'])  ? (float) $row['run_time']  : null;
            $pushUps   = isset($row['push_ups'])  ? (int)   $row['push_ups']  : null;
            $sitUps    = isset($row['sit_ups'])   ? (int)   $row['sit_ups']   : null;

            // Pass/fail standards
            $runPass     = $runTime  !== null ? ($sex === 'M' ? $runTime  <= 18 : $runTime  <= 21) : null;
            $pushUpsPass = $pushUps  !== null ? ($sex === 'M' ? $pushUps  >= 42 : $pushUps  >= 19) : null;
            $sitUpsPass  = $sitUps   !== null ? ($sex === 'M' ? $sitUps   >= 50 : $sitUps   >= 35) : null;

            $overall = ($runTime !== null && $pushUps !== null && $sitUps !== null)
    ? PhysicalFitness::computeOverall($runTime, $pushUps, $sitUps, $sex)
    : null;

            PhysicalFitness::updateOrCreate(
                ['user_id' => $userId, 'enrollment_setting_id' => $setting->id],
                [
                    'run_time'      => $runTime,
                    'push_ups'      => $pushUps,
                    'sit_ups'       => $sitUps,
                    'run_pass'      => $runPass,
                    'push_ups_pass' => $pushUpsPass,
                    'sit_ups_pass'  => $sitUpsPass,
                    'overall'       => $overall,
                ]
            );
        }

        return back()->with('success', 'Physical Fitness scores saved.');
    }

    // Save Quiz scores
    public function saveQuizzes(Request $request)
    {
        $setting = EnrollmentSetting::getActive();
        if (!$setting) return back()->with('error', 'No active enrollment period.');

        if ($setting->isFinalized()) {
            return back()->with('error', 'This term has been finalized. Quiz scores can no longer be changed.');
        }

         $term = $request->input('term', 'midterm');

        // Save quiz settings (max scores) first
        $quizMaxes = $request->input('quiz_max', []);
        $quizLabels = $request->input('quiz_label', []);

         QuizSetting::where('enrollment_setting_id', $setting->id)
        ->where('term', $term)
        ->delete();

    
        foreach ($quizMaxes as $num => $max) {
            QuizSetting::create([
                'enrollment_setting_id' => $setting->id,
                 'term'                  => $term,
                'quiz_number'           => $num,
                'quiz_label'            => $quizLabels[$num] ?? "Quiz {$num}",
                'max_score'             => (float) $max,
            ]);
        }

        // Save cadet quiz scores
        $data = $request->input('quiz', []);
        foreach ($data as $userId => $quizzes) {
            foreach ($quizzes as $quizNum => $score) {
                $max = (float) ($quizMaxes[$quizNum] ?? 0);
                QuizScore::updateOrCreate(
                    [
                        'user_id'               => $userId,
                        'enrollment_setting_id' => $setting->id,
                         'term'                  => $term,
                        'quiz_number'           => $quizNum,
                    ],
                    [
                        'score'     => (float) $score,
                        'max_score' => $max,
                    ]
                );
            }
        }

        return back()->with('success', 'Quiz scores saved.');
    }

    public function addQuiz(Request $request)
{
    $setting = EnrollmentSetting::getActive();
    if (!$setting) return back()->with('error', 'No active enrollment period.');

    if ($setting->isFinalized()) {
        return back()->with('error', 'This term has been finalized. No new quizzes can be added.');
    }

    $term = $request->input('term', 'midterm');

    $lastQuiz = QuizSetting::where('enrollment_setting_id', $setting->id)
        ->where('term', $term)
        ->max('quiz_number') ?? 0;

    $newNum = $lastQuiz + 1;

    QuizSetting::create([
        'enrollment_setting_id' => $setting->id,
        'term'                  => $term,
        'quiz_number'           => $newNum,
        'quiz_label'            => "Quiz {$newNum}",
        'max_score'             => 20,
    ]);

    return back()->with('success', "Quiz {$newNum} added.");
}

         public function saveGrades(Request $request)
    {
        $setting = EnrollmentSetting::getActive();
        if (!$setting) return back()->with('error', 'No active enrollment period.');

        if ($setting->isFinalized()) {
            return back()->with('error', 'This term has been finalized. Grades can no longer be changed.');
        }

        $request->validate([
            'term'                           => 'required|in:midterm,final',
            'grade.*.field_activities_score' => 'nullable|numeric|min:0|max:100',
            'grade.*.exam_score'             => 'nullable|numeric|min:0|max:50',
        ]);

        $term = $request->input('term');
        $calc = GradeCalculator::fromSetting($setting);
        $num  = fn ($v) => is_numeric($v) ? (float) $v : null;

        foreach ($request->input('grade', []) as $userId => $row) {
            $enrollment = Enrollment::where('user_id', $userId)
                ->where('enrollment_setting_id', $setting->id)
                ->first();

            if (!$enrollment) {
                continue;
            }

            $fa   = $num($row['field_activities_score'] ?? null);
           $attData = AttendanceCalculator::forCadet($userId, $setting->id, $term);
            $exam = $num($row['exam_score'] ?? null);

            $quizAvg = QuizScore::computeAverage($userId, $setting->id);
            $quiz    = $quizAvg > 0 ? (float) $quizAvg : null;

            $scores = [
                'quizzes'          => ['score' => $quiz, 'max' => 100],
                'field_activities' => ['score' => $fa,   'max' => 100],
               'attendance'       => ['score' => $attData['score'], 'max' => $attData['max']],
                'exam'             => ['score' => $exam, 'max' => 50],
            ];

            $key = [
                'user_id'               => $userId,
                'enrollment_setting_id' => $setting->id,
                'term'                  => $term,
            ];

            if (!$calc->hasAnyScore($scores)) {
                GradeRecord::where($key)->delete();
                continue;
            }

            $percentile = $calc->termPercentile($scores);
            $numerical  = $calc->numericalGrade($percentile);

            GradeRecord::updateOrCreate($key, [
                'enrollment_id'          => $enrollment->id,
                'field_activities_score' => $fa,
                'attendance_score' => $attData['score'] ?? 0,
                'exam_score'             => $exam ?? 0,
                'final_score'            => $percentile,
                'grade'                  => number_format($numerical, 2),
                'status'                 => $numerical <= 3.00 ? 'pass' : 'fail',
                'remarks'                => GradeRecord::computeRemarks($percentile),
            ]);

            $midtermRec = GradeRecord::where(['user_id' => $userId, 'enrollment_setting_id' => $setting->id, 'term' => 'midterm'])->first();
$finalRec   = GradeRecord::where(['user_id' => $userId, 'enrollment_setting_id' => $setting->id, 'term' => 'final'])->first();

if ($midtermRec && $finalRec) {
    $result = $calc->semestral($midtermRec->final_score, $finalRec->final_score);

    SemestralGrade::updateOrCreate(
        ['user_id' => $userId, 'enrollment_setting_id' => $setting->id],
        [
            'percentile' => $result['percentile'],
            'grade'      => $result['numerical'],
            'status'     => $result['numerical'] <= 3.00 ? 'pass' : 'fail',
        ]
    );
}
        }

        return back()->with('success', 'Grades saved and computed.');
    }   
}