<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\EnrollmentSetting;

class GradeRecord extends Model
{
    protected $fillable = [
    'user_id', 'enrollment_setting_id', 'enrollment_id', 'term',
    'field_activities_score', 'attendance_score', 'exam_score',
    'final_score', 'grade', 'status', 'remarks',
];

    protected $casts = [
        'attendance_score' => 'float',
        'exam_score' => 'float',
        'final_score' => 'float',
        'field_activities_score' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setting()
    {
        return $this->belongsTo(
            EnrollmentSetting::class,
            'enrollment_setting_id'
        );
    }

    public function enrollment()
    {
        return $this->belongsTo(
            Enrollment::class,
            'enrollment_id'
        );
    }

    /**
     * Compute final score based on weights.
     */
    public static function computeFinalScore(
        float $pftScore,
        float $attendanceScore,
        float $quizAverage,
        float $examScore,
        float $pftWeight,
        float $attendanceWeight,
        float $quizzesWeight,
        float $examWeight
    ): float {
        $examPercent = ($examScore / 50) * 100;

        $final = ($pftScore * $pftWeight / 100)
            + ($attendanceScore * $attendanceWeight / 100)
            + ($quizAverage * $quizzesWeight / 100)
            + ($examPercent * $examWeight / 100);

        return round($final, 2);
    }

    /**
     * Convert final score to numerical grade.
     */
    public static function computeGrade(float $finalScore): float
    {
        if ($finalScore >= 97) return 1.00;
        if ($finalScore >= 94) return 1.25;
        if ($finalScore >= 91) return 1.50;
        if ($finalScore >= 88) return 1.75;
        if ($finalScore >= 85) return 2.00;
        if ($finalScore >= 82) return 2.25;
        if ($finalScore >= 79) return 2.50;
        if ($finalScore >= 76) return 2.75;
        if ($finalScore >= 75) return 3.00;

        return 5.00;
    }

    /**
     * Compute remarks.
     */
    public static function computeRemarks(float $finalScore): string
    {
        if ($finalScore >= 94) return 'Excellent';
        if ($finalScore >= 88) return 'Very Good';
        if ($finalScore >= 85) return 'Good';
        if ($finalScore >= 75) return 'Satisfactory';

        return 'Failed';
    }

    /**
     * Compute status.
     */
    public static function computeStatus(float $finalScore): string
    {
        return $finalScore >= 75 ? 'pass' : 'fail';
    }
}