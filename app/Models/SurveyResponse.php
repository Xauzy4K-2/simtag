<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyResponse extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'survey_id',
        'customer_id',
        'suggestion',
        'average_score',
        'submitted_at',
    ];

    protected $casts = [
        'average_score' => 'float',
        'submitted_at' => 'datetime',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class, 'response_id');
    }

    public static function satisfactionLabel(?float $score): string
    {
        return match (true) {
            $score === null => 'Belum ada nilai',
            $score >= 4.5 => 'Sangat Puas',
            $score >= 3.5 => 'Puas',
            $score >= 2.5 => 'Cukup Puas',
            $score >= 1.5 => 'Tidak Puas',
            default => 'Sangat Tidak Puas',
        };
    }

    /** Indeks Kepuasan dalam skala 0-100 dari skor rata-rata 1-5. */
    public static function satisfactionIndex(?float $score): ?float
    {
        return $score === null ? null : round($score / 5 * 100, 1);
    }
}
