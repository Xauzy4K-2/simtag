<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillSetting extends Model
{
    use HasFactory;

    const CREATED_AT = null;

    protected $fillable = [
        'setting_name',
        'setting_value',
        'description',
        'updated_by',
    ];

    protected $casts = [
        'setting_value' => 'decimal:2',
        'updated_at'    => 'datetime',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function getValue(string $settingName, $default = null)
    {
        return static::where('setting_name', $settingName)->value('setting_value') ?? $default;
    }
}
