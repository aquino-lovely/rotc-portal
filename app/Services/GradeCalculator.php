<?php

namespace App\Services;

/**
 * Mirrors the grading spreadsheet:
 *   EQ            = (score / max) * 50 + 50          (BK5/$BK$3*$BU$7+$BV$7, both 50)
 *   term          = ROUND(SUM(weighted components), 0)
 *   semestral     = ROUND(midterm * w1 + final * w2, 0)
 *   numerical     = lookup on the school's grading scale
 */
class GradeCalculator
{
    public const MULTIPLIER = 50; // BU7
    public const BASE       = 50; // BV7

    /** Minimum percentile => numerical grade (from the grading system table). */
    private const SCALE = [
        97 => 1.00,
        94 => 1.25,
        91 => 1.50,
        88 => 1.75,
        85 => 2.00,
        82 => 2.25,
        79 => 2.50,
        76 => 2.75,
        75 => 3.00,
    ];
    private const FAILED = 5.00;

    /**
     * @param array $weights keys: quizzes, field_activities, attendance, exam, first_term, second_term (all in %)
     */
    public function __construct(private array $weights)
    {
    }

    /** Build from the enrollment period settings model. */
    public static function fromSetting($setting): self
    {
        return new self([
            'quizzes'          => (float) ($setting->quizzes_weight ?? 0),
            'field_activities' => (float) ($setting->field_activities_weight ?? 30),
            'attendance'       => (float) ($setting->attendance_weight ?? 40),
            'exam'             => (float) ($setting->midterm_exam_weight ?? 30),
            'first_term'       => (float) ($setting->first_term_weight ?? 40),
            'second_term'      => (float) ($setting->second_term_weight ?? 60),
        ]);
    }

    /** EQ on a 50-100 scale. A blank score or a missing max gives the base of 50, as in the sheet. */
    public function eq(?float $score, ?float $max): float
    {
        if ($score === null || $max === null || $max <= 0) {
            return (float) self::BASE;
        }

        return ($score / $max) * self::MULTIPLIER + self::BASE;
    }

    /**
     * Term (Midterm or Final) percentile.
     *
     * @param array $scores each key => ['score' => ?float, 'max' => ?float]
     *                      keys: quizzes, field_activities, attendance, exam
     */
    public function termPercentile(array $scores): int
    {
        $sum = 0.0;

        foreach (['quizzes', 'field_activities', 'attendance', 'exam'] as $key) {
            $score = $scores[$key]['score'] ?? null;
            $max   = $scores[$key]['max'] ?? null;

            $sum += $this->eq($score, $max) * ($this->weights[$key] / 100);
        }

        // The sheet rounds T to 2 decimals, then the percentile to a whole number.
        return (int) round(round($sum, 2), 0);
    }

    /** Semestral percentile from the two rounded term percentiles. */
    public function semestralPercentile(int $midterm, int $final): int
    {
        $value = $midterm * ($this->weights['first_term'] / 100)
               + $final   * ($this->weights['second_term'] / 100);

        return (int) round(round($value, 2), 0);
    }

    /**
     * True if at least one component that carries a weight above 0 has a score entered.
     * Used to decide whether a cadet has been graded at all (a 0% component such as
     * Quizzes is ignored, so it can't create a grade on its own).
     */
    public function hasAnyScore(array $scores): bool
    {
        foreach (['quizzes', 'field_activities', 'attendance', 'exam'] as $key) {
            if (($this->weights[$key] ?? 0) > 0 && ($scores[$key]['score'] ?? null) !== null) {
                return true;
            }
        }

        return false;
    }

    /** Percentile -> numerical grade (1.00 to 3.00, or 5.00 if failed). */
    public function numericalGrade(int $percentile): float
    {
        foreach (self::SCALE as $min => $grade) {
            if ($percentile >= $min) {
                return $grade;
            }
        }

        return self::FAILED;
    }

    /** Convenience: semestral percentile and numerical grade in one call. */
    public function semestral(int $midterm, int $final): array
    {
        $percentile = $this->semestralPercentile($midterm, $final);

        return [
            'percentile' => $percentile,
            'numerical'  => number_format($this->numericalGrade($percentile), 2),
        ];
    }
}

/*
 * Usage:
 *
 * $calc = GradeCalculator::fromSetting($setting);
 *
 * $midterm = $calc->termPercentile([
 *     'quizzes'          => ['score' => null, 'max' => null], // 0% weight
 *     'field_activities' => ['score' => 10,   'max' => 10],
 *     'attendance'       => ['score' => 6,    'max' => 8],
 *     'exam'             => ['score' => 50,   'max' => 50],
 * ]);
 *
 * $result = $calc->semestral($midterm, $final);  // ['percentile' => 88, 'numerical' => '1.75']
 *
 * Statuses that are not computed (4.00 Conditional, Drp, Inc) should be set manually.
 */