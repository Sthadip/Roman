@extends('layouts.wallet')
@section('title','Trade — NEXUS')
@section('page-title','Trading')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* ── Layout ─────────────────────── */
.tpg{display:grid;grid-template-columns:1fr 310px;gap:12px}
@media(max-width:1100px){.tpg{grid-template-columns:1fr}}
.tpg-right{display:flex;flex-direction:column;gap:10px}

/* ── Coin tabs ──────────────────── */
.ctabs{display:flex;gap:8px;margin-bottom:12px}
.ctab{flex:1;padding:9px 8px;border-radius:10px;border:2px solid var(--border2);color:var(--muted);font-weight:700;font-size:12.5px;text-align:center;text-decoration:none;transition:all .15s;display:flex;flex-direction:column;align-items:center;gap:2px}
.ctab-btc.on{border-color:#F7931A;background:#F7931A14;color:#F7931A}
.ctab-eth.on{border-color:#627EEA;background:#627EEA14;color:#627EEA}
.ctab .cp{font-size:10px;font-weight:600;opacity:.85}

/* ── Ticker bar ─────────────────── */
.tkr{display:flex;gap:16px;flex-wrap:wrap;padding:10px 14px;background:var(--surface);border:1px solid var(--border2);border-radius:10px;margin-bottom:10px;align-items:center}
.tkr-s{display:flex;flex-direction:column;gap:1px}
.tkr-s .tl{font-size:9px;color:var(--dim);text-transform:uppercase;letter-spacing:.07em}
.tkr-s .tv{font-size:13px;font-weight:700;font-family:'DM Mono',monospace}
.live-badge{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:100px;font-size:10px;font-weight:700;background:#00b89420;color:#00b894;border:1px solid #00b89444}
.live-dot{width:6px;height:6px;background:#00b894;border-radius:50%;animation:ldot 1.5s ease-in-out infinite}
@keyframes ldot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(0.85)}}

/* ── Chart ──────────────────────── */
.cbox{background:#070f1a;border:1px solid var(--border2);border-radius:12px;overflow:hidden;margin-bottom:10px}
.ctb{padding:3px 9px;border-radius:5px;border:1px solid var(--border2);background:transparent;color:var(--muted);font-size:11px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s}
.ctb.on{background:var(--accent);color:#030a12;border-color:var(--accent)}
.indb{padding:3px 8px;border-radius:5px;border:1px solid #0d2035;background:transparent;color:var(--dim);font-size:10.5px;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s}
.indb.on{border-color:#627EEA88;color:#627EEA;background:#627EEA14}
#chart{width:100%;height:320px}
.rsipane{border-top:1px solid #0d2035;display:none}
#rsiPane{width:100%;height:60px}

/* ── Leverage Panel ─────────────── */
.lev-card{background:var(--surface);border:1px solid var(--border2);border-radius:12px;overflow:hidden;margin-bottom:10px}
.lev-header{padding:10px 14px;border-bottom:1px solid var(--border2);display:flex;align-items:center;justify-content:space-between}
.lev-title{font-size:12px;font-weight:700;color:var(--text)}
.lev-body{padding:14px}
.dir-pills{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:12px}
.dir-pill{padding:10px 6px;border-radius:9px;border:2px solid var(--border2);cursor:pointer;font-size:13px;font-weight:800;text-align:center;transition:all .15s;font-family:'DM Sans',sans-serif;color:var(--muted);user-select:none}
.dir-pill.long-on{background:#00b89418;border-color:#00b894;color:#00b894;box-shadow:0 0 16px #00b89422}
.dir-pill.short-on{background:#d6303118;border-color:#d63031;color:#d63031;box-shadow:0 0 16px #d6303122}
.lev-pills{display:flex;gap:5px;flex-wrap:wrap;margin-bottom:12px}
.lev-pill{flex:1;min-width:40px;padding:6px 4px;border-radius:7px;border:1px solid var(--border2);cursor:pointer;font-size:11.5px;font-weight:700;text-align:center;transition:all .15s;font-family:'DM Mono',monospace;color:var(--muted);user-select:none}
.lev-pill.on{background:var(--accent);color:#030a12;border-color:var(--accent)}
.finput{width:100%;background:#040f1c;border:1px solid var(--border2);border-radius:8px;padding:10px 12px;color:var(--text);font-family:'DM Mono',monospace;font-size:14px;outline:none;transition:border-color .15s;box-sizing:border-box}
.finput:focus{border-color:var(--accent)}
.flabel{font-size:9.5px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.07em;margin-bottom:4px;display:block}
.fg{margin-bottom:10px}
.pct-row{display:grid;grid-template-columns:repeat(4,1fr);gap:4px;margin-bottom:10px}
.pct-btn{padding:5px 0;border-radius:6px;border:1px solid var(--border2);background:transparent;color:var(--muted);font-size:11px;font-weight:700;cursor:pointer;font-family:'DM Mono',monospace;transition:all .15s}
.pct-btn:hover{background:var(--accent);color:#030a12;border-color:var(--accent)}
.lev-preview{background:#040f1c;border:1px solid var(--border2);border-radius:8px;padding:10px 12px;margin-bottom:12px;font-size:11.5px}
.lev-preview-row{display:flex;justify-content:space-between;align-items:center;padding:2px 0}
.lev-preview-row .lk{color:var(--dim)}
.lev-preview-row .lv{font-family:'DM Mono',monospace;font-weight:700}
.open-btn{width:100%;padding:12px;border:none;border-radius:9px;font-size:13.5px;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;letter-spacing:.02em}
.open-btn-long{background:linear-gradient(135deg,#00b894,#00d4a8);color:#030a12}
.open-btn-long:hover{filter:brightness(1.1);box-shadow:0 4px 20px #00b89440}
.open-btn-short{background:linear-gradient(135deg,#d63031,#ff5252);color:#fff}
.open-btn-short:hover{filter:brightness(1.1);box-shadow:0 4px 20px #d6303140}
.open-btn:disabled{opacity:.5;cursor:not-allowed;filter:none;box-shadow:none}

/* ── Open Positions ─────────────── */
.pos-wrap{background:var(--surface);border:1px solid var(--border2);border-radius:12px;overflow:hidden;margin-bottom:10px}
.pos-header{padding:10px 14px;border-bottom:1px solid var(--border2);display:flex;align-items:center;justify-content:space-between}
.pos-title{font-size:12px;font-weight:700}
.pos-count{background:#627EEA22;color:#627EEA;border:1px solid #627EEA44;font-size:10px;padding:1px 8px;border-radius:100px;font-weight:700}
.pos-table{width:100%;font-size:11px;border-collapse:collapse}
.pos-table th{padding:6px 10px;text-align:left;color:var(--dim);font-size:9.5px;font-weight:700;border-bottom:1px solid var(--border2);text-transform:uppercase;letter-spacing:.06em;white-space:nowrap}
.pos-table td{padding:8px 10px;border-bottom:1px solid var(--border);vertical-align:middle;white-space:nowrap}
.pos-table tr:last-child td{border-bottom:none}
.pos-table tr:hover td{background:#ffffff04}
.dir-long{background:#00b89420;color:#00b894;border:1px solid #00b89444;font-size:9px;padding:2px 8px;border-radius:100px;font-weight:800;white-space:nowrap}
.dir-short{background:#d6303120;color:#d63031;border:1px solid #d6303144;font-size:9px;padding:2px 8px;border-radius:100px;font-weight:800;white-space:nowrap}
.lev-badge{background:#627EEA20;color:#627EEA;border:1px solid #627EEA44;font-size:9px;padding:2px 6px;border-radius:5px;font-weight:700;white-space:nowrap}
.pnl-pos{color:#00b894;font-weight:700;font-family:'DM Mono',monospace}
.pnl-neg{color:#d63031;font-weight:700;font-family:'DM Mono',monospace}
.close-btn{padding:4px 12px;border-radius:6px;border:1px solid #d6303144;background:#d6303112;color:#d63031;font-size:11px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;white-space:nowrap}
.close-btn:hover{background:#d63031;color:#fff}
.close-btn:disabled{opacity:.5;cursor:not-allowed}
.liq-warn{font-size:9px;color:#f39c12;font-weight:600;margin-top:2px}
.dist-bar{height:3px;border-radius:2px;background:var(--border2);margin-top:3px;overflow:hidden}
.dist-fill{height:100%;border-radius:2px;transition:width .5s}

/* ── Order Book ─────────────────── */
.ob-wrap{background:var(--surface);border:1px solid var(--border2);border-radius:12px;overflow:hidden;margin-bottom:10px}
.ob-cols{display:grid;grid-template-columns:1fr 1fr}
.ob-col{max-height:220px;overflow:hidden}
.ob-col-head{display:grid;grid-template-columns:1fr 1fr 1fr;padding:4px 10px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid var(--border)}
.ob-row{display:grid;grid-template-columns:1fr 1fr 1fr;padding:3px 10px;position:relative;font-size:11px;font-family:'DM Mono',monospace}
.ob-bar{position:absolute;top:0;bottom:0;right:0;border-radius:2px;pointer-events:none;opacity:.12}
.ob-bar.ask{background:#d63031}
.ob-bar.bid{background:#00b894}
.ob-col.asks .ob-row{border-right:1px solid var(--border)}
.ob-spread{text-align:center;padding:5px;font-size:10px;font-weight:700;color:var(--dim);border-top:1px solid var(--border2);background:#040f1c}

/* ── Market Trades ──────────────── */
.mt-wrap{background:var(--surface);border:1px solid var(--border2);border-radius:12px;overflow:hidden;margin-bottom:10px}
.mt-hd{padding:8px 12px;font-size:10px;font-weight:700;text-transform:uppercase;color:var(--muted);border-bottom:1px solid var(--border2)}
.mt-list{max-height:160px;overflow-y:auto}
.mt-row{display:grid;grid-template-columns:1fr 1fr 1fr;padding:4px 12px;font-size:10.5px;font-family:'DM Mono',monospace;border-bottom:1px solid var(--border)}

/* ── Right panel ────────────────── */
.balcard{background:var(--surface);border:1px solid var(--border2);border-radius:10px;padding:12px;margin-bottom:10px}
.balitem{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid var(--border)}
.balitem:last-child{border-bottom:none}
.balitem .bn{display:flex;align-items:center;gap:6px;font-size:12.5px;font-weight:600}
.balitem .bv{font-family:'DM Mono',monospace;font-size:12.5px;font-weight:700}
.balitem .blk{font-size:9.5px;color:var(--yellow)}

/* ── Closed history ─────────────── */
.hist-wrap{background:var(--surface);border:1px solid var(--border2);border-radius:12px;overflow:hidden;margin-bottom:10px}
.hist-hd{padding:10px 14px;border-bottom:1px solid var(--border2);font-size:12px;font-weight:700}
.hist-row{display:flex;align-items:center;gap:8px;padding:8px 12px;border-bottom:1px solid var(--border);font-size:11px;flex-wrap:wrap}
.hist-row:last-child{border-bottom:none}

/* ── Toast ──────────────────────── */
#toast{position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none}
.tmsg{padding:11px 16px;border-radius:10px;font-size:12.5px;font-weight:600;max-width:320px;box-shadow:0 4px 20px #0009;animation:tsi .2s ease;pointer-events:all}
.tmsg-ok{background:#00b89418;border:1px solid #00b89455;color:#00b894}
.tmsg-er{background:#d6303118;border:1px solid #d6303155;color:#d63031}
@keyframes tsi{from{transform:translateY(12px);opacity:0}to{transform:none;opacity:1}}
.mono{font-family:'DM Mono',monospace}
.spin{display:inline-block;width:11px;height:11px;border:2px solid #fff4;border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;vertical-align:middle}
@keyframes spin{to{transform:rotate(360deg)}}
.liq-badge{background:#f39c1220;color:#f39c12;border:1px solid #f39c1244;font-size:9px;padding:2px 6px;border-radius:5px;font-weight:700}
</style>

{{-- Coin tabs --}}
<div class="ctabs">
@foreach(['BTC','ETH'] as $c)
@php $m=$c==='BTC'?$btcMkt:$ethMkt; $mt=$coinMeta[$c]; $ch=(float)$m->change_pct; @endphp
<a href="{{ route('user.trade',['coin'=>$c]) }}" class="ctab ctab-{{ strtolower($c) }} {{ $coin===$c?'on':'' }}">
  <span style="font-size:15px">{{ $mt['icon'] }}</span>
  <span>{{ $c }}/USDT</span>
  <span class="cp" style="color:{{ $ch>=0?'#00b894':'#d63031' }}">
    $<span id="sw-{{ $c }}">{{ number_format((float)$m->price,2) }}</span>
    {{ $ch>=0?'+':'' }}{{ number_format($ch,2) }}%
  </span>
</a>
@endforeach
</div>

<div class="tpg">
<div>

  {{-- Ticker --}}
  @php $cmt=$coinMeta[$coin]; $chg=(float)$market->change_pct; $liveMode=\Schema::hasColumn('market_index','live_mode')?(bool)$market->live_mode:false; @endphp
  <div class="tkr">
    <div class="tkr-s">
      <span class="tl">{{ $coin }}/USDT</span>
      <span class="tv" id="t-price" style="font-size:20px;color:{{ $cmt['color'] }}">${{ number_format((float)$market->price,2) }}</span>
    </div>
    <div class="tkr-s">
      <span class="tl">24h Change</span>
      <span class="tv" id="t-chg" style="color:{{ $chg>=0?'#00b894':'#d63031' }}">{{ $chg>=0?'+':'' }}{{ number_format($chg,2) }}%</span>
    </div>
    <div class="tkr-s">
      <span class="tl">24h High</span>
      <span class="tv" id="t-high" style="color:#00b894">${{ number_format((float)$market->high_24h,2) }}</span>
    </div>
    <div class="tkr-s">
      <span class="tl">24h Low</span>
      <span class="tv" id="t-low" style="color:#d63031">${{ number_format((float)$market->low_24h,2) }}</span>
    </div>
    <div class="tkr-s">
      <span class="tl">Mode</span>
      <span id="t-mode" class="{{ $liveMode?'live-badge':'' }}" style="{{ $liveMode?'':'font-size:11px;font-weight:700;color:var(--muted)' }}">
        @if($liveMode)&#x1F7E2; LIVE <span class="live-dot"></span>@else &#9881; Manual @endif
      </span>
    </div>
    <div id="t-paused" style="display:{{ $market->trading_enabled?'none':'flex' }};align-items:center;gap:6px;background:#d6303118;border:1px solid #d6303133;border-radius:7px;padding:5px 10px;font-size:11px;color:#d63031;font-weight:700;margin-left:auto">
      &#9888; Trading Paused
    </div>
  </div>

  {{-- Chart --}}
  <div class="cbox">
    <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border-bottom:1px solid #0d2035;flex-wrap:wrap">
      <span style="font-size:12px;font-weight:700;color:var(--muted);flex:1">{{ $coin }}/USDT &middot; Candles</span>
      <div style="display:flex;gap:4px">
        @foreach(['1'=>'1m','5'=>'5m','15'=>'15m','60'=>'1h'] as $iv=>$lb)
        <button class="ctb {{ $iv==='1'?'on':'' }}" onclick="switchIv({{ $iv }},this)">{{ $lb }}</button>
        @endforeach
      </div>
      <div style="display:flex;gap:4px">
        <button class="indb" id="iMA" onclick="togInd('MA')">MA</button>
        <button class="indb" id="iBB" onclick="togInd('BB')">BB</button>
        <button class="indb on" id="iRSI" onclick="togInd('RSI')">RSI</button>
      </div>
    </div>
    <div id="chart"></div>
    <div class="rsipane" id="rsiWrap"><div id="rsiPane"></div></div>
  </div>

  {{-- ═══ LEVERAGE TRADING PANEL ═══ --}}
  <div class="lev-card">
    <div class="lev-header">
      <span class="lev-title">&#128200; Leverage Trading &mdash; {{ $coin }}/USDT</span>
      <span id="paused-badge" style="display:{{ $market->trading_enabled?'none':'flex' }};align-items:center;gap:4px;background:#d6303118;border:1px solid #d6303133;border-radius:6px;padding:3px 9px;font-size:10px;color:#d63031;font-weight:700">&#9888; Paused</span>
    </div>
    <div class="lev-body" id="lev-body">

      {{-- Direction --}}
      <div class="fg">
        <label class="flabel">Direction</label>
        <div class="dir-pills">
          <div class="dir-pill long-on" id="dp-long" onclick="setDir('long')">&#9650; LONG</div>
          <div class="dir-pill" id="dp-short" onclick="setDir('short')">&#9660; SHORT</div>
        </div>
      </div>

      {{-- Leverage --}}
      <div class="fg">
        <label class="flabel">Leverage</label>
        <div class="lev-pills" id="lev-pills">
          @foreach([2,5,10,20,50,100] as $lx)
          <div class="lev-pill {{ $lx===10?'on':'' }}" onclick="setLev({{ $lx }},this)">{{ $lx }}x</div>
          @endforeach
        </div>
      </div>

      {{-- Margin --}}
      <div class="fg">
        <label class="flabel">Margin (USDT)</label>
        <input type="number" id="lev-margin" class="finput" placeholder="0.00" min="1" step="0.01" oninput="calcLev()">
      </div>

      {{-- Quick % --}}
      <div class="pct-row">
        @foreach([25,50,75,100] as $p)
        <button class="pct-btn" onclick="setMarginPct({{ $p }})">{{ $p }}%</button>
        @endforeach
      </div>

      {{-- Preview --}}
      <div class="lev-preview">
        <div class="lev-preview-row"><span class="lk">Entry Price</span><span class="lv" id="lp-entry">${{ number_format((float)$market->price,2) }}</span></div>
        <div class="lev-preview-row"><span class="lk">Position Size</span><span class="lv" id="lp-size">$0.00</span></div>
        <div class="lev-preview-row"><span class="lk">Leverage</span><span class="lv" id="lp-lev" style="color:var(--accent)">10x</span></div>
        <div class="lev-preview-row"><span class="lk">Liq. Price</span><span class="lv" id="lp-liq" style="color:#f39c12">—</span></div>
        <div class="lev-preview-row"><span class="lk">+1% PnL</span><span class="lv" id="lp-pnl1" style="color:#00b894">—</span></div>
      </div>

      {{-- Risk warning --}}
      <div style="padding:7px 10px;background:#f39c1208;border:1px solid #f39c1222;border-radius:7px;font-size:10px;color:#f39c12;margin-bottom:10px;line-height:1.6">
        &#9888; High leverage = high risk. If price reaches the liquidation price, your entire margin is lost.
      </div>

      <button class="open-btn open-btn-long" id="open-btn" onclick="openPosition()">
        &#9650; Open LONG Position
      </button>
    </div>
  </div>

  {{-- ═══ OPEN POSITIONS ═══ --}}
  <div class="pos-wrap">
    <div class="pos-header">
      <span class="pos-title">&#128293; Open Positions</span>
      <span class="pos-count" id="pos-count">0 open</span>
    </div>
    <div id="positions-body">
      <div style="text-align:center;padding:24px;color:var(--muted);font-size:12px">No open positions</div>
    </div>
  </div>

  {{-- ═══ ORDER BOOK ═══ --}}
  <div class="ob-wrap">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;border-bottom:1px solid var(--border2)">
      <span style="font-size:11px;font-weight:700;color:var(--muted)">Order Book</span>
      <span style="font-size:9px;color:var(--dim)" id="ob-mode-lbl">Live depth</span>
    </div>
    <div class="ob-cols">
      <div class="ob-col asks">
        <div class="ob-col-head" style="color:#d63031"><span>Price</span><span>Amt</span><span>Total</span></div>
        <div id="ob-asks"></div>
      </div>
      <div class="ob-col bids">
        <div class="ob-col-head" style="color:#00b894"><span>Price</span><span>Amt</span><span>Total</span></div>
        <div id="ob-bids"></div>
      </div>
    </div>
    <div class="ob-spread" id="ob-spread">Spread: &mdash;</div>
  </div>

  {{-- Market Trades --}}
  <div class="mt-wrap">
    <div class="mt-hd">Market Trades</div>
    <div class="mt-list" id="mt-list">
      <div style="text-align:center;padding:16px;color:var(--muted);font-size:11px">No recent trades</div>
    </div>
  </div>

  {{-- Closed History --}}
  <div class="hist-wrap">
    <div class="hist-hd">My Position History</div>
    <div id="hist-body">
      <div style="text-align:center;padding:18px;color:var(--muted);font-size:12px">No closed positions yet</div>
    </div>
  </div>

</div>
<div class="tpg-right">

  {{-- Balance --}}
  <div class="balcard">
    <div style="font-size:10px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px">Balances</div>
    @foreach(['USDT','BTC','ETH'] as $c)
    @php $w=$wallets[$c]??null; $mt=$coinMeta[$c]; @endphp
    <div class="balitem">
      <div class="bn"><span style="color:{{ $mt['color'] }}">{{ $mt['icon'] }}</span> {{ $c }}</div>
      <div style="text-align:right">
        <div class="bv" id="bal-{{ $c }}" style="color:{{ $mt['color'] }}">{{ number_format((float)($w->available??0),$c==='USDT'?2:8) }}</div>
        <div class="blk" id="lock-{{ $c }}" style="{{ ($w->in_order??0)>0?'':'display:none' }}">{{ ($w->in_order??0)>0?number_format((float)$w->in_order,$c==='USDT'?2:8).' locked':'' }}</div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Live PnL Summary --}}
  <div class="balcard">
    <div style="font-size:10px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px">Unrealised PnL</div>
    <div style="text-align:center;padding:8px 0">
      <div class="mono" id="total-pnl" style="font-size:22px;font-weight:800;color:#00b894">$0.00</div>
      <div id="total-pnl-pct" style="font-size:12px;color:var(--muted);margin-top:2px">0.00%</div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px">
      <div style="background:#00b89412;border:1px solid #00b89433;border-radius:7px;padding:8px;text-align:center">
        <div style="font-size:9px;color:var(--dim);margin-bottom:2px">LONG</div>
        <div class="mono" id="long-pnl" style="font-weight:700;color:#00b894;font-size:13px">$0.00</div>
      </div>
      <div style="background:#d6303112;border:1px solid #d6303133;border-radius:7px;padding:8px;text-align:center">
        <div style="font-size:9px;color:var(--dim);margin-bottom:2px">SHORT</div>
        <div class="mono" id="short-pnl" style="font-weight:700;color:#d63031;font-size:13px">$0.00</div>
      </div>
    </div>
  </div>

  {{-- How it works --}}
  <div style="background:var(--surface);border:1px solid var(--border2);border-radius:10px;padding:12px;font-size:10.5px;color:var(--dim);line-height:1.8">
    <div style="font-weight:700;color:var(--muted);margin-bottom:6px">&#8505; Leverage Trading</div>
    <div>&bull; <strong style="color:var(--text)">Long</strong> = profit when price rises</div>
    <div>&bull; <strong style="color:var(--text)">Short</strong> = profit when price falls</div>
    <div>&bull; Margin is locked when trade opens</div>
    <div>&bull; Only you can close your trade</div>
    <div>&bull; At liquidation price, margin is lost</div>
    <div style="margin-top:6px;padding:6px 8px;background:#f39c1210;border-radius:6px;color:#f39c12;font-size:10px">
      &#9888; Higher leverage = higher risk of liquidation
    </div>
  </div>

</div>
</div>
<div id="toast"></div>

<script src="https://unpkg.com/lightweight-charts@4.1.0/dist/lightweight-charts.standalone.production.js"></script>
<script>
var COIN     = '{{ $coin }}';
var PRICE    = {{ (float)$market->price }};
var HIGH     = {{ (float)$market->high_24h }};
var LOW      = {{ (float)$market->low_24h }};
var USDT_BAL = {{ (float)($wallets['USDT']->available??0) }};
var CSRF     = document.querySelector('meta[name=csrf-token]').content;
var APIU     = '{{ route("user.trade.api","_") }}'.replace('_', COIN);
var LEVOPEN  = '{{ route("user.leverage.open") }}';
var LEVCLOSE = '{{ url("trade/leverage") }}';
var IV       = 1;
var _dir     = 'long';
var _lev     = 10;

// ── Chart ─────────────────────────────────────────────────────────
var el = document.getElementById('chart');
var chart = LightweightCharts.createChart(el, {
  layout:{background:{color:'#070f1a'},textColor:'#4a7a9b'},
  grid:{vertLines:{color:'#0d203520'},horzLines:{color:'#0d203530'}},
  crosshair:{mode:LightweightCharts.CrosshairMode.Normal},
  rightPriceScale:{borderColor:'#0d2035',visible:true,scaleMargins:{top:0.08,bottom:0.2}},
  timeScale:{borderColor:'#0d2035',timeVisible:true,secondsVisible:false},
  width:el.clientWidth, height:320,
});
var cs = chart.addCandlestickSeries({upColor:'#00b894',downColor:'#d63031',borderUpColor:'#00b894',borderDownColor:'#d63031',wickUpColor:'#00b89488',wickDownColor:'#d6303188'});
var vs = chart.addHistogramSeries({priceFormat:{type:'volume'},priceScaleId:'vol',scaleMargins:{top:0.85,bottom:0}});
chart.priceScale('vol').applyOptions({scaleMargins:{top:0.85,bottom:0}});
new ResizeObserver(function(){chart.applyOptions({width:el.clientWidth});if(rsiC)rsiC.applyOptions({width:document.getElementById('rsiPane').clientWidth});}).observe(el);
var indOn={MA:false,BB:false,RSI:true}, ser={ma:null,bbH:null,bbL:null}, rsiC=null, rsiSr=null, allC=[];

function _ma(d,p){return d.map(function(c,i){if(i<p-1)return null;var s=0;for(var j=i-p+1;j<=i;j++)s+=d[j].close;return{time:c.time,value:s/p};}).filter(Boolean);}
function _bb(d,p,m){p=p||20;m=m||2;return d.map(function(c,i){if(i<p-1)return null;var sl=d.slice(i-p+1,i+1),mn=sl.reduce(function(a,b){return a+b.close;},0)/p,sd=Math.sqrt(sl.reduce(function(a,b){return a+(b.close-mn)*(b.close-mn);},0)/p);return{time:c.time,hi:mn+m*sd,lo:mn-m*sd};}).filter(Boolean);}
function _rsi(d,p){p=p||14;var r=[],g=[],l=[];for(var i=1;i<d.length;i++){var x=d[i].close-d[i-1].close;g.push(x>0?x:0);l.push(x<0?-x:0);if(i>=p){var ag=g.slice(-p).reduce(function(a,b){return a+b;},0)/p,al=l.slice(-p).reduce(function(a,b){return a+b;},0)/p;r.push({time:d[i].time,value:al===0?100:parseFloat((100-100/(1+ag/al)).toFixed(2))});}}return r;}

function applyInds(){
  if(allC.length<2) return;
  if(indOn.MA){if(!ser.ma)ser.ma=chart.addLineSeries({color:'#f1c40f',lineWidth:1.5,priceLineVisible:false});ser.ma.setData(_ma(allC,20));}else if(ser.ma){chart.removeSeries(ser.ma);ser.ma=null;}
  if(indOn.BB){var b=_bb(allC);if(!ser.bbH){ser.bbH=chart.addLineSeries({color:'#627EEA55',lineWidth:1,priceLineVisible:false});ser.bbL=chart.addLineSeries({color:'#627EEA55',lineWidth:1,priceLineVisible:false});}ser.bbH.setData(b.map(function(x){return{time:x.time,value:x.hi};}));ser.bbL.setData(b.map(function(x){return{time:x.time,value:x.lo};}));}else if(ser.bbH){chart.removeSeries(ser.bbH);chart.removeSeries(ser.bbL);ser.bbH=null;ser.bbL=null;}
  var rw=document.getElementById('rsiWrap');
  if(indOn.RSI){rw.style.display='block';if(!rsiC){var re=document.getElementById('rsiPane');rsiC=LightweightCharts.createChart(re,{layout:{background:{color:'#070f1a'},textColor:'#4a7a9b'},grid:{vertLines:{color:'#0d203515'},horzLines:{color:'#0d203520'}},rightPriceScale:{borderColor:'#0d2035',visible:true,scaleMargins:{top:0.05,bottom:0.05}},timeScale:{borderColor:'#0d2035',visible:false},width:re.clientWidth,height:60});rsiSr=rsiC.addLineSeries({color:'#a855f7',lineWidth:1.5,priceLineVisible:false});chart.timeScale().subscribeVisibleLogicalRangeChange(function(r){if(r)rsiC.timeScale().setVisibleLogicalRange(r);});rsiC.timeScale().subscribeVisibleLogicalRangeChange(function(r){if(r)chart.timeScale().setVisibleLogicalRange(r);});}rsiSr.setData(_rsi(allC));}else{rw.style.display='none';if(rsiC){rsiC.remove();rsiC=null;rsiSr=null;}}
}
function togInd(k){indOn[k]=!indOn[k];document.getElementById('i'+k).classList.toggle('on',indOn[k]);applyInds();}
function switchIv(iv,btn){IV=iv;document.querySelectorAll('.ctb').forEach(function(b){b.classList.remove('on');});btn.classList.add('on');fetchState();}

// ── Leverage UI ───────────────────────────────────────────────────
function setDir(d){
  _dir=d;
  document.getElementById('dp-long').className='dir-pill'+(d==='long'?' long-on':'');
  document.getElementById('dp-short').className='dir-pill'+(d==='short'?' short-on':'');
  var btn=document.getElementById('open-btn');
  if(d==='long'){btn.className='open-btn open-btn-long';btn.textContent='\u25B2 Open LONG Position';}
  else{btn.className='open-btn open-btn-short';btn.textContent='\u25BC Open SHORT Position';}
  calcLev();
}
function setLev(l,el){
  _lev=l;
  document.querySelectorAll('.lev-pill').forEach(function(p){p.classList.remove('on');});
  el.classList.add('on');
  calcLev();
}
function setMarginPct(pct){
  var v=(USDT_BAL*pct/100);
  document.getElementById('lev-margin').value=v>0?v.toFixed(2):'';
  calcLev();
}
function calcLev(){
  var margin=parseFloat(document.getElementById('lev-margin').value)||0;
  var posSize=margin*_lev;
  // Liq price calc (mirrors PHP)
  var mmr=0.005;
  var liqPrice=0;
  if(PRICE>0&&margin>0){
    liqPrice=_dir==='long'?PRICE*(1-1/_lev+mmr):PRICE*(1+1/_lev-mmr);
  }
  document.getElementById('lp-entry').textContent='$'+fp(PRICE);
  document.getElementById('lp-size').textContent='$'+(posSize>0?posSize.toFixed(2):'0.00');
  document.getElementById('lp-lev').textContent=_lev+'x';
  document.getElementById('lp-liq').textContent=liqPrice>0?'$'+fp(liqPrice):'—';
  // PnL on 1% move
  var pnl1=posSize*0.01;
  document.getElementById('lp-pnl1').textContent=pnl1>0?('+$'+pnl1.toFixed(2))+' / (\u2212$'+pnl1.toFixed(2)+')':'—';
}

function openPosition(){
  var margin=parseFloat(document.getElementById('lev-margin').value)||0;
  if(margin<1){toast('Enter margin amount (min $1)','er');return;}
  if(margin>USDT_BAL){toast('Insufficient USDT balance','er');return;}
  var btn=document.getElementById('open-btn');
  btn.disabled=true;btn.innerHTML='<span class="spin"></span> Opening...';
  fetch(LEVOPEN,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},body:JSON.stringify({coin:COIN,direction:_dir,margin:margin,leverage:_lev})})
    .then(function(r){return r.json();})
    .then(function(d){
      if(d.error){toast(d.error,'er');}
      else{toast(d.success,'ok');document.getElementById('lev-margin').value='';fetchState();}
    })
    .catch(function(){toast('Network error','er');})
    .finally(function(){
      btn.disabled=false;
      btn.innerHTML=(_dir==='long'?'\u25B2 Open LONG':'\u25BC Open SHORT')+' Position';
    });
}

function closePosition(id){
  var btn=document.getElementById('cbtn-'+id);
  if(!btn) return;
  btn.disabled=true;btn.innerHTML='<span class="spin"></span>';
  fetch(LEVCLOSE+'/'+id+'/close',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},body:JSON.stringify({})})
    .then(function(r){return r.json();})
    .then(function(d){
      if(d.error){toast(d.error,'er');}
      else{toast(d.success,'ok');fetchState();}
    })
    .catch(function(){toast('Network error','er');})
    .finally(function(){if(btn){btn.disabled=false;btn.textContent='Close';}});
}

// ── Render positions ──────────────────────────────────────────────
function renderPositions(positions){
  var el=document.getElementById('positions-body');
  var cnt=document.getElementById('pos-count');
  if(!positions||positions.length===0){
    el.innerHTML='<div style="text-align:center;padding:24px;color:var(--muted);font-size:12px">No open positions</div>';
    cnt.textContent='0 open';
    document.getElementById('total-pnl').textContent='$0.00';
    document.getElementById('total-pnl-pct').textContent='0.00%';
    document.getElementById('long-pnl').textContent='$0.00';
    document.getElementById('short-pnl').textContent='$0.00';
    return;
  }
  cnt.textContent=positions.length+' open';

  // Calc totals
  var totalPnl=0, totalMargin=0, longPnl=0, shortPnl=0;
  positions.forEach(function(p){totalPnl+=p.pnl;totalMargin+=p.margin;if(p.direction==='long')longPnl+=p.pnl;else shortPnl+=p.pnl;});
  var totalPct=totalMargin>0?(totalPnl/totalMargin*100):0;

  var tpEl=document.getElementById('total-pnl');
  tpEl.textContent=(totalPnl>=0?'+':'')+'$'+Math.abs(totalPnl).toFixed(2);
  tpEl.style.color=totalPnl>=0?'#00b894':'#d63031';
  var tpPctEl=document.getElementById('total-pnl-pct');
  tpPctEl.textContent=(totalPct>=0?'+':'')+totalPct.toFixed(2)+'%';
  tpPctEl.style.color=totalPct>=0?'#00b894':'#d63031';
  document.getElementById('long-pnl').textContent=(longPnl>=0?'+':'')+'$'+Math.abs(longPnl).toFixed(2);
  document.getElementById('long-pnl').style.color=longPnl>=0?'#00b894':'#d63031';
  document.getElementById('short-pnl').textContent=(shortPnl>=0?'+':'')+'$'+Math.abs(shortPnl).toFixed(2);
  document.getElementById('short-pnl').style.color=shortPnl>=0?'#00b894':'#d63031';

  // Draw price lines for liq prices
  try {
    positions.forEach(function(p){
      if(p.is_mine){
        cs.createPriceLine({price:p.liq_price,color:'#f39c1266',lineWidth:1,lineStyle:LightweightCharts.LineStyle.Dashed,title:'LIQ '+p.direction.toUpperCase(),axisLabelVisible:true});
        cs.createPriceLine({price:p.entry_price,color:(p.direction==='long'?'#00b89466':'#d6303166'),lineWidth:1,lineStyle:LightweightCharts.LineStyle.Dotted,title:'ENTRY',axisLabelVisible:true});
      }
    });
  } catch(e){}

  // Render table
  var rows=positions.map(function(p){
    var pnlCls=p.pnl>=0?'pnl-pos':'pnl-neg';
    var pnlStr=(p.pnl>=0?'+':'')+'$'+Math.abs(p.pnl).toFixed(2);
    var pnlPctStr=(p.pnl_pct>=0?'+':'')+p.pnl_pct.toFixed(2)+'%';
    // Distance to liquidation as %
    var distToLiq=Math.abs((PRICE-p.liq_price)/PRICE*100);
    var distColor=distToLiq<5?'#d63031':distToLiq<15?'#f39c12':'#00b894';
    var closeBtn=p.is_mine?('<button class="close-btn" id="cbtn-'+p.id+'" onclick="closePosition('+p.id+')">Close</button>'):'<span style="font-size:10px;color:var(--dim)">'+p.user+'</span>';
    return '<tr><td>'+
      '<span class="'+(p.direction==='long'?'dir-long':'dir-short')+'">'+(p.direction==='long'?'LONG':'SHORT')+'</span>'+
      '</td><td>'+
      '<span class="lev-badge">'+p.leverage+'x</span>'+
      '</td><td>'+
      '<span class="mono" style="color:var(--text)">$'+p.margin.toFixed(2)+'</span>'+
      '</td><td>'+
      '<span class="mono" style="color:var(--muted)">$'+p.position_size.toFixed(2)+'</span>'+
      '</td><td>'+
      '<span class="mono" style="color:var(--muted)">$'+p.entry_price.toFixed(2)+'</span>'+
      '</td><td>'+
      '<span class="mono" style="color:#f39c12">$'+p.liq_price.toFixed(2)+'</span>'+
      '<div class="dist-bar"><div class="dist-fill" style="width:'+Math.min(distToLiq/20*100,100).toFixed(0)+'%;background:'+distColor+'"></div></div>'+
      '</td><td>'+
      '<span class="'+pnlCls+'">'+pnlStr+'</span>'+
      '<span style="font-size:9.5px;color:var(--dim);margin-left:4px">('+pnlPctStr+')</span>'+
      '</td><td style="font-size:10px;color:var(--dim)">'+p.opened_at+'</td>'+
      '<td>'+closeBtn+'</td></tr>';
  }).join('');

  el.innerHTML='<div style="overflow-x:auto"><table class="pos-table"><thead><tr>'+
    '<th>Dir</th><th>Lev</th><th>Margin</th><th>Size</th><th>Entry</th><th>Liq Price</th><th>PnL</th><th>Opened</th><th></th>'+
    '</tr></thead><tbody>'+rows+'</tbody></table></div>';
}

// ── Closed history ────────────────────────────────────────────────
function renderHistory(hist){
  var el=document.getElementById('hist-body');if(!el||!hist) return;
  var closed=hist.filter(function(h){return h.status==='closed'||h.status==='liquidated';});
  if(closed.length===0){el.innerHTML='<div style="text-align:center;padding:18px;color:var(--muted);font-size:12px">No closed positions yet</div>';return;}
  el.innerHTML=closed.map(function(t){
    var pnlStr=(t.pnl>=0?'+':'')+'$'+Math.abs(t.pnl||0).toFixed(2);
    var pnlCol=t.pnl>=0?'#00b894':'#d63031';
    return '<div class="hist-row">'+
      '<span class="'+(t.direction==='long'?'dir-long':'dir-short')+'">'+(t.direction||'?').toUpperCase()+'</span>'+
      '<span class="lev-badge">'+t.leverage+'x</span>'+
      '<span class="mono" style="color:var(--muted)">$'+(t.margin||0).toFixed(2)+' margin</span>'+
      '<span style="flex:1"></span>'+
      '<span class="mono" style="color:'+pnlCol+';font-weight:700">'+pnlStr+'</span>'+
      (t.status==='liquidated'?'<span class="liq-badge">LIQUIDATED</span>':'')+
      '<span style="font-size:10px;color:var(--dim)">'+t.at+'</span>'+
      '</div>';
  }).join('');
}

// ── Market state ──────────────────────────────────────────────────
function fetchState(){
  fetch(APIU+'?interval='+IV)
    .then(function(r){if(!r.ok)throw new Error();return r.json();})
    .then(function(d){
      applyMarket(d.market);
      applyCandles(d.candles);
      applyWallets(d.wallets);
      renderPositions(d.leverage_positions||[]);
      applyOrderBook(d.order_book);
      applyMarketTrades(d.recent_fills);
      renderHistory(d.leverage_history||[]);
    })
    .catch(function(e){console.warn('Poll error',e);});
}

function applyCandles(candles){
  if(!candles||candles.length===0) return;
  var seen=new Set(),clean=[];
  for(var i=0;i<candles.length;i++){var c=candles[i];if(!seen.has(c.time)&&c.open&&c.high&&c.low&&c.close){seen.add(c.time);clean.push(c);}}
  clean.sort(function(a,b){return a.time-b.time;});
  if(clean.length===0) return;
  allC=clean;
  cs.setData(clean);
  vs.setData(clean.map(function(c){return{time:c.time,value:c.volume||0.001,color:c.close>=c.open?'#00b89428':'#d6303128'};}));
  applyInds();
}

function applyMarket(m){
  if(!m) return;
  PRICE=m.price; HIGH=m.high_24h; LOW=m.low_24h;
  document.getElementById('t-price').textContent='$'+fp(PRICE);
  var ce=document.getElementById('t-chg');ce.textContent=(m.change_pct>=0?'+':'')+m.change_pct.toFixed(2)+'%';ce.style.color=m.change_pct>=0?'#00b894':'#d63031';
  document.getElementById('t-high').textContent='$'+fp(HIGH);
  document.getElementById('t-low').textContent='$'+fp(LOW);
  var me=document.getElementById('t-mode');
  if(me){if(m.live_mode){me.className='live-badge';me.innerHTML='&#x1F7E2; LIVE <span class="live-dot"></span>';}else{me.className='';me.style.cssText='font-size:11px;font-weight:700;color:var(--muted)';me.textContent='Manual';}}
  document.getElementById('t-paused').style.display=m.trading_enabled?'none':'flex';
  document.getElementById('paused-badge').style.display=m.trading_enabled?'none':'flex';
  document.getElementById('open-btn').disabled=!m.trading_enabled;
  document.getElementById('lp-entry').textContent='$'+fp(PRICE);
  ['BTC','ETH'].forEach(function(c){var e=document.getElementById('sw-'+c);if(e&&c===COIN)e.textContent=fp(PRICE);});
  calcLev();
}

function applyWallets(ws){
  if(!ws) return;
  if(ws.USDT) USDT_BAL=ws.USDT.available;
  ['BTC','ETH','USDT'].forEach(function(c){
    if(ws[c]){
      var el=document.getElementById('bal-'+c);if(el)el.textContent=(c==='USDT'?ws[c].available.toFixed(2):ws[c].available.toFixed(8));
      var lk=document.getElementById('lock-'+c);if(lk){if(ws[c].in_order>0){lk.textContent=ws[c].in_order.toFixed(c==='USDT'?2:8)+' locked';lk.style.display='block';}else lk.style.display='none';}
    }
  });
}

function applyOrderBook(ob){
  if(!ob) return;
  var asksEl=document.getElementById('ob-asks'),bidsEl=document.getElementById('ob-bids');if(!asksEl||!bidsEl) return;
  var maxSum=ob.max_sum||1;
  var ah=[].concat(ob.asks).reverse().map(function(r){return '<div class="ob-row"><div class="ob-bar ask" style="width:'+Math.min((r.sum/maxSum)*100,100).toFixed(1)+'%"></div><span style="color:#d63031;position:relative">'+r.price.toFixed(2)+'</span><span style="color:var(--muted);position:relative">'+r.amount.toFixed(4)+'</span><span style="color:var(--dim);position:relative">'+r.total.toFixed(2)+'</span></div>';}).join('');
  var bh=ob.bids.map(function(r){return '<div class="ob-row"><div class="ob-bar bid" style="width:'+Math.min((r.sum/maxSum)*100,100).toFixed(1)+'%"></div><span style="color:#00b894;position:relative">'+r.price.toFixed(2)+'</span><span style="color:var(--muted);position:relative">'+r.amount.toFixed(4)+'</span><span style="color:var(--dim);position:relative">'+r.total.toFixed(2)+'</span></div>';}).join('');
  asksEl.innerHTML=ah; bidsEl.innerHTML=bh;
  var sp=document.getElementById('ob-spread');if(sp)sp.textContent='Spread: $'+(ob.spread||0).toFixed(2);
}

function applyMarketTrades(fills){
  if(!fills||!fills.length) return;
  var el=document.getElementById('mt-list');if(!el) return;
  el.innerHTML=fills.map(function(f){return '<div class="mt-row"><span style="color:'+(f.side==='buy'?'#00b894':'#d63031')+';font-weight:700">'+f.price.toFixed(2)+'</span><span style="color:var(--muted)">'+f.coin_amount.toFixed(4)+'</span><span style="color:var(--dim);font-size:9px">'+new Date(f.at*1000).toLocaleTimeString()+'</span></div>';}).join('');
}

function fp(p){return p>=1000?p.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}):p.toFixed(p<1?6:2);}
function toast(msg,t){t=t||'ok';var e=document.createElement('div');e.className='tmsg tmsg-'+t;e.textContent=msg;document.getElementById('toast').appendChild(e);setTimeout(function(){e.remove();},4000);}

setDir('long');
fetchState();
setInterval(fetchState, 4000);
</script>
@endsection
