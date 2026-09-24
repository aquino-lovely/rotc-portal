<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrollmentSetting extends Model
{
   protected $fillable = [
    'school_year',
    'semester',
    'is_open',
    'unit',
    'time',
    'start_date',
    'end_date',
    'class_start_date',
    'class_end_date',
    'finalized',
    'finalized_at',
    'finalized_by',

    // grade weights (shared by Midterm and Final)
    'quizzes_weight',
    'field_activities_weight',
    'attendance_weight',
    'midterm_exam_weight',

    // semestral split
    'first_term_weight',
    'second_term_weight',
];

    protected $casts = [
        'is_open'    => 'boolean',
        'start_date' => 'date',
        'end_date'   => 'date',
         'class_start_date' => 'date',
         'class_end_date'   => 'date',
         'finalized'    => 'boolean',
         'finalized_at' => 'datetime',
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Whether this setting is open right now, factoring in
     * the manual override AND the start/end date window.
     */
    public function isCurrentlyOpen(): bool
    {
        if (!$this->is_open) {
            return false; // admin manually closed it, dates don't matter
        }

        $today = now()->startOfDay();

        if ($this->start_date && $today->lt($this->start_date)) {
            return false; // hasn't started yet
        }

        if ($this->end_date && $today->gt($this->end_date)) {
            return false; // deadline passed
        }

        return true;
    }

    /**
     * Latest setting marked open by the admin (manual override).
     * Note: this does NOT check dates — call ->isCurrentlyOpen()
     * on the result if you need the real "can cadets enroll right now" answer.
     */
    public static function getActive()
{
    return static::where('is_open', true)->latest('updated_at')->first();
}
    public function isFinalized(): bool
{
    return (bool) $this->finalized;
}
}