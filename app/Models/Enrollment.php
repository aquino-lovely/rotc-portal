<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GradeRecord;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id',
        'enrollment_setting_id',
        'status',
        'rejection_reason',
        'finalized_at',
        'school_year',
        'semester',
        'is_open',
        'unit',
        'time',
        ];

    protected $casts = [
        'finalized_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setting()
    {
        return $this->belongsTo(EnrollmentSetting::class, 'enrollment_setting_id');
    }

    public function physicalFitness()
    {
        return $this->hasMany(PhysicalFitness::class);
    }

    public function quizScores()
    {
        return $this->hasMany(QuizScore::class);
    }

    public function gradeRecords()
    {
        return $this->hasMany(GradeRecord::class);
    }
    public function gradeRecord()
{
    return $this->hasOne(GradeRecord::class, 'enrollment_id', 'id');
}
public static function resolveEligibilityFor($user, $setting): array
{
    if (self::hasPassedLevel($user->id, 1) && self::hasPassedLevel($user->id, 2)) {
        return ['blocked' => true, 'reason' => 'This cadet has already completed NSTP/ROTC and is not required to enroll.', 'subject_level' => null, 'is_retake' => false];
    }

    if ($setting->semester !== 'Second Semester') {
        // First Semester = Level 1 only
        $tookLevel1 = self::where('user_id', $user->id)->where('subject_level', 1)->exists();

        if ($tookLevel1) {
            if (!self::hasGradeForLevel($user->id, 1)) {
                return ['blocked' => true, 'reason' => "This cadet's First Semester grade has not been posted yet.", 'subject_level' => null, 'is_retake' => false];
            }

            if (self::hasPassedLevel($user->id, 1)) {
                return ['blocked' => true, 'reason' => 'This cadet has already passed Military Science 1. Enroll in Second Semester for Military Science 2 instead.', 'subject_level' => null, 'is_retake' => false];
            }

            // Failed level 1 previously -> allow retake
            return ['blocked' => false, 'reason' => null, 'subject_level' => 1, 'is_retake' => true];
        }

        // No prior attempt -> fresh enrollment
        return ['blocked' => false, 'reason' => null, 'subject_level' => 1, 'is_retake' => false];
    }

    // Second Semester = Level 2 (requires Level 1 passed first)
    $tookLevel1 = self::where('user_id', $user->id)->where('subject_level', 1)->exists();

    if (!$tookLevel1) {
        return ['blocked' => true, 'reason' => 'No First Semester ROTC 1 enrollment on record for this cadet.', 'subject_level' => null, 'is_retake' => false];
    }

    if (!self::hasGradeForLevel($user->id, 1)) {
        return ['blocked' => true, 'reason' => "This cadet's First Semester grade has not been posted yet.", 'subject_level' => null, 'is_retake' => false];
    }

    if (!self::hasPassedLevel($user->id, 1)) {
        return ['blocked' => true, 'reason' => 'This cadet must pass Military Science 1 before enrolling in Military Science 2.', 'subject_level' => null, 'is_retake' => false];
    }

    // Level 1 passed -> check Level 2 history
    $tookLevel2 = self::where('user_id', $user->id)->where('subject_level', 2)->exists();

    if ($tookLevel2) {
        if (!self::hasGradeForLevel($user->id, 2)) {
            return ['blocked' => true, 'reason' => "This cadet's Military Science 2 grade has not been posted yet.", 'subject_level' => null, 'is_retake' => false];
        }

        if (self::hasPassedLevel($user->id, 2)) {
            return ['blocked' => true, 'reason' => 'This cadet has already passed Military Science 2.', 'subject_level' => null, 'is_retake' => false];
        }

        // Failed level 2 previously -> allow retake
        return ['blocked' => false, 'reason' => null, 'subject_level' => 2, 'is_retake' => true];
    }

    // First time attempting level 2
    return ['blocked' => false, 'reason' => null, 'subject_level' => 2, 'is_retake' => false];
}

public static function hasGradeForLevel(int $userId, int $level): bool
{
    return self::where('user_id', $userId)
        ->where('subject_level', $level)
        ->whereHas('gradeRecord', fn ($q) => $q->whereNotNull('status'))
        ->exists();
}

public static function hasPassedLevel(int $userId, int $level): bool
{
    return self::where('user_id', $userId)
        ->where('subject_level', $level)
        ->whereHas('gradeRecord', fn ($q) => $q->where('status', 'pass'))
        ->exists();
}
}