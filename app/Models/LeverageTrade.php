<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeverageTrade extends Model
{
    protected $fillable = [
        'user_id','coin','direction','margin','leverage','position_size',
        'entry_price','liq_price','close_price','pnl','pnl_pct',
        'status','opened_at','closed_at','note',
    ];

    protected $casts = [
        'margin'        => 'decimal:8',
        'position_size' => 'decimal:8',
        'entry_price'   => 'decimal:8',
        'liq_price'     => 'decimal:8',
        'close_price'   => 'decimal:8',
        'pnl'           => 'decimal:8',
        'pnl_pct'       => 'decimal:4',
        'leverage'      => 'integer',
        'opened_at'     => 'datetime',
        'closed_at'     => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function isOpen():        bool { return $this->status === 'open'; }
    public function isClosed():      bool { return $this->status === 'closed'; }
    public function isLiquidated():  bool { return $this->status === 'liquidated'; }

    /**
     * Calculate unrealised PnL given current market price.
     * Long:  PnL = (currentPrice - entryPrice) / entryPrice * positionSize
     * Short: PnL = (entryPrice - currentPrice) / entryPrice * positionSize
     */
    public function unrealisedPnl(float $currentPrice): float
    {
        $entry = (float)$this->entry_price;
        $size  = (float)$this->position_size;
        if ($entry <= 0) return 0;
        if ($this->direction === 'long') {
            return round(($currentPrice - $entry) / $entry * $size, 8);
        }
        return round(($entry - $currentPrice) / $entry * $size, 8);
    }

    /**
     * PnL as % of margin.
     */
    public function unrealisedPnlPct(float $currentPrice): float
    {
        $margin = (float)$this->margin;
        if ($margin <= 0) return 0;
        return round($this->unrealisedPnl($currentPrice) / $margin * 100, 4);
    }

    /**
     * Is this position liquidated at the given price?
     */
    public function isLiquidatedAt(float $price): bool
    {
        if ($this->direction === 'long')  return $price <= (float)$this->liq_price;
        return $price >= (float)$this->liq_price;
    }

    /**
     * Compute liquidation price.
     * Long:  liqPrice = entryPrice * (1 - 1/leverage + maintenanceMarginRate)
     * Short: liqPrice = entryPrice * (1 + 1/leverage - maintenanceMarginRate)
     * We use 0.5% maintenance margin for simplicity.
     */
    public static function calcLiqPrice(string $direction, float $entryPrice, int $leverage): float
    {
        $mmr = 0.005; // 0.5% maintenance margin
        if ($direction === 'long') {
            return round($entryPrice * (1 - 1 / $leverage + $mmr), 2);
        }
        return round($entryPrice * (1 + 1 / $leverage - $mmr), 2);
    }
}
