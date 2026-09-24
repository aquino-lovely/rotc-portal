<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'cadet_id',
        'surname', 'first_name', 'middle_initial','suffix',
        'contact_no', 'relationship',
        'barangay', 'city_municipality', 'province',
    ];

    public function cadet()
    {
        return $this->belongsTo(Cadet::class);
    }
}