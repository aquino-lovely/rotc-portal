<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalFitness extends Model
{
    protected $table = 'physical_fitness';

    protected $fillable = [
        'user_id',
        'enrollment_setting_id',
        'run_time',
        'push_ups',
        'sit_ups',
        'run_pass',
        'push_ups_pass',
        'sit_ups_pass',
        'overall',
    ];

    protected $casts = [
        'run_pass'      => 'boolean',
        'push_ups_pass' => 'boolean',
        'sit_ups_pass'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setting()
    {
        return $this->belongsTo(EnrollmentSetting::class, 'enrollment_setting_id');
    }

    // app/Models/PhysicalFitness.php

public static function computeEventPoints(float $value, string $event, string $sex): float
{
    // Placeholder anchors — replace with your unit's actual standards if you have them.
    $standards = [
        'run_time' => [ // lower is better (minutes)
            'M' => ['excellent' => 14,   'pass' => 17, 'fail_floor' => 22],
            'F' => ['excellent' => 16.5, 'pass' => 21, 'fail_floor' => 25],
        ],
        'push_ups' => [ // higher is better
            'M' => ['excellent' => 60, 'pass' => 43, 'fail_floor' => 20],
            'F' => ['excellent' => 30, 'pass' => 19, 'fail_floor' => 8],
        ],
        'sit_ups' => [ // higher is better
            'M' => ['excellent' => 65, 'pass' => 46, 'fail_floor' => 25],
            'F' => ['excellent' => 48, 'pass' => 35, 'fail_floor' => 15],
        ],
    ];

    $s = $standards[$event][$sex];
    $lowerIsBetter = $event === 'run_time';

    if ($lowerIsBetter) {
        if ($value <= $s['excellent'])  return 100;
        if ($value <= $s['pass'])       return 100 - (($value - $s['excellent']) / ($s['pass'] - $s['excellent'])) * 40;
        if ($value <= $s['fail_floor']) return 60 - (($value - $s['pass']) / ($s['fail_floor'] - $s['pass'])) * 60;
        return 0;
    }

    if ($value >= $s['excellent'])  return 100;
    if ($value >= $s['pass'])       return 60 + (($value - $s['pass']) / ($s['excellent'] - $s['pass'])) * 40;
    if ($value >= $s['fail_floor']) return (($value - $s['fail_floor']) / ($s['pass'] - $s['fail_floor'])) * 60;
    return 0;
}

public static function computeOverall(float $runTime, int $pushUps, int $sitUps, string $sex): float
{
    $runPts  = self::computeEventPoints($runTime, 'run_time', $sex);
    $pushPts = self::computeEventPoints($pushUps, 'push_ups', $sex);
    $sitPts  = self::computeEventPoints($sitUps, 'sit_ups', $sex);

    return round(($runPts + $pushPts + $sitPts) / 3, 2);
}

public function getRatingAttribute(): ?string
{
    if ($this->overall === null) return null;

    return match (true) {
        $this->overall >= 90 => 'Excellent',
        $this->overall >= 75 => 'Very Satisfactory',
        $this->overall >= 60 => 'Satisfactory',
        default => 'Failed',
    };
}
}