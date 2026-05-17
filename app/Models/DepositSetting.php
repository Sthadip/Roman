<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositSetting extends Model
{
    protected $fillable = [
        'coin', 'account_name', 'account_number', 'bank_name', 'network',
        'wallet_address', 'instructions', 'qr_image_path', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public static function active(): ?self
    {
        return self::where('is_active', true)->first();
    }

    public static function forCoin(string $coin): ?self
    {
        return self::where('coin', $coin)->where('is_active', true)->first();
    }
}
