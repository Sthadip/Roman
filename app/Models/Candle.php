<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Candle extends Model
{
    protected $table    = 'candles';
    protected $fillable = ['coin','open','high','low','close','volume','interval_minutes','candle_time'];
    protected $casts    = ['candle_time'=>'datetime'];

    // Upsert current candle, update OHLC
    public static function tick(string $coin, float $price, float $volume = 0, int $intervalMin = 1): void
    {
        $flooredTime = now()->floorUnit('minute', $intervalMin);
        $existing    = self::where('coin',$coin)
            ->where('interval_minutes',$intervalMin)
            ->where('candle_time',$flooredTime)->first();

        if ($existing) {
            $existing->update([
                'high'   => max((float)$existing->high, $price),
                'low'    => min((float)$existing->low, $price),
                'close'  => $price,
                'volume' => (float)$existing->volume + $volume,
            ]);
        } else {
            self::create([
                'coin'             => $coin,
                'open'             => $price,
                'high'             => $price,
                'low'              => $price,
                'close'            => $price,
                'volume'           => $volume,
                'interval_minutes' => $intervalMin,
                'candle_time'      => $flooredTime,
            ]);
        }
    }

    // Get candles for chart in TradingView Lightweight Charts format
    public static function chartData(string $coin, int $intervalMin = 1, int $limit = 120): array
    {
        return self::where('coin',$coin)
            ->where('interval_minutes',$intervalMin)
            ->orderBy('candle_time')
            ->latest('candle_time')
            ->limit($limit)
            ->get()
            ->reverse()->values()
            ->map(fn($c) => [
                'time'  => $c->candle_time->timestamp,
                'open'  => (float)$c->open,
                'high'  => (float)$c->high,
                'low'   => (float)$c->low,
                'close' => (float)$c->close,
            ])->toArray();
    }
}
