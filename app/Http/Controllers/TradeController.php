<?php
namespace App\Http\Controllers;

use App\Models\Candle;
use App\Models\MarketIndex;
use App\Models\OrderBook;
use App\Models\PriceHistory;
use App\Models\LeverageTrade;
use App\Models\Trade;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TradeController extends Controller
{
    // ── User: Trading Page ──────────────────────────────────────
    public function index(Request $request)
    {
        $user    = Auth::user();
        $uid     = $user->id;
        Wallet::ensureForUser($uid);
        $coin    = strtoupper($request->get('coin', 'BTC'));
        if (!in_array($coin, ['BTC','ETH'])) $coin = 'BTC';
        $market  = MarketIndex::forCoin($coin);
        $btcMkt  = MarketIndex::forCoin('BTC');
        $ethMkt  = MarketIndex::forCoin('ETH');
        $wallets = Wallet::where('user_id', $uid)->get()->keyBy('coin');
        $coinMeta = Wallet::supportedCoins();
        return view('user.trade', compact('coin','market','btcMkt','ethMkt','wallets','coinMeta'));
    }

    // ── User: Place Buy ─────────────────────────────────────────
    public function buy(Request $request)
    {
        $request->validate(['coin'=>'required|in:BTC,ETH','usdt_amount'=>'required|numeric|min:1']);
        $uid  = Auth::id();
        $coin = $request->coin;
        $usdt = (float)$request->usdt_amount;
        $market = MarketIndex::forCoin($coin);
        if (!$market->trading_enabled)
            return response()->json(['error'=>"Trading for {$coin} is currently disabled."], 422);
        $usdtWallet = Wallet::where('user_id',$uid)->where('coin','USDT')->first();
        if (!$usdtWallet || (float)$usdtWallet->available < $usdt)
            return response()->json(['error'=>"Insufficient USDT. Available: ₮".number_format((float)($usdtWallet->available??0),2)], 422);
        $price   = (float)$market->price;
        $coinAmt = round($usdt / $price, 8);
        $usdtWallet->decrement('available', $usdt);
        $usdtWallet->increment('in_order', $usdt);
        $trade = Trade::create(['user_id'=>$uid,'coin'=>$coin,'side'=>'buy','coin_amount'=>$coinAmt,'usdt_amount'=>$usdt,'price'=>$price,'status'=>'pending']);
        self::processOrderBook($coin, $price);
        return response()->json(['success'=>"Buy order placed: {$coinAmt} {$coin} @ ₮".number_format($price,2),'trade_id'=>$trade->id]);
    }

    // ── User: Place Sell ────────────────────────────────────────
    public function sell(Request $request)
    {
        $request->validate(['coin'=>'required|in:BTC,ETH','coin_amount'=>'required|numeric|min:0.00000001','sell_price'=>'required|numeric|min:0.01']);
        $uid     = Auth::id();
        $coin    = $request->coin;
        $coinAmt = (float)$request->coin_amount;
        $price   = (float)$request->sell_price;
        $market  = MarketIndex::forCoin($coin);
        if (!$market->trading_enabled)
            return response()->json(['error'=>"Trading for {$coin} is currently disabled."], 422);
        if ($price > (float)$market->high_24h || $price < (float)$market->low_24h)
            return response()->json(['error'=>"Sell price must be ₮".number_format((float)$market->low_24h,2)." – ₮".number_format((float)$market->high_24h,2)], 422);
        $coinWallet = Wallet::where('user_id',$uid)->where('coin',$coin)->first();
        if (!$coinWallet || (float)$coinWallet->available < $coinAmt)
            return response()->json(['error'=>"Insufficient {$coin}. Available: ".number_format((float)($coinWallet->available??0),8)], 422);
        $usdt = round($coinAmt * $price, 8);
        $coinWallet->decrement('available', $coinAmt);
        $coinWallet->increment('in_order', $coinAmt);
        $trade = Trade::create(['user_id'=>$uid,'coin'=>$coin,'side'=>'sell','coin_amount'=>$coinAmt,'usdt_amount'=>$usdt,'price'=>$price,'status'=>'pending']);
        self::processOrderBook($coin, $price);
        return response()->json(['success'=>"Sell order placed: {$coinAmt} {$coin} @ ₮".number_format($price,2),'trade_id'=>$trade->id]);
    }

    // ── Admin: Trading Panel ────────────────────────────────────
    public function adminIndex(Request $request)
    {
        $coin    = strtoupper($request->get('coin', 'BTC'));
        if (!in_array($coin, ['BTC','ETH'])) $coin = 'BTC';
        $btcMkt  = MarketIndex::forCoin('BTC');
        $ethMkt  = MarketIndex::forCoin('ETH');
        $market  = $coin === 'BTC' ? $btcMkt : $ethMkt;
        $coinMeta = Wallet::supportedCoins();
        $users   = \App\Models\User::where('role','user')->orderBy('name')->get(['id','name','email']);
        return view('admin.trade', compact('coin','market','btcMkt','ethMkt','coinMeta','users'));
    }

    // ── Admin: Reset Market (sync to live Binance data) ─────────
    public function resetMarket(Request $request)
    {
        $request->validate(['coin' => 'required|in:BTC,ETH']);
        $coin   = $request->coin;
        $market = MarketIndex::forCoin($coin);
        $symbol = $coin === 'BTC' ? 'BTCUSDT' : 'ETHUSDT';

        $liveData = self::fetchBinanceTicker($symbol);
        if (!$liveData) {
            return response()->json(['error' => 'Could not fetch live price from Binance. Check server connectivity.'], 422);
        }

        $price  = $liveData['price'];
        $high24 = $liveData['high'];
        $low24  = $liveData['low'];
        $chgPct = $liveData['change_pct'];

        $updateData = [
            'price'           => round($price, 2),
            'high_24h'        => round($high24, 2),
            'low_24h'         => round($low24, 2),
            'change_pct'      => round($chgPct, 4),
            'trading_enabled' => true,
            'drift_enabled'   => false,
            'drift_direction' => 'none',
            'drift_last_run'  => null,
            'updated_by'      => Auth::id(),
        ];

        // Only set live_mode fields if columns exist
        if (\Schema::hasColumn('market_index', 'live_mode')) {
            $updateData['live_mode']       = true;
            $updateData['live_seeded_at']  = now();
            $updateData['live_open_price'] = round($price, 2);
        }

        $market->update($updateData);

        self::seedCandlesFromBinance($coin, $symbol, $price);
        PriceHistory::record($coin, $price, $high24, $low24);
        self::processOrderBook($coin, $price);

        return response()->json([
            'success' => "{$coin} market linked to live Binance data — ₮".number_format($price, 2),
            'market'  => $market->fresh(),
        ]);
    }

    // ── Admin: Update Drift Settings ────────────────────────────
    public function updateMarket(Request $request)
    {
        $request->validate([
            'coin'            => 'required|in:BTC,ETH',
            'trading_enabled' => 'nullable|boolean',
            'drift_enabled'   => 'nullable|boolean',
            'drift_pct'       => 'nullable|numeric|min:0|max:50',
            'drift_interval'  => 'nullable|integer|min:5|max:3600',
            'drift_direction' => 'nullable|in:up,down,none',
        ]);
        $coin    = $request->coin;
        $market  = MarketIndex::forCoin($coin);
        $driftOn = $request->boolean('drift_enabled', false);

        $updateData = [
            'trading_enabled' => $request->boolean('trading_enabled', true),
            'drift_enabled'   => $driftOn,
            'drift_pct'       => $request->drift_pct ?? (float)$market->drift_pct,
            'drift_interval'  => $request->drift_interval ?? (int)$market->drift_interval,
            'drift_direction' => $request->drift_direction ?? $market->drift_direction,
            'updated_by'      => Auth::id(),
        ];

        $hasLiveMode = \Schema::hasColumn('market_index', 'live_mode');

        if (!$driftOn) {
            // Stopping drift — restore live mode if available
            if ($hasLiveMode) {
                $updateData['live_mode'] = !is_null($market->live_seeded_at ?? null);
            }
            // Re-sync price with Binance on stop if was live
            $wasLive = $hasLiveMode && ($market->live_seeded_at ?? null);
            if ($wasLive) {
                $symbol   = $coin === 'BTC' ? 'BTCUSDT' : 'ETHUSDT';
                $liveData = self::fetchBinanceTicker($symbol);
                if ($liveData) {
                    $updateData['price']      = round($liveData['price'], 2);
                    $updateData['high_24h']   = round($liveData['high'], 2);
                    $updateData['low_24h']    = round($liveData['low'], 2);
                    $updateData['change_pct'] = round($liveData['change_pct'], 4);
                }
            }
        } else {
            // Drift on — suspend live mode
            if ($hasLiveMode) {
                $updateData['live_mode'] = false;
            }
        }

        $market->update($updateData);
        $market->refresh();

        Candle::tick($coin, (float)$market->price, 0, 1);
        Candle::tick($coin, (float)$market->price, 0, 5);
        Candle::tick($coin, (float)$market->price, 0, 15);
        self::processOrderBook($coin, (float)$market->price);

        return response()->json([
            'success' => 'Drift ' . ($driftOn ? 'started' : 'stopped'),
            'market'  => $market,
        ]);
    }

    // ── Admin: Create Bulk/Limit Order ──────────────────────────
    public function createOrderBook(Request $request)
    {
        $request->validate([
            'coin'          => 'required|in:BTC,ETH',
            'side'          => 'required|in:buy,sell',
            'trigger_price' => 'required|numeric|min:0.01',
            'coin_amount'   => 'required|numeric|min:0.00000001',
            'user_ids'      => 'required|array|min:1',
            'user_ids.*'    => 'exists:users,id',
        ]);
        $coin    = $request->coin;
        $market  = MarketIndex::forCoin($coin);
        $created = 0;
        foreach ($request->user_ids as $uid) {
            $tp   = (float)$request->trigger_price;
            $ca   = (float)$request->coin_amount;
            $usdt = round($ca * $tp, 8);
            OrderBook::create(['user_id'=>$uid,'coin'=>$coin,'side'=>$request->side,'trigger_price'=>$tp,'coin_amount'=>$ca,'usdt_amount'=>$usdt,'status'=>'open']);
            $created++;
        }
        self::processOrderBook($coin, (float)$market->price);
        return response()->json(['success'=>"Created {$created} limit order(s) for {$request->coin_amount} {$coin} @ ₮{$request->trigger_price}"]);
    }

    // ── Admin: Cancel Order Book ────────────────────────────────
    public function cancelOrderBook(OrderBook $order)
    {
        $order->update(['status'=>'cancelled']);
        return response()->json(['success'=>'Limit order cancelled.']);
    }

    // ── Admin: Fill Buy ─────────────────────────────────────────
    public function fillBuy(Trade $trade)
    {
        if (!$trade->isPending() || $trade->side !== 'buy')
            return response()->json(['error'=>'Not a pending buy.'], 422);
        DB::transaction(function() use ($trade) {
            $uw = Wallet::where('user_id',$trade->user_id)->where('coin','USDT')->first();
            if ($uw) $uw->decrement('in_order', $trade->usdt_amount);
            $cw = Wallet::where('user_id',$trade->user_id)->where('coin',$trade->coin)->first();
            if ($cw) { $cw->increment('available', $trade->coin_amount); $cw->refresh(); }
            $trade->update(['status'=>'filled','filled_by'=>Auth::id(),'filled_at'=>now()]);
            Transaction::record($trade->user_id,'trade_buy',$trade->coin,$trade->coin_amount,'credit',
                "Bought {$trade->coin_amount} {$trade->coin} @ ₮{$trade->price}",
                $cw ? (float)$cw->available : 0, 'trade', $trade->id);
        });
        return response()->json(['success'=>"Buy filled — {$trade->coin_amount} {$trade->coin} credited."]);
    }

    // ── Admin: Fill Sell ────────────────────────────────────────
    public function fillSell(Trade $trade)
    {
        if (!$trade->isPending() || $trade->side !== 'sell')
            return response()->json(['error'=>'Not a pending sell.'], 422);
        DB::transaction(function() use ($trade) {
            $cw = Wallet::where('user_id',$trade->user_id)->where('coin',$trade->coin)->first();
            if ($cw) $cw->decrement('in_order', $trade->coin_amount);
            $uw = Wallet::where('user_id',$trade->user_id)->where('coin','USDT')->first();
            if ($uw) { $uw->increment('available', $trade->usdt_amount); $uw->refresh(); }
            $trade->update(['status'=>'filled','filled_by'=>Auth::id(),'filled_at'=>now()]);
            Transaction::record($trade->user_id,'trade_sell','USDT',$trade->usdt_amount,'credit',
                "Sold {$trade->coin_amount} {$trade->coin} @ ₮{$trade->price}",
                $uw ? (float)$uw->available : 0, 'trade', $trade->id);
        });
        return response()->json(['success'=>"Sell filled — ₮{$trade->usdt_amount} USDT credited."]);
    }

    // ── Admin: Cancel Trade ─────────────────────────────────────
    public function cancelTrade(Trade $trade)
    {
        if (!$trade->isPending()) return response()->json(['error'=>'Not pending.'], 422);
        DB::transaction(function() use ($trade) {
            if ($trade->side === 'buy') {
                $w = Wallet::where('user_id',$trade->user_id)->where('coin','USDT')->first();
                if ($w) { $w->decrement('in_order',$trade->usdt_amount); $w->increment('available',$trade->usdt_amount); }
            } else {
                $w = Wallet::where('user_id',$trade->user_id)->where('coin',$trade->coin)->first();
                if ($w) { $w->decrement('in_order',$trade->coin_amount); $w->increment('available',$trade->coin_amount); }
            }
            $trade->update(['status'=>'cancelled','filled_by'=>Auth::id(),'filled_at'=>now()]);
        });
        return response()->json(['success'=>'Order cancelled, funds returned.']);
    }

    // ── Auto-match Order Book ────────────────────────────────────
    public static function processOrderBook(string $coin, float $price): void
    {
        $open = OrderBook::where('coin',$coin)->where('status','open')->get();
        foreach ($open as $order) {
            $hit = ($order->side === 'buy'  && $price <= (float)$order->trigger_price)
                || ($order->side === 'sell' && $price >= (float)$order->trigger_price);
            if (!$hit) continue;
            DB::transaction(function() use ($order, $price) {
                if ($order->side === 'buy') {
                    $uw = Wallet::where('user_id',$order->user_id)->where('coin','USDT')->first();
                    if (!$uw || (float)$uw->available < (float)$order->usdt_amount) { $order->update(['status'=>'cancelled']); return; }
                    $uw->decrement('available', $order->usdt_amount);
                    $cw = Wallet::where('user_id',$order->user_id)->where('coin',$order->coin)->first();
                    if ($cw) { $cw->increment('available', $order->coin_amount); $cw->refresh(); }
                    $t = Trade::create(['user_id'=>$order->user_id,'coin'=>$order->coin,'side'=>'buy','coin_amount'=>$order->coin_amount,'usdt_amount'=>$order->usdt_amount,'price'=>$price,'status'=>'filled','filled_at'=>now()]);
                    $order->update(['status'=>'filled','filled_at'=>now(),'trade_id'=>$t->id]);
                    Transaction::record($order->user_id,'trade_buy',$order->coin,$order->coin_amount,'credit',"Auto-buy {$order->coin_amount} {$order->coin} @ ₮{$price}",$cw?(float)$cw->available:0,'order_book',$order->id);
                } else {
                    $cw = Wallet::where('user_id',$order->user_id)->where('coin',$order->coin)->first();
                    if (!$cw || (float)$cw->available < (float)$order->coin_amount) { $order->update(['status'=>'cancelled']); return; }
                    $cw->decrement('available', $order->coin_amount);
                    $uw = Wallet::where('user_id',$order->user_id)->where('coin','USDT')->first();
                    if ($uw) { $uw->increment('available', $order->usdt_amount); $uw->refresh(); }
                    $t = Trade::create(['user_id'=>$order->user_id,'coin'=>$order->coin,'side'=>'sell','coin_amount'=>$order->coin_amount,'usdt_amount'=>$order->usdt_amount,'price'=>$price,'status'=>'filled','filled_at'=>now()]);
                    $order->update(['status'=>'filled','filled_at'=>now(),'trade_id'=>$t->id]);
                    Transaction::record($order->user_id,'trade_sell','USDT',$order->usdt_amount,'credit',"Auto-sell {$order->coin_amount} {$order->coin} @ ₮{$price}",$uw?(float)$uw->available:0,'order_book',$order->id);
                }
            });
        }
    }

    // ── API: Full live state ─────────────────────────────────────
    public function apiState(Request $request, string $coin)
    {
        $coin   = strtoupper($coin);
        if (!in_array($coin, ['BTC','ETH'])) abort(404);
        $market = MarketIndex::forCoin($coin);
        $symbol = $coin === 'BTC' ? 'BTCUSDT' : 'ETHUSDT';
        $hasLiveMode = \Schema::hasColumn('market_index', 'live_mode');

        // Apply drift tick
        if ($market->drift_enabled && $market->drift_direction !== 'none' && (float)$market->drift_pct > 0) {
            $elapsed = now()->timestamp - ($market->drift_last_run?->timestamp ?? 0);
            if ($elapsed >= (int)$market->drift_interval) {
                $delta    = (float)$market->price * (float)$market->drift_pct / 100;
                $newPrice = $market->drift_direction === 'up'
                    ? (float)$market->price + $delta
                    : max(0.01, (float)$market->price - $delta);
                $newHigh = max((float)$market->high_24h, $newPrice);
                $newLow  = min((float)$market->low_24h, $newPrice);
                $market->update(['price'=>round($newPrice,2),'high_24h'=>round($newHigh,2),'low_24h'=>round($newLow,2),'drift_last_run'=>now(),'updated_by'=>null]);
                PriceHistory::record($coin, $newPrice, $newHigh, $newLow);
                Candle::tick($coin, $newPrice, 0, 1);
                Candle::tick($coin, $newPrice, 0, 5);
                Candle::tick($coin, $newPrice, 0, 15);
                self::processOrderBook($coin, $newPrice);
                $market->refresh();
            }
        } elseif ($hasLiveMode && ($market->live_mode ?? false) && !$market->drift_enabled) {
            // Live mode: sync with Binance every 15s
            $elapsed = now()->timestamp - ($market->drift_last_run?->timestamp ?? 0);
            if ($elapsed >= 15) {
                $liveData = self::fetchBinanceTicker($symbol);
                if ($liveData) {
                    $livePrice = round($liveData['price'], 2);
                    $market->update([
                        'price'          => $livePrice,
                        'high_24h'       => round($liveData['high'], 2),
                        'low_24h'        => round($liveData['low'], 2),
                        'change_pct'     => round($liveData['change_pct'], 4),
                        'drift_last_run' => now(),
                    ]);
                    Candle::tick($coin, $livePrice, (float)($liveData['volume'] ?? 0), 1);
                    Candle::tick($coin, $livePrice, (float)($liveData['volume'] ?? 0), 5);
                    Candle::tick($coin, $livePrice, (float)($liveData['volume'] ?? 0), 15);
                    PriceHistory::record($coin, $livePrice, $liveData['high'], $liveData['low']);
                    self::processOrderBook($coin, $livePrice);
                    $market->refresh();
                }
            }
        }

        $interval = max(1, (int)$request->get('interval', 1));
        $uid      = Auth::id();
        $user     = Auth::user();

        // Candles
        $candles = Candle::chartData($coin, $interval, 200);
        if (empty($candles)) {
            $candles = self::candlesFromHistory($coin, $interval);
        }

        // Live order book
        $liveBook = self::buildLiveOrderBook($coin, (float)$market->price, (bool)$market->drift_enabled);

        // Trades
        $allBuys  = Trade::with('user')->where('coin',$coin)->where('side','buy')->where('status','pending')
            ->latest()->limit(30)->get()
            ->map(fn($t) => ['id'=>$t->id,'user'=>$t->user->name,'coin_amount'=>(float)$t->coin_amount,'usdt_amount'=>(float)$t->usdt_amount,'price'=>(float)$t->price,'is_mine'=>$t->user_id===$uid]);
        $allSells = Trade::with('user')->where('coin',$coin)->where('side','sell')->where('status','pending')
            ->latest()->limit(30)->get()
            ->map(fn($t) => ['id'=>$t->id,'user'=>$t->user->name,'coin_amount'=>(float)$t->coin_amount,'usdt_amount'=>(float)$t->usdt_amount,'price'=>(float)$t->price,'is_mine'=>$t->user_id===$uid]);

        $myOrders = Trade::where('user_id',$uid)->where('coin',$coin)->where('status','pending')
            ->latest()->get()
            ->map(fn($t) => ['id'=>$t->id,'side'=>$t->side,'coin_amount'=>(float)$t->coin_amount,'usdt_amount'=>(float)$t->usdt_amount,'price'=>(float)$t->price,'at'=>$t->created_at->diffForHumans()]);

        $histQuery = $user->isAdmin()
            ? Trade::with('user')->where('coin',$coin)->whereIn('status',['filled','cancelled'])->latest()->limit(30)
            : Trade::where('user_id',$uid)->where('coin',$coin)->whereIn('status',['filled','cancelled'])->latest()->limit(20);
        $myHistory = $histQuery->get()
            ->map(fn($t) => ['id'=>$t->id,'side'=>$t->side,'user'=>$t->relationLoaded('user')?$t->user->name:'','coin_amount'=>(float)$t->coin_amount,'usdt_amount'=>(float)$t->usdt_amount,'price'=>(float)$t->price,'status'=>$t->status,'at'=>($t->filled_at??$t->created_at)->diffForHumans()]);

        // Also pull closed leverage positions for history tab
        $closedLevQuery = $user->isAdmin()
            ? LeverageTrade::with('user')->where('coin',$coin)->whereIn('status',['closed','liquidated'])->latest('closed_at')->limit(30)
            : LeverageTrade::where('user_id',$uid)->where('coin',$coin)->whereIn('status',['closed','liquidated'])->latest('closed_at')->limit(20);
        $closedLevHistory = $closedLevQuery->get()->map(fn($p) => [
            'id'        => $p->id,
            'direction' => $p->direction,
            'leverage'  => (int)$p->leverage,
            'margin'    => (float)$p->margin,
            'pnl'       => (float)$p->pnl,
            'pnl_pct'   => (float)$p->pnl_pct,
            'status'    => $p->status,
            'at'        => ($p->closed_at ?? $p->created_at)->diffForHumans(),
        ]);

        $wallets = Wallet::where('user_id',$uid)->get()->keyBy('coin')
            ->map(fn($w) => ['available'=>(float)$w->available,'in_order'=>(float)$w->in_order]);

        $limitOrders = $user->isAdmin()
            ? OrderBook::with('user')->where('coin',$coin)->where('status','open')->orderBy('trigger_price','desc')->get()
                ->map(fn($o) => ['id'=>$o->id,'user'=>$o->user->name,'side'=>$o->side,'trigger_price'=>(float)$o->trigger_price,'coin_amount'=>(float)$o->coin_amount,'usdt_amount'=>(float)$o->usdt_amount])
            : collect();

        $recentFills = Trade::where('coin',$coin)->where('status','filled')
            ->latest('filled_at')->limit(30)->get()
            ->map(fn($t) => ['side'=>$t->side,'price'=>(float)$t->price,'coin_amount'=>(float)$t->coin_amount,'at'=>($t->filled_at??$t->created_at)->timestamp]);

        $liveMode = $hasLiveMode ? (bool)($market->live_mode ?? false) : false;

        // Leverage positions
        $leveragePositions = self::getLeveragePositions($coin, $uid, (float)$market->price, $user->isAdmin());

        return response()->json([
            'market'       => ['price'=>(float)$market->price,'high_24h'=>(float)$market->high_24h,'low_24h'=>(float)$market->low_24h,'change_pct'=>(float)$market->change_pct,'trading_enabled'=>(bool)$market->trading_enabled,'drift_enabled'=>(bool)$market->drift_enabled,'drift_direction'=>$market->drift_direction,'drift_pct'=>(float)$market->drift_pct,'drift_interval'=>(int)$market->drift_interval,'live_mode'=>$liveMode],
            'candles'      => $candles,
            'wallets'      => $wallets,
            'my_orders'    => $myOrders,
            'my_history'   => $myHistory,
            'all_buys'     => $allBuys,
            'all_sells'    => $allSells,
            'limit_orders' => $limitOrders,
            'order_book'   => $liveBook,
            'recent_fills' => $recentFills,
            'leverage_positions' => $leveragePositions,
            'leverage_history'   => $closedLevHistory,
        ]);
    }

    // ── Build realistic live order book ──────────────────────────
    private static function buildLiveOrderBook(string $coin, float $price, bool $driftActive): array
    {
        $tick   = $coin === 'BTC' ? 0.1 : 0.01;
        $levels = 15;
        $spread = $coin === 'BTC' ? 0.5 : 0.05;

        $bucket = $driftActive ? (int)(time() / 5) : (int)(time() / 15);
        mt_srand((int)($price * 10) + $bucket);

        $midPrice = round(round($price / $tick) * $tick, 2);

        $asks = [];
        $bids = [];

        for ($i = 0; $i < $levels; $i++) {
            $jitter   = mt_rand(0, 8) / 10;
            $askPrice = round($midPrice + $spread + ($i * $tick * (1 + $jitter)), 2);
            $baseAmt  = $coin === 'BTC'
                ? round(0.005 + mt_rand(1, 800) / 1000, 4)
                : round(0.05  + mt_rand(1, 5000) / 100, 2);
            $asks[] = ['price'=>$askPrice,'amount'=>$baseAmt,'total'=>round($askPrice * $baseAmt, 2),'sum'=>0];
        }

        for ($i = 0; $i < $levels; $i++) {
            $jitter   = mt_rand(0, 8) / 10;
            $bidPrice = round($midPrice - $spread - ($i * $tick * (1 + $jitter)), 2);
            $baseAmt  = $coin === 'BTC'
                ? round(0.005 + mt_rand(1, 800) / 1000, 4)
                : round(0.05  + mt_rand(1, 5000) / 100, 2);
            $bids[] = ['price'=>$bidPrice,'amount'=>$baseAmt,'total'=>round($bidPrice * $baseAmt, 2),'sum'=>0];
        }

        $askSum = 0;
        $bidSum = 0;
        foreach ($asks as &$a) { $askSum = round($askSum + $a['amount'], 4); $a['sum'] = $askSum; }
        foreach ($bids as &$b) { $bidSum = round($bidSum + $b['amount'], 4); $b['sum'] = $bidSum; }
        unset($a, $b);

        return ['asks'=>$asks,'bids'=>$bids,'spread'=>round($spread * 2, 2),'max_sum'=>max($askSum, $bidSum)];
    }

    // ── Fetch ticker from Binance ────────────────────────────────
    private static function fetchBinanceTicker(string $symbol): ?array
    {
        try {
            $resp = Http::timeout(6)->get('https://api.binance.com/api/v3/ticker/24hr', ['symbol' => $symbol]);
            if (!$resp->successful()) return null;
            $d = $resp->json();
            return [
                'price'      => (float)($d['lastPrice'] ?? 0),
                'high'       => (float)($d['highPrice']  ?? 0),
                'low'        => (float)($d['lowPrice']   ?? 0),
                'change_pct' => (float)($d['priceChangePercent'] ?? 0),
                'volume'     => (float)($d['volume']     ?? 0),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    // ── Seed candle history from Binance klines ──────────────────
    private static function seedCandlesFromBinance(string $coin, string $symbol, float $fallback): void
    {
        Candle::where('coin', $coin)->delete();
        PriceHistory::where('coin', $coin)->delete();

        $intervals = [1 => '1m', 5 => '5m', 15 => '15m'];

        foreach ($intervals as $min => $biv) {
            try {
                $resp = Http::timeout(10)->get('https://api.binance.com/api/v3/klines', [
                    'symbol'   => $symbol,
                    'interval' => $biv,
                    'limit'    => 200,
                ]);
                if (!$resp->successful()) throw new \Exception('non-200');
                foreach ($resp->json() as $k) {
                    $ts = \Carbon\Carbon::createFromTimestampMs((int)$k[0]);
                    Candle::updateOrCreate(
                        ['coin' => $coin, 'interval_minutes' => $min, 'candle_time' => $ts],
                        ['open' => (float)$k[1], 'high' => (float)$k[2], 'low' => (float)$k[3], 'close' => (float)$k[4], 'volume' => (float)$k[5]]
                    );
                    if ($min === 1) {
                        PriceHistory::create(['coin' => $coin, 'price' => (float)$k[4], 'high' => (float)$k[2], 'low' => (float)$k[3], 'recorded_at' => $ts]);
                    }
                }
            } catch (\Exception $e) {
                self::seedSyntheticCandles($coin, $min, $fallback);
            }
        }
    }

    // ── Fallback synthetic candles ───────────────────────────────
    private static function seedSyntheticCandles(string $coin, int $intervalMin, float $base): void
    {
        $now   = now()->floorUnit('minute', $intervalMin);
        $price = $base;
        for ($i = 200; $i >= 0; $i--) {
            $t   = $now->copy()->subMinutes($i * $intervalMin);
            $o   = round($price * (1 + (mt_rand(-8, 8) / 1000)), 2);
            $c   = round($price * (1 + (mt_rand(-5, 5) / 1000)), 2);
            $h   = round(max($o, $c) * (1 + mt_rand(1, 4) / 1000), 2);
            $l   = round(min($o, $c) * (1 - mt_rand(1, 4) / 1000), 2);
            $vol = round(mt_rand(5, 300) / 100, 4);
            Candle::updateOrCreate(
                ['coin' => $coin, 'interval_minutes' => $intervalMin, 'candle_time' => $t],
                ['open' => $o, 'high' => $h, 'low' => $l, 'close' => $c, 'volume' => $vol]
            );
            $price = $c;
        }
    }

    // ── Build candles from PriceHistory fallback ─────────────────
    private static function candlesFromHistory(string $coin, int $intervalMin): array
    {
        $rows = PriceHistory::where('coin', $coin)->orderBy('recorded_at')->get();
        if ($rows->isEmpty()) {
            $market  = MarketIndex::forCoin($coin);
            $price   = (float)$market->price;
            $now     = now()->timestamp;
            $out     = [];
            for ($i = 119; $i >= 0; $i--) {
                $t  = $now - ($i * 60);
                $o  = round($price * (1 + mt_rand(-8, 8) / 1000), 2);
                $c  = round($price * (1 + mt_rand(-5, 5) / 1000), 2);
                $h  = round(max($o, $c) * (1 + mt_rand(1, 5) / 1000), 2);
                $l  = round(min($o, $c) * (1 - mt_rand(1, 5) / 1000), 2);
                $out[] = ['time' => $t, 'open' => $o, 'high' => $h, 'low' => $l, 'close' => $c, 'volume' => round(mt_rand(5, 200) / 100, 4)];
            }
            return $out;
        }
        $grouped = [];
        foreach ($rows as $row) {
            $ts  = $row->recorded_at->timestamp;
            $key = (int)floor($ts / ($intervalMin * 60)) * ($intervalMin * 60);
            if (!isset($grouped[$key])) {
                $grouped[$key] = ['time' => $key, 'open' => (float)$row->price, 'high' => (float)$row->high, 'low' => (float)$row->low, 'close' => (float)$row->price, 'volume' => 0.01];
            } else {
                $grouped[$key]['high']  = max($grouped[$key]['high'], (float)$row->high);
                $grouped[$key]['low']   = min($grouped[$key]['low'],  (float)$row->low);
                $grouped[$key]['close'] = (float)$row->price;
            }
        }
        ksort($grouped);
        return array_values(array_slice($grouped, -200));
    }

    // ══════════════════════════════════════════════════════════════
    //  LEVERAGE TRADING
    // ══════════════════════════════════════════════════════════════

    // ── User: Open a Leverage Position ──────────────────────────
    public function leverageOpen(Request $request)
    {
        $request->validate([
            'coin'      => 'required|in:BTC,ETH',
            'direction' => 'required|in:long,short',
            'margin'    => 'required|numeric|min:1',
            'leverage'  => 'required|integer|in:2,5,10,20,50,100',
        ]);

        $uid      = Auth::id();
        $coin     = $request->coin;
        $dir      = $request->direction;
        $margin   = (float)$request->margin;
        $leverage = (int)$request->leverage;
        $market   = MarketIndex::forCoin($coin);

        if (!$market->trading_enabled)
            return response()->json(['error' => "Trading for {$coin} is currently disabled."], 422);

        $wallet = Wallet::where('user_id', $uid)->where('coin', 'USDT')->first();
        if (!$wallet || (float)$wallet->available < $margin)
            return response()->json(['error' => "Insufficient USDT. Available: $" . number_format((float)($wallet->available ?? 0), 2)], 422);

        $entryPrice  = (float)$market->price;
        $posSize     = round($margin * $leverage, 8);
        $liqPrice    = LeverageTrade::calcLiqPrice($dir, $entryPrice, $leverage);

        $wallet->decrement('available', $margin);
        $wallet->increment('in_order',  $margin);

        $lt = LeverageTrade::create([
            'user_id'       => $uid,
            'coin'          => $coin,
            'direction'     => $dir,
            'margin'        => $margin,
            'leverage'      => $leverage,
            'position_size' => $posSize,
            'entry_price'   => $entryPrice,
            'liq_price'     => $liqPrice,
            'status'        => 'open',
            'opened_at'     => now(),
        ]);

        Transaction::record($uid, 'trade_margin', 'USDT', $margin, 'debit',
            "Margin locked — {$dir} {$coin} x{$leverage} @ \${$entryPrice}",
            (float)$wallet->available, 'leverage_trade', $lt->id);

        return response()->json([
            'success'  => strtoupper($dir) . " {$coin} x{$leverage} opened @ $" . number_format($entryPrice, 2),
            'trade_id' => $lt->id,
        ]);
    }

    // ── User: Close a Leverage Position ─────────────────────────
    public function leverageClose(Request $request, LeverageTrade $lt)
    {
        if ($lt->user_id !== Auth::id())
            return response()->json(['error' => 'Unauthorized.'], 403);

        if (!$lt->isOpen())
            return response()->json(['error' => 'Trade is not open.'], 422);

        $market     = MarketIndex::forCoin($lt->coin);
        $closePrice = (float)$market->price;
        $pnl        = $lt->unrealisedPnl($closePrice);
        $pnlPct     = $lt->unrealisedPnlPct($closePrice);
        $margin     = (float)$lt->margin;
        $returnAmt  = max(0, round($margin + $pnl, 8));

        DB::transaction(function() use ($lt, $closePrice, $pnl, $pnlPct, $margin, $returnAmt) {
            $uid    = $lt->user_id;
            $wallet = Wallet::where('user_id', $uid)->where('coin', 'USDT')->first();
            $wallet->decrement('in_order', $margin);
            $wallet->increment('available', $returnAmt);
            $wallet->refresh();

            $lt->update([
                'status'      => 'closed',
                'close_price' => $closePrice,
                'pnl'         => $pnl,
                'pnl_pct'     => $pnlPct,
                'closed_at'   => now(),
            ]);

            $type = $pnl >= 0 ? 'trade_profit' : 'trade_loss';
            $desc = ($pnl >= 0 ? "Profit closed" : "Loss closed") .
                " — {$lt->direction} {$lt->coin} x{$lt->leverage} @ \${$closePrice} PnL: " .
                ($pnl >= 0 ? '+' : '') . "\${$pnl}";

            Transaction::record($uid, $type, 'USDT', $returnAmt, 'credit',
                $desc, (float)$wallet->available, 'leverage_trade', $lt->id);
        });

        $lt->refresh();
        $sign = $pnl >= 0 ? '+' : '';
        return response()->json([
            'success' => "Position closed — PnL: {$sign}$" . number_format($pnl, 2),
            'pnl'     => $pnl,
        ]);
    }

    // ── Shared: Get open leverage positions ──────────────────────
    public static function getLeveragePositions(string $coin, int $userId, float $currentPrice, bool $isAdmin = false): array
    {
        $query = LeverageTrade::with('user')->where('coin', $coin)->where('status', 'open');
        if (!$isAdmin) $query->where('user_id', $userId);

        foreach ($query->get() as $pos) {
            if ($pos->isLiquidatedAt($currentPrice)) {
                DB::transaction(function() use ($pos) {
                    $wallet = Wallet::where('user_id', $pos->user_id)->where('coin', 'USDT')->first();
                    if ($wallet) $wallet->decrement('in_order', (float)$pos->margin);
                    $pos->update([
                        'status'      => 'liquidated',
                        'close_price' => $pos->liq_price,
                        'pnl'         => -(float)$pos->margin,
                        'pnl_pct'     => -100,
                        'closed_at'   => now(),
                    ]);
                    Transaction::record($pos->user_id, 'trade_loss', 'USDT', 0, 'debit',
                        "LIQUIDATED — {$pos->direction} {$pos->coin} x{$pos->leverage}",
                        $wallet ? (float)$wallet->available : 0, 'leverage_trade', $pos->id);
                });
            }
        }

        $posQuery = LeverageTrade::with('user')->where('coin', $coin)->where('status', 'open');
        if (!$isAdmin) $posQuery->where('user_id', $userId);

        return $posQuery->orderByDesc('opened_at')->get()->map(function($p) use ($currentPrice, $userId) {
            $pnl    = $p->unrealisedPnl($currentPrice);
            $pnlPct = $p->unrealisedPnlPct($currentPrice);
            return [
                'id'            => $p->id,
                'is_mine'       => $p->user_id === $userId,
                'user'          => $p->user->name ?? 'Unknown',
                'coin'          => $p->coin,
                'direction'     => $p->direction,
                'margin'        => (float)$p->margin,
                'leverage'      => (int)$p->leverage,
                'position_size' => (float)$p->position_size,
                'entry_price'   => (float)$p->entry_price,
                'liq_price'     => (float)$p->liq_price,
                'current_price' => $currentPrice,
                'pnl'           => $pnl,
                'pnl_pct'       => $pnlPct,
                'opened_at'     => $p->opened_at->diffForHumans(),
            ];
        })->toArray();
    }

    // ── Admin: Leverage overview page ────────────────────────────
    public function adminLeverageIndex(Request $request)
    {
        $coin     = strtoupper($request->get('coin', 'BTC'));
        if (!in_array($coin, ['BTC','ETH'])) $coin = 'BTC';
        $market   = MarketIndex::forCoin($coin);
        $btcMkt   = MarketIndex::forCoin('BTC');
        $ethMkt   = MarketIndex::forCoin('ETH');
        $coinMeta = Wallet::supportedCoins();
        $openPos  = LeverageTrade::with('user')->where('coin', $coin)->where('status', 'open')->orderByDesc('opened_at')->get();
        $closedPos = LeverageTrade::with('user')->where('coin', $coin)->whereIn('status', ['closed','liquidated'])->orderByDesc('closed_at')->limit(50)->get();
        return view('admin.leverage', compact('coin','market','btcMkt','ethMkt','coinMeta','openPos','closedPos'));
    }
}
