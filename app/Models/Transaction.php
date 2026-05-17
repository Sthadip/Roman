<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'type', 'coin', 'amount', 'direction',
        'description', 'ref_type', 'ref_id', 'balance_after',
    ];

    public function isCredit(): bool { return $this->direction === 'credit'; }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'deposit'    => '↓',
            'withdrawal' => '↑',
            'interest'   => '◎',
            'profit'     => '◈',
            'bonus'         => '★',
            'manual_credit' => '⊕',
            default         => '•',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'deposit'    => '#00e5a0',
            'withdrawal' => '#ff5252',
            'interest'   => '#00e5ff',
            'profit'     => '#ffd600',
            'bonus'         => '#7c4dff',
            'manual_credit' => '#00e5ff',
            default         => '#5a8aa0',
        };
    }

    public static function record(
        int $userId, string $type, string $coin, float $amount,
        string $direction, string $description, float $balanceAfter,
        ?string $refType = null, ?int $refId = null
    ): self {
        return self::create([
            'user_id'      => $userId,
            'type'         => $type,
            'coin'         => $coin,
            'amount'       => $amount,
            'direction'    => $direction,
            'description'  => $description,
            'balance_after'=> $balanceAfter,
            'ref_type'     => $refType,
            'ref_id'       => $refId,
        ]);
    }

    public function user() { return $this->belongsTo(User::class); }
}
