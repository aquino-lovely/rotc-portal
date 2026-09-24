<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cadet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name', 'middle_name', 'last_name', 'suffix',
        'birthday', 'age', 'sex', 'civil_status', 'religion',
        'blood_type', 'height_cm', 'weight_kg', 'skill_1', 'skill_2',
        'barangay', 'city_municipality', 'province', 'contact_no', 'campus','serial_number',
        'student_number', 'course', 'year_section',
        'photo_path','cadet_type','rank', 'company', 'platoon',
    ];

    protected $casts = [
        'birthday' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function beneficiary()
    {
        return $this->hasOne(Beneficiary::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}