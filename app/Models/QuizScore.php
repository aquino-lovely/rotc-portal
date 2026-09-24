<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizScore extends Model
{
    protected $fillable = [
        'user_id',
        'enrollment_setting_id',
        'term',
        'quiz_number',
        'score',
        'max_score',
    ];

    public static function computeAverage($userId, $settingId, $term = 'midterm')
{
    $scores = static::where('user_id', $userId)
        ->where('enrollment_setting_id', $settingId)
        ->where('term', $term)
        ->get();

    if ($scores->isEmpty()) {
        return 0;
    }

    $total = $scores->sum(function ($s) {
        return $s->max_score > 0 ? ($s->score / $s->max_score) * 100 : 0;
    });

    return round($total / $scores->count(), 2);
}
}