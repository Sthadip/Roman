<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'coin', 'coin_name', 'available', 'in_order'];
    protected $casts    = ['available' => 'decimal:8', 'in_order' => 'decimal:8'];

    public function getTotalAttribute(): string
    {
        return number_format((float)$this->available + (float)$this->in_order, 8, '.', '');
    }

    public static function supportedCoins(): array
    {
        return [
            'BTC'  => ['name' => 'Bitcoin',              'icon' => '₿', 'color' => '#F7931A', 'bg' => '#F7931A22'],
            'ETH'  => ['name' => 'Ethereum',             'icon' => 'Ξ', 'color' => '#627EEA', 'bg' => '#627EEA22'],
            'USDT' => ['name' => 'USD',           'icon' => '$', 'color' => '#26A17B', 'bg' => '#26A17B22'],
        ];
    }

    public static function depositCoin(): string { return 'USDT'; }

    public static function ensureForUser(int $userId): void
    {
        foreach (self::supportedCoins() as $coin => $meta) {
            self::firstOrCreate(
                ['user_id' => $userId, 'coin' => $coin],
                ['coin_name' => $meta['name'], 'available' => 0, 'in_order' => 0]
            );
        }
    }

    public function user() { return $this->belongsTo(User::class); }
}
