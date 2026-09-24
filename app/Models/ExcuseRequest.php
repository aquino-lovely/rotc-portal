<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcuseRequest extends Model
{
    protected $fillable = [
        'user_id',
        'enrollment_setting_id',
        'training_date',
        'reason',
        'explanation',
        'document_path',
        'status',
    ];

    protected $casts = [
        'training_date' => 'date',
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