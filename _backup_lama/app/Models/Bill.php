<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'billing_period',
        'water_usage_m3',
        'water_rate',
        'administration_fee',
        'penalty_fee',
        'total_amount',
        'due_date',
        'status',
    ];

    protected $casts = [
        'water_usage_m3'      => 'decimal:2',
        'water_rate'          => 'decimal:2',
        'administration_fee'  => 'decimal:2',
        'penalty_fee'         => 'decimal:2',
        'total_amount'        => 'decimal:2',
        'due_date'            => 'date',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'bill_id');
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('status', 'unpaid');
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'overdue')
            ->orWhere(function (Builder $q) {
                $q->where('status', 'unpaid')
                    ->where('due_date', '<', now());
            });
    }

    public function scopeForPeriod(Builder $query, string $period): Builder
    {
        return $query->where('billing_period', $period);
    }
}
