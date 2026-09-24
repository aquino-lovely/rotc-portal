<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSetting extends Model
{
    protected $fillable = [
        'enrollment_setting_id',
         'term',
        'quiz_number',
        'quiz_label',
        'max_score',
    ];

    public function setting()
    {
        return $this->belongsTo(EnrollmentSetting::class, 'enrollment_setting_id');
    }
}