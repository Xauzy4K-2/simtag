<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyQuestion extends Model
{
    use HasFactory;

    public const TYPES = [
        'rating' => 'Rating bintang (1-5)',
        'multiple_choice' => 'Pilihan ganda',
        'text' => 'Jawaban teks',
    ];

    public const RATING_LABELS = [
        1 => 'Sangat Tidak Puas',
        2 => 'Tidak Puas',
        3 => 'Cukup Puas',
        4 => 'Puas',
        5 => 'Sangat Puas',
    ];

    protected $fillable = [
        'survey_id',
        'question_text',
        'question_type',
        'options',
        'display_order',
        'is_required',
    ];

    protected $casts = [
        'options' => 'array',
        'display_order' => 'integer',
        'is_required' => 'boolean',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class, 'question_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->question_type] ?? $this->question_type;
    }
}
