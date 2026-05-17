# Leverage Trading Update — Install Guide

## Files Changed

```
app/Http/Controllers/TradeController.php   ← leverage open/close methods + apiState
app/Models/LeverageTrade.php               ← NEW model
app/Models/MarketIndex.php                 ← live_mode fields
database/migrations/2026_05_17_..._create_leverage_trades_table.php  ← NEW table
database/migrations/2026_05_16_..._add_live_mode_to_market_index.php ← run if not yet run
resources/views/user/trade.blade.php       ← full leverage trading UI
resources/views/admin/trade.blade.php      ← admin view of all open positions
routes/web.php                             ← 2 new leverage routes
```

## After copying files, run:

```bash
php artisan migrate
```

## What's New

### Leverage Trading Panel (User)
- Choose LONG ▲ or SHORT ▼ direction
- Pick leverage: 2x · 5x · 10x · 20x · 50x · 100x
- Enter USDT margin (25/50/75/100% shortcuts)
- Live preview: Position size, Entry price, Liquidation price, +1% PnL estimate
- Margin locked on open, returned ± PnL on close
- Only the user who opened the trade can close it
- Auto-liquidation: if price hits liq price, margin is lost
- Entry & liquidation price lines drawn on the chart

### Open Positions Table
- Shows all user's open positions with live PnL
- Columns: Direction, Leverage, Margin, Size, Entry, Liq Price, PnL, Opened At, Close button
- Distance-to-liquidation progress bar (green → yellow → red)
- Live unrealised PnL summary card (Long PnL / Short PnL / Total)

### Admin View
- Admins see ALL users' open leverage positions on the trade panel
- Columns include User name, same position details, live PnL

### Closed Position History
- Closed and liquidated positions shown in history tab
- PnL shown in green/red, liquidated trades marked with badge
