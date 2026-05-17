<?php
namespace Database\Seeders;
use App\Models\Candle;
use App\Models\MarketIndex;
use App\Models\PriceHistory;
use Illuminate\Database\Seeder;

class MarketSeeder extends Seeder
{
    public function run(): void
    {
        $markets = [
            'BTC' => ['price'=>67500,'high_24h'=>69200,'low_24h'=>66100,'change_pct'=>1.42],
            'ETH' => ['price'=>3520, 'high_24h'=>3620, 'low_24h'=>3380, 'change_pct'=>0.68],
        ];

        foreach ($markets as $coin => $data) {
            $market = MarketIndex::updateOrCreate(['coin'=>$coin], array_merge($data,[
                'trading_enabled'=>true,'drift_enabled'=>false,
                'drift_pct'=>0,'drift_interval'=>60,'drift_direction'=>'none',
            ]));
            $this->command->info("Market: {$coin} @ {$data['price']}");

            $price = (float)$data['price'];
            $now   = now();

            // Seed 120 price history points (1-min intervals, 2 hours back)
            PriceHistory::where('coin',$coin)->delete();
            Candle::where('coin',$coin)->delete();

            for ($i = 119; $i >= 0; $i--) {
                $ts       = $now->copy()->subMinutes($i);
                $variance = $price * (mt_rand(-15,15) / 1000); // ±1.5%
                $p        = round($price + $variance, 2);
                $high     = round($p * (1 + mt_rand(1,8)/1000), 2);
                $low      = round($p * (1 - mt_rand(1,8)/1000), 2);

                PriceHistory::create(['coin'=>$coin,'price'=>$p,'high'=>$high,'low'=>$low,'recorded_at'=>$ts]);

                // Candles for 1m, 5m, 15m intervals
                foreach ([1,5,15] as $ivMin) {
                    if ($i % $ivMin !== 0) continue;
                    $o = round($p * (1 + mt_rand(-5,5)/1000), 2);
                    $c = round($p * (1 + mt_rand(-5,5)/1000), 2);
                    $h = max($o,$c,$high);
                    $l = min($o,$c,$low);
                    $floor = $ts->copy()->floorUnit('minute',$ivMin);
                    Candle::updateOrCreate(
                        ['coin'=>$coin,'interval_minutes'=>$ivMin,'candle_time'=>$floor],
                        ['open'=>$o,'high'=>$h,'low'=>$l,'close'=>$c,'volume'=>round(mt_rand(10,500)/100,4)]
                    );
                }
                $price = $p; // walk price
            }
            $this->command->info("  → Seeded 120 price points + candles for {$coin}");
        }
    }
}
