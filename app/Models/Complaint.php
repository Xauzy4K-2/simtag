<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Complaint extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'Air Tidak Mengalir',
        'Air Keruh / Berbau',
        'Tekanan Air Lemah',
        'Pipa Bocor',
        'Kebocoran Meter Pelanggan',
        'Permintaan Penggantian Meter',
        'Permintaan Pemasangan Baru',
        'Pipa Tersumbat',
        'Gangguan Distribusi',
        'Pelayanan Petugas',
        'Lainnya',
    ];

    public const STATUSES = [
        'open' => 'Diterima',
        'in_progress' => 'Diproses',
        'resolved' => 'Selesai',
        'closed' => 'Ditutup',
    ];

    protected $fillable = [
        'ticket_number',
        'customer_id',
        'category',
        'subject',
        'description',
        'location',
        'attachment_path',
        'status',
        'admin_response',
        'handled_by',
        'handled_at',
    ];

    protected $casts = [
        'handled_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path ? Storage::disk('public')->url($this->attachment_path) : null;
    }

    public static function generateTicket(): string
    {
        $prefix = 'ADU-'.now()->format('ymd');
        $count = static::where('ticket_number', 'like', $prefix.'%')->count() + 1;

        do {
            $ticket = $prefix.'-'.str_pad((string) $count++, 3, '0', STR_PAD_LEFT);
        } while (static::where('ticket_number', $ticket)->exists());

        return $ticket;
    }
}
