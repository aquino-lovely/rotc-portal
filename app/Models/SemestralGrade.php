<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SemestralGrade extends Model
{
    protected $fillable = [
        'user_id',
        'enrollment_setting_id',
        'percentile',
        'grade',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setting()
    {
        return $this->belongsTo(EnrollmentSetting::class, 'enrollment_setting_id');
    }
}