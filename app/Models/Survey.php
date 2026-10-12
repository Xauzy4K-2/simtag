<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    use HasFactory;

    public const STATUSES = [
        'draft' => 'Draf',
        'published' => 'Aktif',
        'closed' => 'Ditutup',
    ];

    protected $fillable = [
        'title',
        'description',
        'status',
        'ends_at',
        'published_at',
        'closed_at',
        'created_by',
    ];

    protected $casts = [
        'ends_at' => 'date',
        'published_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class, 'survey_id')->orderBy('display_order')->orderBy('id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class, 'survey_id');
    }

    /** Survei yang sedang dibuka dan belum melewati batas akhir. */
    public function scopeOpenForResponses(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhereDate('ends_at', '>=', today()));
    }

    public function isOpen(): bool
    {
        return $this->status === 'published' && (! $this->ends_at || $this->ends_at->gte(today()));
    }

    /** Kunci status untuk tampilan badge (draft|published|expired|closed). */
    public function getDisplayStatusAttribute(): string
    {
        return $this->status === 'published' && ! $this->isOpen() ? 'expired' : $this->status;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->display_status === 'expired' ? 'Berakhir' : (self::STATUSES[$this->status] ?? $this->status);
    }
}
