<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    use HasFactory;

    public const STATUSES = [
        'active' => 'Aktif',
        'inactive' => 'Nonaktif',
        'blocked' => 'Diblokir',
    ];

    public const DISTRICTS = [
        'Bukit Intan', 'Gabek', 'Gerunggang', 'Girimaya',
        'Pangkal Balam', 'Rangkui', 'Taman Sari',
    ];

    protected $fillable = [
        'customer_number',
        'full_name',
        'email',
        'phone',
        'gender',
        'district',
        'address',
        'status',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'customer_id');
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'customer_id');
    }

    public function surveyResponses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class, 'customer_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getGenderLabelAttribute(): string
    {
        return $this->gender === 'female' ? 'Perempuan' : 'Laki-laki';
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', trim($this->full_name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }

    public static function generateNumber(): string
    {
        $prefix = 'TP-'.now()->format('ym');
        $last = static::where('customer_number', 'like', $prefix.'%')
            ->orderByDesc('customer_number')
            ->value('customer_number');
        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
