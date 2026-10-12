<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminActivityLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'admin_id',
        'activity',
        'target_table',
        'target_id',
        'details',
        'ip_address',
    ];

    protected $casts = [
        'target_id' => 'integer',
        'created_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public static function record(string $activity, ?Model $target = null, ?string $details = null): void
    {
        static::create([
            'admin_id' => auth()->id(),
            'activity' => $activity,
            'target_table' => $target?->getTable(),
            'target_id' => $target?->getKey(),
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }
}
