<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLES = [
        'admin' => 'Administrator',
        'staff' => 'Petugas',
        'customer' => 'Pelanggan',
    ];

    public const STATUSES = [
        'active' => 'Aktif',
        'inactive' => 'Nonaktif',
        'suspended' => 'Ditangguhkan',
    ];

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'password_hash',
        'role',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function passwordResetTokens(): HasMany
    {
        return $this->hasMany(PasswordResetToken::class, 'user_id');
    }

    public function createdSurveys(): HasMany
    {
        return $this->hasMany(Survey::class, 'created_by');
    }

    public function handledComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'handled_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isInternal(): bool
    {
        return in_array($this->role, ['admin', 'staff'], true);
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function homeRoute(): string
    {
        return $this->isCustomer() ? route('app.dashboard') : route('admin.dashboard');
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', trim($this->name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }

    public function scopeInternal(Builder $query): Builder
    {
        return $query->whereIn('role', ['admin', 'staff']);
    }
}
