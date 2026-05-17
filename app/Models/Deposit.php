<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = [
        'user_id', 'network', 'amount', 'transaction_id', 'wallet_address',
        'screenshot_path', 'note', 'status', 'reviewed_by', 'reviewed_at',
    ];

    // Deposits always credit USDT regardless of network used
    public const CREDIT_COIN = 'USDT';

    public function getNetworkIconAttribute(): string
    {
        return match($this->network) {
            'BTC'  => '₿',
            'ETH'  => 'Ξ',
            default => $this->network,
        };
    }

    public function getNetworkColorAttribute(): string
    {
        return match($this->network) {
            'BTC'  => '#F7931A',
            'ETH'  => '#627EEA',
            default => '#5a8aa0',
        };
    }

    public function getNetworkBgAttribute(): string
    {
        return match($this->network) {
            'BTC'  => '#F7931A22',
            'ETH'  => '#627EEA22',
            default => '#5a8aa022',
        };
    }

    public function getNetworkNameAttribute(): string
    {
        return match($this->network) {
            'BTC'  => 'Bitcoin',
            'ETH'  => 'Ethereum',
            default => $this->network,
        };
    }

    protected $casts = ['reviewed_at' => 'datetime'];

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isConfirmed(): bool { return $this->status === 'confirmed'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'confirmed' => '#00e5a0',
            'rejected'  => '#ff5252',
            default     => '#ffd600',
        };
    }

    public function getStatusBgAttribute(): string
    {
        return match($this->status) {
            'confirmed' => '#00e5a022',
            'rejected'  => '#ff525222',
            default     => '#ffd60022',
        };
    }

    public function user()     { return $this->belongsTo(User::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}
