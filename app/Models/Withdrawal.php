<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [
        'user_id','coin','amount','usdt_amount','coin_amount','rate_used',
        'wallet_address','network','note','status','reviewed_by','reviewed_at',
    ];
    protected $casts = ['reviewed_at'=>'datetime','rate_used'=>'decimal:8'];

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }

    // True if destination is crypto (not USDT)
    public function isCryptoWithdrawal(): bool { return $this->coin !== 'USDT'; }

    public function user()     { return $this->belongsTo(User::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }

    // Live rates: USDT per 1 unit of coin
    public static function liveRates(): array
    {
        // In production: replace with a real API call (CoinGecko, Binance, etc.)
        return ['BTC' => 67500.00, 'ETH' => 3500.00, 'USDT' => 1.00];
    }

    public static function usdtToCoin(float $usdt, string $coin): array
    {
        $rates    = self::liveRates();
        $rate     = $rates[$coin] ?? 1;
        $coinAmt  = $coin === 'USDT' ? $usdt : round($usdt / $rate, 8);
        return ['coin_amount' => $coinAmt, 'rate' => $rate];
    }
}
