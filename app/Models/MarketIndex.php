<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MarketIndex extends Model
{
    protected $table    = 'market_index';
    protected $fillable = [
        'coin','price','high_24h','low_24h','change_pct','trading_enabled','updated_by',
        'drift_pct','drift_interval','drift_direction','drift_enabled','drift_last_run',
        'live_mode','live_seeded_at','live_open_price',
    ];
    protected $casts = [
        'trading_enabled'=>'boolean','drift_enabled'=>'boolean','live_mode'=>'boolean',
        'price'=>'decimal:8','high_24h'=>'decimal:8','low_24h'=>'decimal:8',
        'change_pct'=>'decimal:4','drift_pct'=>'decimal:4','live_open_price'=>'decimal:8',
        'drift_last_run'=>'datetime','live_seeded_at'=>'datetime',
    ];

    public function updater() { return $this->belongsTo(User::class,'updated_by'); }

    public static function forCoin(string $coin): self
    {
        $defaults = [
            'BTC'=>['price'=>67500,'high_24h'=>69000,'low_24h'=>66000,'change_pct'=>1.25],
            'ETH'=>['price'=>3500, 'high_24h'=>3600, 'low_24h'=>3400, 'change_pct'=>0.85],
        ];
        $d = $defaults[$coin] ?? ['price'=>1,'high_24h'=>1,'low_24h'=>1,'change_pct'=>0];
        return self::firstOrCreate(['coin'=>$coin], array_merge($d,[
            'trading_enabled'=>true,'drift_enabled'=>false,
            'drift_pct'=>0,'drift_interval'=>60,'drift_direction'=>'none',
        ]));
    }

    public static function all_indexed(): array
    {
        return self::whereIn('coin',['BTC','ETH'])->get()->keyBy('coin')->toArray();
    }
}
