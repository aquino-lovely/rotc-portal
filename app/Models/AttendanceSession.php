<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $fillable = [
        'enrollment_setting_id',
        'session_date',
        'cutoff_time',
        'qr_token',
        'qr_expires_at',
        'is_active',
    ];

    protected $casts = [
        'session_date'  => 'date',
        'qr_expires_at' => 'datetime',
        'is_active'     => 'boolean',
    ];

    public function setting()
    {
        return $this->belongsTo(EnrollmentSetting::class, 'enrollment_setting_id');
    }

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}