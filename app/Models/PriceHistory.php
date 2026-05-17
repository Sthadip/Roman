<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $table    = 'price_history';
    protected $fillable = ['coin','price','high','low','recorded_at'];
    protected $casts    = ['recorded_at'=>'datetime'];

    public static function record(string $coin, float $price, ?float $high=null, ?float $low=null): void
    {
        self::create(['coin'=>$coin,'price'=>$price,'high'=>$high,'low'=>$low,'recorded_at'=>now()]);
    }

    // Last N points for chart
    public static function chartData(string $coin, int $points=60): array
    {
        return self::where('coin',$coin)
            ->latest('recorded_at')
            ->limit($points)
            ->get(['price','high','low','recorded_at'])
            ->reverse()
            ->values()
            ->map(fn($p)=>[
                'time'  => $p->recorded_at->timestamp,
                'price' => (float)$p->price,
                'high'  => (float)($p->high ?? $p->price),
                'low'   => (float)($p->low  ?? $p->price),
            ])->toArray();
    }
}
