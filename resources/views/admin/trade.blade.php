@extends('layouts.wallet')
@section('title','Trading Panel — Admin')
@section('page-title','Trading Panel')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
.apg{display:grid;grid-template-columns:1fr 360px;gap:12px}
@media(max-width:1140px){.apg{grid-template-columns:1fr}}
.apg-right{display:flex;flex-direction:column;gap:10px}
.ctabs{display:flex;gap:8px;margin-bottom:12px}
.ctab{flex:1;padding:9px 8px;border-radius:10px;border:2px solid var(--border2);color:var(--muted);font-weight:700;font-size:12.5px;text-align:center;text-decoration:none;transition:all .15s;display:flex;flex-direction:column;align-items:center;gap:2px}
.ctab-btc.on{border-color:#F7931A;background:#F7931A14;color:#F7931A}
.ctab-eth.on{border-color:#627EEA;background:#627EEA14;color:#627EEA}
.tkr{display:flex;gap:14px;flex-wrap:wrap;padding:10px 14px;background:var(--surface);border:1px solid var(--border2);border-radius:10px;margin-bottom:10px;align-items:center}
.ts{display:flex;flex-direction:column;gap:1px}
.ts .tl{font-size:9px;color:var(--dim);text-transform:uppercase;letter-spacing:.07em}
.ts .tv{font-size:13px;font-weight:700;font-family:'DM Mono',monospace}
.live-badge{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:100px;font-size:10px;font-weight:700;background:#00b89420;color:#00b894;border:1px solid #00b89444}
.live-dot{width:6px;height:6px;background:#00b894;border-radius:50%;animation:ldot 1.5s ease-in-out infinite}
@keyframes ldot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(0.85)}}
.cbox{background:#070f1a;border:1px solid var(--border2);border-radius:12px;overflow:hidden;margin-bottom:10px}
.ctb{padding:3px 9px;border-radius:5px;border:1px solid var(--border2);background:transparent;color:var(--muted);font-size:11px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s}
.ctb.on{background:var(--accent);color:#030a12;border-color:var(--accent)}
.indb{padding:3px 8px;border-radius:5px;border:1px solid #0d2035;background:transparent;color:var(--dim);font-size:10.5px;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s}
.indb.on{border-color:#627EEA88;color:#627EEA;background:#627EEA14}
#admChart{width:100%;height:320px}
.rsipane2{border-top:1px solid #0d2035;display:none}
#admRsi{width:100%;height:60px}
.ob-wrap{background:var(--surface);border:1px solid var(--border2);border-radius:12px;overflow:hidden;margin-bottom:10px}
.ob-cols{display:grid;grid-template-columns:1fr 1fr}
.ob-col{max-height:240px;overflow:hidden}
.ob-col-head{display:grid;grid-template-columns:1fr 1fr 1fr;padding:4px 10px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid var(--border)}
.ob-row{display:grid;grid-template-columns:1fr 1fr 1fr;padding:3px 10px;position:relative;font-size:11px;font-family:'DM Mono',monospace}
.ob-bar{position:absolute;top:0;bottom:0;right:0;border-radius:2px;pointer-events:none;opacity:.12}
.ob-bar.ask{background:#d63031}
.ob-bar.bid{background:#00b894}
.ob-col.asks .ob-row{border-right:1px solid var(--border)}
.ob-spread{text-align:center;padding:5px;font-size:10px;font-weight:700;color:var(--dim);border-top:1px solid var(--border2);background:#040f1c}
.mt-wrap{background:var(--surface);border:1px solid var(--border2);border-radius:12px;overflow:hidden;margin-bottom:10px}
.mt-hd{padding:8px 12px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);border-bottom:1px solid var(--border2)}
.mt-list{max-height:160px;overflow-y:auto}
.mt-row{display:grid;grid-template-columns:1fr 1fr 1fr;padding:4px 12px;font-size:10.5px;font-family:'DM Mono',monospace;border-bottom:1px solid var(--border)}
.otabs{display:flex;border-radius:7px;overflow:hidden;border:1px solid var(--border2);margin-bottom:10px}
.otb{flex:1;padding:8px 6px;font-size:11.5px;font-weight:700;border:none;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;color:var(--muted);transition:all .15s;display:flex;align-items:center;justify-content:center;gap:5px}
.otb.bon{background:#00b89420;color:#00b894}
.otb.son{background:#d6303120;color:#d63031}
.ocnt{background:#ffffff14;border-radius:100px;padding:1px 6px;font-size:10px}
.ord-row{display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid var(--border);font-size:11.5px;flex-wrap:wrap}
.ord-row:last-child{border-bottom:none}
.uinf{display:flex;flex-direction:column;gap:1px;flex:1;min-width:0}
.uinf .un{font-size:12px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ovals{display:flex;flex-direction:column;gap:1px;text-align:right}
.ovals .ov{font-size:12px;font-weight:700;font-family:'DM Mono',monospace}
.ovals .os{font-size:10px;color:var(--dim)}
.actbtn{padding:4px 10px;border-radius:6px;border:none;cursor:pointer;font-size:11px;font-weight:700;font-family:'DM Sans',sans-serif;transition:all .15s}
.ab-fill-buy{background:#00b89420;color:#00b894}.ab-fill-buy:hover{background:#00b894;color:#030a12}
.ab-fill-sell{background:#d6303120;color:#d63031}.ab-fill-sell:hover{background:#d63031;color:#fff}
.ab-cancel{background:var(--surface);color:var(--muted);border:1px solid var(--border2)}.ab-cancel:hover{color:#d63031;border-color:#d63031}
.rcard{background:var(--surface);border:1px solid var(--border2);border-radius:10px;padding:14px}
.rcard-hd{font-size:10px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;display:flex;align-items:center;justify-content:space-between}
.fi{width:100%;background:#040f1c;border:1px solid var(--border2);border-radius:7px;padding:9px 11px;color:var(--text);font-family:'DM Mono',monospace;font-size:13px;outline:none;transition:border-color .15s;box-sizing:border-box}
.fi:focus{border-color:var(--accent)}
.fi:disabled{opacity:.45;cursor:not-allowed}
.fl{font-size:9.5px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px;display:block}
.fg{margin-bottom:8px}
.frow{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.drpills{display:flex;gap:5px}
.drpill{flex:1;padding:7px 4px;border-radius:7px;border:1px solid var(--border2);cursor:pointer;font-size:11.5px;font-weight:700;text-align:center;transition:all .15s;font-family:'DM Sans',sans-serif;color:var(--muted);user-select:none}
.drpill input{display:none}
.drpill.up-on{background:#00b89420;border-color:#00b894;color:#00b894}
.drpill.dn-on{background:#d6303120;border-color:#d63031;color:#d63031}
.drpill.no-on{background:#ffffff14;border-color:#888;color:var(--text)}
.drpill.locked{opacity:.4;cursor:not-allowed;pointer-events:none}
.sidepills{display:flex;gap:5px;margin-bottom:8px}
.sidepill{flex:1;padding:7px;border-radius:7px;border:1px solid var(--border2);cursor:pointer;font-size:12px;font-weight:700;text-align:center;transition:all .15s;font-family:'DM Sans',sans-serif;color:var(--muted);user-select:none}
.sidepill input{display:none}
.sidepill.buy-on{background:#00b89420;border-color:#00b894;color:#00b894}
.sidepill.sell-on{background:#d6303120;border-color:#d63031;color:#d63031}
.user-list{max-height:150px;overflow-y:auto;border:1px solid var(--border2);border-radius:7px;background:#040f1c}
.user-chk{display:flex;align-items:center;gap:8px;padding:7px 10px;border-bottom:1px solid var(--border);cursor:pointer;font-size:11.5px;transition:background .12s}
.user-chk:last-child{border-bottom:none}
.user-chk:hover{background:#ffffff07}
.user-chk input{accent-color:var(--accent);cursor:pointer}
.lotbl{width:100%;font-size:11px;border-collapse:collapse}
.lotbl th{padding:4px 6px;text-align:left;color:var(--dim);font-size:9.5px;font-weight:700;border-bottom:1px solid var(--border);text-transform:uppercase;letter-spacing:.06em}
.lotbl td{padding:5px 6px;border-bottom:1px solid var(--border)}
.lotbl tr:last-child td{border-bottom:none}
.stats-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.stat-box{border-radius:8px;padding:10px}
.stat-buy{background:#00b89412;border:1px solid #00b89433}
.stat-sell{background:#d6303112;border:1px solid #d6303133}
.reset-btn{width:100%;padding:11px;border:1px solid #00b89444;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;background:#00b89412;color:#00b894}
.reset-btn:hover{background:#00b89430;box-shadow:0 0 16px #00b89430}
.reset-btn:disabled{opacity:.5;cursor:not-allowed}
.drift-locked-notice{background:#f39c1210;border:1px solid #f39c1230;border-radius:7px;padding:7px 10px;font-size:10.5px;color:#f39c12;margin-bottom:8px;display:none}
#toast{position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none}
.tmsg{padding:11px 16px;border-radius:10px;font-size:12.5px;font-weight:600;max-width:300px;box-shadow:0 4px 20px #0009;animation:tsi .2s ease;pointer-events:all}
.tmsg-ok{background:#00b89418;border:1px solid #00b89455;color:#00b894}
.tmsg-er{background:#d6303118;border:1px solid #d6303155;color:#d63031}
@keyframes tsi{from{transform:translateY(12px);opacity:0}to{transform:none;opacity:1}}
.mono{font-family:'DM Mono',monospace}
.badge-buy{background:#00b89422;color:#00b894;border:1px solid #00b89444;font-size:9px;padding:2px 7px;border-radius:100px;font-weight:700;white-space:nowrap}
.badge-sell{background:#d6303122;color:#d63031;border:1px solid #d6303144;font-size:9px;padding:2px 7px;border-radius:100px;font-weight:700;white-space:nowrap}
.spin{display:inline-block;width:11px;height:11px;border:2px solid #fff4;border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;vertical-align:middle}
@keyframes spin{to{transform:rotate(360deg)}}
.drift-badge{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:100px;font-size:10px;font-weight:700}
.drift-up{background:#00b89420;color:#00b894;border:1px solid #00b89444}
.drift-dn{background:#d6303120;color:#d63031;border:1px solid #d6303144}
.drift-off{background:var(--surface);color:var(--muted);border:1px solid var(--border2)}
.lev-badge{background:#627EEA20;color:#627EEA;border:1px solid #627EEA44;font-size:9px;padding:2px 6px;border-radius:5px;font-weight:700;white-space:nowrap}
.pnl-pos{color:#00b894;font-weight:700;font-family:'DM Mono',monospace}
.pnl-neg{color:#d63031;font-weight:700;font-family:'DM Mono',monospace}
</style>

{{-- Coin tabs --}}
<div class="ctabs">
@foreach(['BTC','ETH'] as $c)
@php $m=$c==='BTC'?$btcMkt:$ethMkt; $mt=$coinMeta[$c]; $ch=(float)$m->change_pct; @endphp
<a href="{{ route('admin.trade',['coin'=>$c]) }}" class="ctab ctab-{{ strtolower($c) }} {{ $coin===$c?'on':'' }}">
  <span style="font-size:15px">{{ $mt['icon'] }}</span>
  <span>{{ $c }}/USDT</span>
  <span style="font-size:10px;color:{{ $ch>=0 ? '#00b894' : '#d63031' }}">
    $<span id="sw-{{ $c }}">{{ number_format((float)$m->price,2) }}</span>
    {{ $ch>=0?'+':'' }}{{ number_format($ch,2) }}%
  </span>
</a>
@endforeach
</div>

<div class="apg">
<div>

  @php $chg=(float)$market->change_pct; $cmt=$coinMeta[$coin]; @endphp
  <div class="tkr">
    <div class="ts"><span class="tl">Price</span><span class="tv" id="t-price" style="font-size:18px;color:{{ $cmt['color'] }}">${{ number_format((float)$market->price,2) }}</span></div>
    <div class="ts"><span class="tl">24h Change</span><span class="tv" id="t-chg" style="color:{{ $chg>=0?'#00b894':'#d63031' }}">{{ $chg>=0?'+':'' }}{{ number_format($chg,2) }}%</span></div>
    <div class="ts"><span class="tl">24h High</span><span class="tv" id="t-high" style="color:#00b894">${{ number_format((float)$market->high_24h,2) }}</span></div>
    <div class="ts"><span class="tl">24h Low</span><span class="tv" id="t-low" style="color:#d63031">${{ number_format((float)$market->low_24h,2) }}</span></div>
    <div class="ts">
      <span class="tl">Drift</span>
      @php $driftClass = $market->drift_enabled ? ($market->drift_direction==='up' ? 'drift-up' : 'drift-dn') : 'drift-off'; @endphp
      <span id="drift-badge" class="drift-badge {{ $driftClass }}">
        {{ $market->drift_enabled ? strtoupper($market->drift_direction).' '.$market->drift_pct.'%' : 'OFF' }}
      </span>
    </div>
    <div class="ts">
      <span class="tl">Mode</span>
      @php $liveMode = \Schema::hasColumn('market_index','live_mode') ? (bool)$market->live_mode : false; @endphp
      <span id="t-mode" class="{{ $liveMode ? 'live-badge' : '' }}" style="{{ $liveMode ? '' : 'font-size:10px;font-weight:700;color:var(--muted)' }}">
        @if($liveMode)🟢 LIVE <span class="live-dot"></span>@else ⚙ Manual @endif
      </span>
    </div>
    <div class="ts">
      <span class="tl">Status</span>
      <span id="t-status" class="badge {{ $market->trading_enabled?'badge-confirmed':'badge-rejected' }}" style="font-size:10px">{{ $market->trading_enabled?'Live':'Paused' }}</span>
    </div>
    <div class="ts" style="margin-left:auto">
      <span class="tl">Pending</span>
      <span class="tv"><span id="pb-cnt" style="color:#00b894">0</span>B / <span id="ps-cnt" style="color:#d63031">0</span>S</span>
    </div>
  </div>

  {{-- Chart --}}
  <div class="cbox">
    <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border-bottom:1px solid #0d2035;flex-wrap:wrap">
      <span style="font-size:12px;font-weight:700;color:var(--muted);flex:1">{{ $coin }}/USDT &middot; Candlesticks</span>
      <div style="display:flex;gap:4px">
        @foreach(['1'=>'1m','5'=>'5m','15'=>'15m','60'=>'1h'] as $iv=>$lb)
        <button class="ctb {{ $iv==='1'?'on':'' }}" onclick="switchIv({{ $iv }},this)">{{ $lb }}</button>
        @endforeach
      </div>
      <div style="display:flex;gap:4px">
        <button class="indb" id="iMA" onclick="togInd('MA')">MA</button>
        <button class="indb" id="iBB" onclick="togInd('BB')">BB</button>
        <button class="indb on" id="iRSI" onclick="togInd('RSI')">RSI</button>
        <button class="indb" id="iVOL" onclick="togInd('VOL')">VOL</button>
      </div>
    </div>
    <div id="admChart"></div>
    <div class="rsipane2" id="rsiWrap2"><div id="admRsi"></div></div>
  </div>

  {{-- Live Order Book --}}
  <div class="ob-wrap">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;border-bottom:1px solid var(--border2)">
      <span style="font-size:11px;font-weight:700;color:var(--muted)">Live Order Book</span>
      <span style="font-size:9px;color:var(--dim)" id="ob-mode-label">loading...</span>
    </div>
    <div class="ob-cols">
      <div class="ob-col asks">
        <div class="ob-col-head" style="color:#d63031"><span>Ask Price</span><span>Amount</span><span>Total</span></div>
        <div id="ob-asks"></div>
      </div>
      <div class="ob-col bids">
        <div class="ob-col-head" style="color:#00b894"><span>Bid Price</span><span>Amount</span><span>Total</span></div>
        <div id="ob-bids"></div>
      </div>
    </div>
    <div class="ob-spread" id="ob-spread">Spread: —</div>
  </div>

  {{-- Market Trades --}}
  <div class="mt-wrap">
    <div class="mt-hd">Market Trades</div>
    <div class="mt-list" id="mt-list">
      <div style="text-align:center;padding:16px;color:var(--muted);font-size:11px">No trades yet</div>
    </div>
  </div>

  {{-- Pending orders --}}
  <div class="card" style="margin-bottom:10px">
    <div class="otabs">
      <button class="otb bon" id="otb-b" onclick="showOTab('b')">🟢 Buys <span class="ocnt" id="bc">0</span></button>
      <button class="otb" id="otb-s" onclick="showOTab('s')">🔴 Sells <span class="ocnt" id="sc">0</span></button>
    </div>
    <div id="panel-b"><div style="padding:14px;text-align:center;color:var(--muted);font-size:12px">No pending buys</div></div>
    <div id="panel-s" style="display:none"><div style="padding:14px;text-align:center;color:var(--muted);font-size:12px">No pending sells</div></div>
  </div>

  {{-- Limit orders --}}
  <div class="card" style="margin-bottom:10px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
      <span style="font-size:12px;font-weight:700">&#128204; Open Limit Orders</span>
      <span id="lo-cnt" class="badge badge-pending" style="font-size:10px">0</span>
    </div>
    <div id="limit-orders"><div style="padding:12px;text-align:center;color:var(--muted);font-size:12px">No open limit orders</div></div>
  </div>

  {{-- Leverage Positions (admin view all) --}}
  <div class="ob-wrap" id="lev-pos-wrap" style="margin-bottom:10px">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;border-bottom:1px solid var(--border2)">
      <span style="font-size:11px;font-weight:700;color:var(--muted)">&#128293; Open Leverage Positions</span>
      <span id="adm-pos-count" style="background:#627EEA22;color:#627EEA;border:1px solid #627EEA44;font-size:10px;padding:1px 8px;border-radius:100px;font-weight:700">0 open</span>
    </div>
    <div id="adm-positions">
      <div style="text-align:center;padding:20px;color:var(--muted);font-size:12px">No open positions</div>
    </div>
  </div>

  {{-- Recent trades --}}
  <div class="card">
    <div style="font-size:12px;font-weight:700;margin-bottom:10px">Recent Trade History</div>
    <div id="recent-trades"><div style="padding:12px;text-align:center;color:var(--muted);font-size:12px">No trades yet</div></div>
  </div>

</div>
<div class="apg-right">

  {{-- Reset Market --}}
  <div class="rcard">
    <div class="rcard-hd">
      &#128279; Market Data Source
      <span id="live-status-badge" class="{{ $liveMode ? 'live-badge' : 'drift-badge drift-off' }}" style="font-size:9px">
        @if($liveMode)LIVE <span class="live-dot"></span>@else Manual @endif
      </span>
    </div>
    <div style="font-size:11px;color:var(--dim);margin-bottom:12px;line-height:1.7">
      Pressing <strong style="color:var(--text)">Reset Market</strong> fetches the real {{ $coin }}/USDT price from Binance, seeds 200 live candles, and links the chart to live data. Users see a seamless transition.
    </div>
    @php $seededAt = \Schema::hasColumn('market_index','live_seeded_at') ? $market->live_seeded_at : null; @endphp
    @if($seededAt)
    <div style="padding:7px 10px;background:#00b89410;border:1px solid #00b89430;border-radius:7px;margin-bottom:10px;font-size:10.5px;color:#00b894">
      &#10003; Linked to live data {{ $seededAt->diffForHumans() }}
    </div>
    @endif
    <button class="reset-btn" id="btn-reset" onclick="doResetMarket()">
      &#128260; Reset Market to Live Data
    </button>
  </div>

  {{-- Trading toggle --}}
  <div class="rcard">
    <div class="rcard-hd">&#9881; Trading Control</div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;background:#040f1c;border:1px solid var(--border2);border-radius:7px;margin-bottom:10px">
      <label style="font-size:12px;font-weight:600;color:var(--text);cursor:pointer" for="f-te">Trading Active</label>
      <input type="checkbox" id="f-te" {{ $market->trading_enabled?'checked':'' }} style="width:17px;height:17px;accent-color:var(--accent);cursor:pointer" onchange="toggleTrading()">
    </div>
    <div style="font-size:10.5px;color:var(--dim)">Disabling prevents users from placing orders. Does not affect drift or live data.</div>
  </div>

  {{-- Auto Drift --}}
  <div class="rcard">
    <div class="rcard-hd">
      &#129302; Auto Price Drift
      @php $dsClass = $market->drift_enabled ? ($market->drift_direction==='up' ? 'drift-up' : 'drift-dn') : 'drift-off'; @endphp
      <span id="drift-status" class="drift-badge {{ $dsClass }}" style="font-size:9px">{{ $market->drift_enabled?'ACTIVE':'OFF' }}</span>
    </div>
    <div style="font-size:11px;color:var(--dim);margin-bottom:10px;line-height:1.6">
      Moves price by % every N seconds. Settings are <strong style="color:#f39c12">locked while drift is active</strong> — stop drift first to change them.
    </div>

    <div class="drift-locked-notice" id="drift-locked-notice">
      &#9888; Stop drift first to change direction or percentage.
    </div>

    <div class="fg">
      <label class="fl">Direction</label>
      <div class="drpills" id="dr-pills">
        @foreach(['up'=>'&#9650; Up','down'=>'&#9660; Down','none'=>'&mdash; None'] as $dir=>$lbl)
        @php
          $isActive = $market->drift_direction===$dir;
          $pillClass = $isActive ? ($dir==='up' ? 'up-on' : ($dir==='down' ? 'dn-on' : 'no-on')) : '';
          $lockClass = $market->drift_enabled ? 'locked' : '';
        @endphp
        <label class="drpill {{ $pillClass }} {{ $lockClass }}" id="drp-{{ $dir }}" onclick="selDir('{{ $dir }}')">
          <input type="radio" name="_dr" value="{{ $dir }}" {{ $isActive?'checked':'' }}>{!! $lbl !!}
        </label>
        @endforeach
      </div>
    </div>
    <div class="frow fg">
      <div>
        <label class="fl">% Per Tick</label>
        <input type="number" id="f-dpct" class="fi" value="{{ number_format((float)$market->drift_pct,3,'.','') }}" step="0.001" min="0" max="50" placeholder="0.500" oninput="updDriftPrev()" {{ $market->drift_enabled?'disabled':'' }}>
      </div>
      <div>
        <label class="fl">Tick (seconds)</label>
        <input type="number" id="f-div" class="fi" value="{{ (int)$market->drift_interval }}" min="5" max="3600" placeholder="60" oninput="updDriftPrev()" {{ $market->drift_enabled?'disabled':'' }}>
      </div>
    </div>
    <div id="drift-prev" style="padding:7px 10px;background:#040f1c;border:1px solid var(--border2);border-radius:7px;margin-bottom:10px;font-size:11px;color:var(--dim)">
      @if($market->drift_enabled && (float)$market->drift_pct > 0)
        Price {{ $market->drift_direction==='up'?'rises':'falls' }}
        <strong style="color:{{ $market->drift_direction==='up'?'#00b894':'#d63031' }}">{{ $market->drift_pct }}%</strong>
        every <strong style="color:var(--text)">{{ $market->drift_interval }}s</strong>
      @else
        Set direction + % to preview
      @endif
    </div>
    @php $driftBtnStyle = $market->drift_enabled ? 'background:#d6303120;color:#d63031;border:1px solid #d6303144' : ''; @endphp
    <button class="btn" style="width:100%;font-size:13px;font-weight:700;{{ $driftBtnStyle }}" id="btn-drift" onclick="submitDrift()">
      {{ $market->drift_enabled ? '&#9209; Stop Drift' : '&#9654; Start Drift' }}
    </button>
  </div>

  {{-- Bulk Limit Orders --}}
  <div class="rcard">
    <div class="rcard-hd">&#128204; Create Bulk Limit Orders</div>
    <div style="font-size:11px;color:var(--dim);margin-bottom:10px;line-height:1.6">
      Auto-fills when price crosses trigger. Select multiple users.
    </div>
    <input type="hidden" id="ob-coin" value="{{ $coin }}">
    <div class="sidepills" id="side-pills">
      <label class="sidepill buy-on" id="sp-buy" onclick="selSide('buy')">
        <input type="radio" name="_side" value="buy" checked> 🟢 Buy
      </label>
      <label class="sidepill" id="sp-sell" onclick="selSide('sell')">
        <input type="radio" name="_side" value="sell"> 🔴 Sell
      </label>
    </div>
    <div class="frow fg">
      <div>
        <label class="fl">Trigger Price $</label>
        <input type="number" id="ob-tp" class="fi" step="0.01" min="0.01" placeholder="{{ number_format((float)$market->price,2,'.','') }}" oninput="calcOB()">
      </div>
      <div>
        <label class="fl">{{ $coin }} Amount</label>
        <input type="number" id="ob-amt" class="fi" step="0.00000001" min="0.00000001" placeholder="0.00" oninput="calcOB()">
      </div>
    </div>
    <div style="background:#040f1c;border:1px solid var(--border2);border-radius:7px;padding:7px 10px;margin-bottom:8px;font-size:11px">
      <div style="display:flex;justify-content:space-between"><span style="color:var(--dim)">USDT value</span><span class="mono" id="ob-prev" style="font-weight:700">$0.00</span></div>
    </div>
    <div class="fg">
      <label class="fl" style="display:flex;align-items:center;justify-content:space-between">
        Select Users
        <button onclick="toggleAllUsers()" style="font-size:9.5px;padding:1px 7px;border-radius:4px;border:1px solid var(--border2);background:transparent;color:var(--muted);cursor:pointer;font-family:'DM Sans',sans-serif">All</button>
      </label>
      <div class="user-list" id="user-list">
        @foreach($users as $u)
        <label class="user-chk">
          <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" class="uid-chk">
          <span style="flex:1">{{ $u->name }}</span>
          <span style="color:var(--dim);font-size:10px">{{ $u->email }}</span>
        </label>
        @endforeach
        @if($users->isEmpty())
        <div style="padding:12px;text-align:center;color:var(--muted);font-size:11px">No users found</div>
        @endif
      </div>
    </div>
    <button class="btn" style="width:100%;font-size:13px;font-weight:700" id="btn-ob" onclick="submitOrderBook()">&#128204; Create Orders</button>
  </div>

  {{-- Stats --}}
  <div class="rcard">
    <div class="rcard-hd">&#128202; Stats</div>
    <div class="stats-grid">
      <div class="stat-box stat-buy">
        <div style="font-size:9px;color:var(--dim);margin-bottom:3px">PENDING BUYS</div>
        <div class="mono" style="font-size:18px;font-weight:700;color:#00b894" id="stat-bc">0</div>
        <div style="font-size:10px;color:var(--muted)" id="stat-bu">$0.00</div>
      </div>
      <div class="stat-box stat-sell">
        <div style="font-size:9px;color:var(--dim);margin-bottom:3px">PENDING SELLS</div>
        <div class="mono" style="font-size:18px;font-weight:700;color:#d63031" id="stat-sc">0</div>
        <div style="font-size:10px;color:var(--muted)" id="stat-su">0 {{ $coin }}</div>
      </div>
    </div>
  </div>

</div>
</div>

<div id="toast"></div>

<script src="https://unpkg.com/lightweight-charts@4.1.0/dist/lightweight-charts.standalone.production.js"></script>
<script>
var COIN   = '{{ $coin }}';
var PRICE  = {{ (float)$market->price }};
var HIGH   = {{ (float)$market->high_24h }};
var LOW    = {{ (float)$market->low_24h }};
var CSRF   = document.querySelector('meta[name=csrf-token]').content;
var APIU   = '{{ route("admin.trade.api","_") }}'.replace('_', COIN);
var RESETU = '{{ route("admin.trade.reset") }}';
var MKTURL = '{{ route("admin.trade.market") }}';
var OBOOK  = '{{ route("admin.trade.order-book") }}';
var IV     = 1;
var _selDir  = '{{ $market->drift_direction }}';
var _selSide = 'buy';
var _driftOn = {{ $market->drift_enabled ? 'true' : 'false' }};

// ── Chart ─────────────────────────────────────────────────────────
var el = document.getElementById('admChart');
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
new ResizeObserver(function(){
  chart.applyOptions({width:el.clientWidth});
  if(rsiC) rsiC.applyOptions({width:document.getElementById('admRsi').clientWidth});
}).observe(el);

var indOn={MA:false,BB:false,RSI:true,VOL:false};
var ser={ma:null,bbH:null,bbL:null};
var rsiC=null, rsiSr=null, allC=[], pLines={};

function _ma(d,p){return d.map(function(c,i){if(i<p-1)return null;var s=0;for(var j=i-p+1;j<=i;j++)s+=d[j].close;return{time:c.time,value:s/p};}).filter(Boolean);}
function _bb(d,p,m){p=p||20;m=m||2;return d.map(function(c,i){if(i<p-1)return null;var sl=d.slice(i-p+1,i+1),mn=sl.reduce(function(a,b){return a+b.close;},0)/p,sd=Math.sqrt(sl.reduce(function(a,b){return a+(b.close-mn)*(b.close-mn);},0)/p);return{time:c.time,hi:mn+m*sd,lo:mn-m*sd};}).filter(Boolean);}
function _rsi(d,p){p=p||14;var r=[],g=[],l=[];for(var i=1;i<d.length;i++){var x=d[i].close-d[i-1].close;g.push(x>0?x:0);l.push(x<0?-x:0);if(i>=p){var ag=g.slice(-p).reduce(function(a,b){return a+b;},0)/p,al=l.slice(-p).reduce(function(a,b){return a+b;},0)/p;r.push({time:d[i].time,value:al===0?100:parseFloat((100-100/(1+ag/al)).toFixed(2))});}}return r;}

function applyInds(){
  if(allC.length<2) return;
  if(indOn.MA){if(!ser.ma)ser.ma=chart.addLineSeries({color:'#f1c40f',lineWidth:1.5,priceLineVisible:false});ser.ma.setData(_ma(allC,20));}
  else if(ser.ma){chart.removeSeries(ser.ma);ser.ma=null;}
  if(indOn.BB){var b=_bb(allC);if(!ser.bbH){ser.bbH=chart.addLineSeries({color:'#627EEA55',lineWidth:1,priceLineVisible:false});ser.bbL=chart.addLineSeries({color:'#627EEA55',lineWidth:1,priceLineVisible:false});}ser.bbH.setData(b.map(function(x){return{time:x.time,value:x.hi};}));ser.bbL.setData(b.map(function(x){return{time:x.time,value:x.lo};}));}
  else if(ser.bbH){chart.removeSeries(ser.bbH);chart.removeSeries(ser.bbL);ser.bbH=null;ser.bbL=null;}
  var rw=document.getElementById('rsiWrap2');
  if(indOn.RSI){
    rw.style.display='block';
    if(!rsiC){var re=document.getElementById('admRsi');rsiC=LightweightCharts.createChart(re,{layout:{background:{color:'#070f1a'},textColor:'#4a7a9b'},grid:{vertLines:{color:'#0d203515'},horzLines:{color:'#0d203520'}},rightPriceScale:{borderColor:'#0d2035',visible:true,scaleMargins:{top:0.05,bottom:0.05}},timeScale:{borderColor:'#0d2035',visible:false},width:re.clientWidth,height:60});rsiSr=rsiC.addLineSeries({color:'#a855f7',lineWidth:1.5,priceLineVisible:false});chart.timeScale().subscribeVisibleLogicalRangeChange(function(r){if(r)rsiC.timeScale().setVisibleLogicalRange(r);});rsiC.timeScale().subscribeVisibleLogicalRangeChange(function(r){if(r)chart.timeScale().setVisibleLogicalRange(r);});}
    rsiSr.setData(_rsi(allC));
  } else {rw.style.display='none';if(rsiC){rsiC.remove();rsiC=null;rsiSr=null;}}
}
function togInd(k){indOn[k]=!indOn[k];document.getElementById('i'+k).classList.toggle('on',indOn[k]);applyInds();}
function switchIv(iv,btn){IV=iv;document.querySelectorAll('.ctb').forEach(function(b){b.classList.remove('on');});btn.classList.add('on');fetchState();}

function drawPriceLines(buys,sells){
  Object.values(pLines).forEach(function(l){try{cs.removePriceLine(l);}catch(e){}});
  pLines={};
  (buys||[]).forEach(function(t){pLines['b'+t.id]=cs.createPriceLine({price:t.price,color:'#00b89488',lineWidth:1,lineStyle:LightweightCharts.LineStyle.Dotted,title:'BUY '+t.coin_amount.toFixed(4),axisLabelVisible:true});});
  (sells||[]).forEach(function(t){pLines['s'+t.id]=cs.createPriceLine({price:t.price,color:'#d6303188',lineWidth:1,lineStyle:LightweightCharts.LineStyle.Dotted,title:'SELL '+t.coin_amount.toFixed(4),axisLabelVisible:true});});
}

// ── Fetch state ───────────────────────────────────────────────────
function fetchState(){
  fetch(APIU+'?interval='+IV)
    .then(function(r){if(!r.ok)throw new Error('HTTP '+r.status);return r.json();})
    .then(function(d){
      applyMarket(d.market);
      applyCandles(d.candles);
      renderPending(d.all_buys, d.all_sells);
      drawPriceLines(d.all_buys, d.all_sells);
      renderLimitOrders(d.limit_orders);
      renderRecentTrades(d.my_history);
      applyOrderBook(d.order_book, d.market);
      applyMarketTrades(d.recent_fills);
      renderAdminPositions(d.leverage_positions||[]);
    })
    .catch(function(e){console.warn('Admin poll err:',e);});
}

function applyCandles(candles){
  if(!candles||candles.length===0) return;
  var seen=new Set(), clean=[];
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
  var ce=document.getElementById('t-chg'); ce.textContent=(m.change_pct>=0?'+':'')+m.change_pct.toFixed(2)+'%'; ce.style.color=m.change_pct>=0?'#00b894':'#d63031';
  document.getElementById('t-high').textContent='$'+fp(HIGH);
  document.getElementById('t-low').textContent='$'+fp(LOW);
  var dc=m.drift_enabled?(m.drift_direction==='up'?'drift-up':'drift-dn'):'drift-off';
  var dt=m.drift_enabled?(m.drift_direction.toUpperCase()+' '+m.drift_pct+'%'):'OFF';
  ['drift-badge','drift-status'].forEach(function(id){var b=document.getElementById(id);if(b){b.textContent=dt;b.className='drift-badge '+dc;}});
  var mb=document.getElementById('t-mode'), lb=document.getElementById('live-status-badge');
  if(mb){if(m.live_mode){mb.className='live-badge';mb.innerHTML='🟢 LIVE <span class="live-dot"></span>';}else{mb.className='';mb.style.cssText='font-size:10px;font-weight:700;color:var(--muted)';mb.textContent='⚙ Manual';}}
  if(lb){if(m.live_mode){lb.className='live-badge';lb.innerHTML='LIVE <span class="live-dot"></span>';}else{lb.className='drift-badge drift-off';lb.textContent='Manual';}}
  _driftOn=m.drift_enabled;
  setDriftLock(m.drift_enabled);
  var ts=document.getElementById('t-status');if(ts){ts.textContent=m.trading_enabled?'Live':'Paused';ts.className='badge '+(m.trading_enabled?'badge-confirmed':'badge-rejected');ts.style.fontSize='10px';}
  document.getElementById('sw-'+COIN).textContent=fp(PRICE);
}

function applyOrderBook(ob, mkt){
  if(!ob) return;
  var asksEl=document.getElementById('ob-asks'), bidsEl=document.getElementById('ob-bids');
  if(!asksEl||!bidsEl) return;
  var maxSum=ob.max_sum||1;
  var lbl=document.getElementById('ob-mode-label');
  if(lbl) lbl.textContent=mkt&&mkt.drift_enabled?'🤖 Drift-driven':(mkt&&mkt.live_mode?'🟢 Live Binance':'⚙ Manual');
  var asksHtml=[].concat(ob.asks).reverse().map(function(r){return '<div class="ob-row"><div class="ob-bar ask" style="width:'+Math.min((r.sum/maxSum)*100,100).toFixed(1)+'%"></div><span style="color:#d63031;position:relative">'+r.price.toFixed(2)+'</span><span style="color:var(--muted);position:relative">'+r.amount.toFixed(4)+'</span><span style="color:var(--dim);position:relative">'+r.total.toFixed(2)+'</span></div>';}).join('');
  var bidsHtml=ob.bids.map(function(r){return '<div class="ob-row"><div class="ob-bar bid" style="width:'+Math.min((r.sum/maxSum)*100,100).toFixed(1)+'%"></div><span style="color:#00b894;position:relative">'+r.price.toFixed(2)+'</span><span style="color:var(--muted);position:relative">'+r.amount.toFixed(4)+'</span><span style="color:var(--dim);position:relative">'+r.total.toFixed(2)+'</span></div>';}).join('');
  asksEl.innerHTML=asksHtml; bidsEl.innerHTML=bidsHtml;
  var sp=document.getElementById('ob-spread');if(sp)sp.textContent='Spread: $'+(ob.spread||0).toFixed(2);
}

function applyMarketTrades(fills){
  if(!fills||!fills.length) return;
  var el=document.getElementById('mt-list'); if(!el) return;
  el.innerHTML=fills.map(function(f){return '<div class="mt-row"><span style="color:'+(f.side==='buy'?'#00b894':'#d63031')+';font-weight:700">'+f.price.toFixed(2)+'</span><span style="color:var(--muted)">'+f.coin_amount.toFixed(4)+'</span><span style="color:var(--dim);font-size:9px">'+new Date(f.at*1000).toLocaleTimeString()+'</span></div>';}).join('');
}

function renderPending(buys,sells){
  buys=buys||[]; sells=sells||[];
  document.getElementById('bc').textContent=buys.length;
  document.getElementById('sc').textContent=sells.length;
  document.getElementById('pb-cnt').textContent=buys.length;
  document.getElementById('ps-cnt').textContent=sells.length;
  document.getElementById('stat-bc').textContent=buys.length;
  document.getElementById('stat-sc').textContent=sells.length;
  var bu=buys.reduce(function(s,t){return s+t.usdt_amount;},0);
  var sco=sells.reduce(function(s,t){return s+t.coin_amount;},0);
  document.getElementById('stat-bu').textContent='$'+bu.toFixed(2);
  document.getElementById('stat-su').textContent=sco.toFixed(6)+' '+COIN;
  var bp=document.getElementById('panel-b');
  bp.innerHTML=buys.length?buys.map(function(t){return '<div class="ord-row"><div class="uinf"><span class="un">'+t.user+'</span></div><div class="ovals"><span class="ov" style="color:#00b894">+'+t.coin_amount.toFixed(6)+' '+COIN+'</span><span class="os">$'+t.usdt_amount.toFixed(2)+' @ $'+t.price.toFixed(2)+'</span></div><div style="display:flex;gap:5px"><button class="actbtn ab-fill-buy" onclick="fillOrder('+t.id+',\'buy\',this)">Fill</button><button class="actbtn ab-cancel" onclick="cancelOrder('+t.id+',this)">X</button></div></div>';}).join(''):'<div style="padding:14px;text-align:center;color:var(--muted);font-size:12px">No pending buys</div>';
  var sp=document.getElementById('panel-s');
  sp.innerHTML=sells.length?sells.map(function(t){return '<div class="ord-row"><div class="uinf"><span class="un">'+t.user+'</span></div><div class="ovals"><span class="ov" style="color:#d63031">-'+t.coin_amount.toFixed(6)+' '+COIN+'</span><span class="os">$'+t.usdt_amount.toFixed(2)+' @ $'+t.price.toFixed(2)+'</span></div><div style="display:flex;gap:5px"><button class="actbtn ab-fill-sell" onclick="fillOrder('+t.id+',\'sell\',this)">Fill</button><button class="actbtn ab-cancel" onclick="cancelOrder('+t.id+',this)">X</button></div></div>';}).join(''):'<div style="padding:14px;text-align:center;color:var(--muted);font-size:12px">No pending sells</div>';
}

function renderLimitOrders(orders){
  var cnt=document.getElementById('lo-cnt'); if(cnt) cnt.textContent=(orders||[]).length;
  var el=document.getElementById('limit-orders'); if(!el) return;
  if(!orders||orders.length===0){el.innerHTML='<div style="padding:12px;text-align:center;color:var(--muted);font-size:12px">No open limit orders</div>';return;}
  el.innerHTML='<table class="lotbl"><thead><tr><th>Side</th><th>User</th><th>Trigger</th><th>'+COIN+'</th><th>USDT</th><th></th></tr></thead><tbody>'+orders.map(function(o){return '<tr><td><span class="'+(o.side==='buy'?'badge-buy':'badge-sell')+'">'+o.side.toUpperCase()+'</span></td><td style="color:var(--text)">'+o.user+'</td><td class="mono" style="color:'+(o.side==='buy'?'#00b894':'#d63031')+'">$'+o.trigger_price.toFixed(2)+'</td><td class="mono">'+o.coin_amount.toFixed(6)+'</td><td class="mono">$'+o.usdt_amount.toFixed(2)+'</td><td><button class="actbtn ab-cancel" onclick="cancelLimitOrder('+o.id+',this)">X</button></td></tr>';}).join('')+'</tbody></table>';
}

function renderRecentTrades(trades){
  var el=document.getElementById('recent-trades'); if(!el||!trades) return;
  if(trades.length===0){el.innerHTML='<div style="padding:12px;text-align:center;color:var(--muted);font-size:12px">No trades yet</div>';return;}
  el.innerHTML=trades.map(function(t){return '<div class="ord-row"><span class="'+(t.side==='buy'?'badge-buy':'badge-sell')+'">'+t.side.toUpperCase()+'</span><div class="uinf" style="flex:1"><span class="mono" style="color:var(--text)">'+t.coin_amount.toFixed(6)+' '+COIN+'</span><span style="font-size:10px;color:var(--dim)">'+(t.user?t.user+' &middot; ':'')+t.at+'</span></div><span class="mono" style="color:var(--muted);font-size:11px">@ $'+t.price.toFixed(2)+'</span><span style="font-size:9px;padding:2px 6px;border-radius:5px;font-weight:700;background:'+(t.status==='filled'?'#00b89422':'#d6303122')+';color:'+(t.status==='filled'?'#00b894':'#d63031')+'">'+t.status+'</span></div>';}).join('');
}

// ── Admin actions ─────────────────────────────────────────────────
function doResetMarket(){
  var btn=document.getElementById('btn-reset');
  btn.disabled=true; btn.innerHTML='<span class="spin"></span> Syncing with Binance...';
  post(RESETU, {coin:COIN})
    .then(function(d){
      toast(d.success||'Market reset to live', 'ok');
      fetchState();
    })
    .catch(function(e){toast(e.message||'Could not connect to Binance','er');})
    .finally(function(){btn.disabled=false;btn.innerHTML='&#128260; Reset Market to Live Data';});
}

function toggleTrading(){
  var enabled=document.getElementById('f-te').checked;
  post(MKTURL,{coin:COIN,trading_enabled:enabled?1:0,drift_enabled:_driftOn?1:0,drift_direction:_selDir,drift_pct:parseFloat(document.getElementById('f-dpct').value)||0,drift_interval:parseInt(document.getElementById('f-div').value)||60})
    .then(function(d){toast('Trading '+(enabled?'enabled':'disabled'),'ok');fetchState();})
    .catch(function(e){toast(e.message||'Error','er');});
}

function submitDrift(){
  var enabled=!_driftOn;
  if(enabled && (!parseFloat(document.getElementById('f-dpct').value)||_selDir==='none')){toast('Set direction and % before starting drift','er');return;}
  var btn=document.getElementById('btn-drift');
  btn.disabled=true; btn.innerHTML='<span class="spin"></span>';
  var body={coin:COIN,trading_enabled:document.getElementById('f-te').checked?1:0,drift_enabled:enabled?1:0,drift_direction:_selDir,drift_pct:parseFloat(document.getElementById('f-dpct').value)||0,drift_interval:parseInt(document.getElementById('f-div').value)||60};
  post(MKTURL, body)
    .then(function(d){
      _driftOn=enabled;
      toast(d.success||'Drift '+(enabled?'started':'stopped'),'ok');
      btn.textContent=enabled?'Stop Drift':'Start Drift';
      btn.style.background=enabled?'#d6303120':'';
      btn.style.color=enabled?'#d63031':'';
      btn.style.border=enabled?'1px solid #d6303144':'';
      setDriftLock(enabled);
      fetchState();
    })
    .catch(function(e){toast(e.message||'Error','er');})
    .finally(function(){btn.disabled=false;});
}

function setDriftLock(locked){
  var notice=document.getElementById('drift-locked-notice');
  var pct=document.getElementById('f-dpct'), div=document.getElementById('f-div');
  if(notice) notice.style.display=locked?'block':'none';
  if(pct) pct.disabled=locked;
  if(div) div.disabled=locked;
  document.querySelectorAll('.drpill').forEach(function(p){locked?p.classList.add('locked'):p.classList.remove('locked');});
}

function submitOrderBook(){
  var uids=[].slice.call(document.querySelectorAll('.uid-chk:checked')).map(function(c){return c.value;});
  if(!uids.length){toast('Select at least one user','er');return;}
  var tp=parseFloat(document.getElementById('ob-tp').value)||0;
  var ca=parseFloat(document.getElementById('ob-amt').value)||0;
  if(!tp||!ca){toast('Enter price and amount','er');return;}
  var btn=document.getElementById('btn-ob');
  btn.disabled=true; btn.innerHTML='<span class="spin"></span>';
  post(OBOOK,{coin:COIN,side:_selSide,trigger_price:tp,coin_amount:ca,user_ids:uids})
    .then(function(d){toast(d.success,'ok');document.querySelectorAll('.uid-chk').forEach(function(c){c.checked=false;});fetchState();})
    .catch(function(e){toast(e.message||'Error','er');})
    .finally(function(){btn.disabled=false;btn.textContent='Create Orders';});
}

function fillOrder(id,side,btn){
  btn.disabled=true; btn.innerHTML='<span class="spin"></span>';
  post('/admin/trade/'+id+'/fill-'+side,{})
    .then(function(d){toast(d.success,'ok');fetchState();})
    .catch(function(e){toast(e.message||'Error','er');})
    .finally(function(){btn.disabled=false;btn.textContent='Fill';});
}
function cancelOrder(id,btn){
  btn.disabled=true;
  post('/admin/trade/'+id+'/cancel',{})
    .then(function(d){toast(d.success,'ok');fetchState();})
    .catch(function(e){toast(e.message||'Error','er');})
    .finally(function(){btn.disabled=false;btn.textContent='X';});
}
function cancelLimitOrder(id,btn){
  btn.disabled=true;
  post('/admin/trade/order-book/'+id+'/cancel',{})
    .then(function(d){toast(d.success,'ok');fetchState();})
    .catch(function(e){toast(e.message||'Error','er');})
    .finally(function(){btn.disabled=false;btn.textContent='X';});
}

function showOTab(t){
  document.getElementById('panel-b').style.display=t==='b'?'block':'none';
  document.getElementById('panel-s').style.display=t==='s'?'block':'none';
  document.getElementById('otb-b').className='otb'+(t==='b'?' bon':'');
  document.getElementById('otb-s').className='otb'+(t==='s'?' son':'');
}
function selDir(d){
  if(_driftOn) return;
  _selDir=d;
  ['up','down','none'].forEach(function(x){
    var lb=document.getElementById('drp-'+x); if(!lb) return;
    var cls='drpill';if(x===d){cls+=(d==='up'?' up-on':d==='down'?' dn-on':' no-on');}
    lb.className=cls;
    lb.querySelector('input').checked=(x===d);
  });
  updDriftPrev();
}
function updDriftPrev(){
  var pct=parseFloat(document.getElementById('f-dpct').value)||0;
  var sec=parseInt(document.getElementById('f-div').value)||60;
  var el=document.getElementById('drift-prev');
  if(pct>0&&_selDir!=='none'){el.innerHTML='Price <strong style="color:'+(_selDir==='up'?'#00b894':'#d63031')+'">'+(_selDir==='up'?'rises':'falls')+' '+pct+'%</strong> every <strong style="color:var(--text)">'+sec+'s</strong>';}
  else{el.textContent='Set direction + % to preview';}
}
function selSide(s){
  _selSide=s;
  ['buy','sell'].forEach(function(x){
    var lb=document.getElementById('sp-'+x); if(!lb) return;
    lb.className='sidepill'+(x===s?(s==='buy'?' buy-on':' sell-on'):'');
    lb.querySelector('input').checked=(x===s);
  });
}
function toggleAllUsers(){
  var chks=document.querySelectorAll('.uid-chk');
  var allOn=[].every.call(chks,function(c){return c.checked;});
  chks.forEach(function(c){c.checked=!allOn;});
}
function calcOB(){
  var p=parseFloat(document.getElementById('ob-tp').value)||0;
  var a=parseFloat(document.getElementById('ob-amt').value)||0;
  document.getElementById('ob-prev').textContent='$'+(p*a).toFixed(2);
}
function fp(p){return p>=1000?p.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}):p.toFixed(p<1?6:2);}
function post(url,data){
  return fetch(url,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},body:JSON.stringify(data)})
    .then(function(r){return r.json().then(function(j){if(!r.ok)throw new Error(j.error||j.message||'Request failed');return j;});});
}
function toast(msg,t){
  t=t||'ok';
  var el=document.createElement('div'); el.className='tmsg tmsg-'+t; el.textContent=msg;
  document.getElementById('toast').appendChild(el);
  setTimeout(function(){el.remove();},4000);
}

selDir(_selDir); selSide('buy'); setDriftLock(_driftOn);
fetchState();
setInterval(fetchState, 4000);

function renderAdminPositions(positions){
  var el=document.getElementById('adm-positions');
  var cnt=document.getElementById('adm-pos-count');
  if(!el) return;
  if(!positions||positions.length===0){
    el.innerHTML='<div style="text-align:center;padding:20px;color:var(--muted);font-size:12px">No open positions</div>';
    cnt.textContent='0 open'; return;
  }
  cnt.textContent=positions.length+' open';
  var rows=positions.map(function(p){
    var pnlCls=p.pnl>=0?'pnl-pos':'pnl-neg';
    var pnlStr=(p.pnl>=0?'+':'')+'$'+Math.abs(p.pnl).toFixed(2);
    var pnlPctStr=(p.pnl_pct>=0?'+':'')+p.pnl_pct.toFixed(2)+'%';
    return '<tr>'+
      '<td style="color:var(--text);font-weight:600">'+p.user+'</td>'+
      '<td><span class="'+(p.direction==='long'?'badge-buy':'badge-sell')+'">'+(p.direction==='long'?'LONG':'SHORT')+'</span></td>'+
      '<td><span class="lev-badge">'+p.leverage+'x</span></td>'+
      '<td class="mono" style="color:var(--muted)">$'+p.margin.toFixed(2)+'</td>'+
      '<td class="mono" style="color:var(--muted)">$'+p.position_size.toFixed(2)+'</td>'+
      '<td class="mono" style="color:var(--muted)">$'+p.entry_price.toFixed(2)+'</td>'+
      '<td class="mono" style="color:#f39c12">$'+p.liq_price.toFixed(2)+'</td>'+
      '<td><span class="'+pnlCls+'">'+pnlStr+'</span> <span style="font-size:9.5px;color:var(--dim)">('+pnlPctStr+')</span></td>'+
      '<td style="font-size:10px;color:var(--dim)">'+p.opened_at+'</td>'+
      '</tr>';
  }).join('');
  el.innerHTML='<div style="overflow-x:auto"><table class="lotbl">'+
    '<thead><tr><th>User</th><th>Dir</th><th>Lev</th><th>Margin</th><th>Size</th><th>Entry</th><th>Liq Price</th><th>PnL</th><th>Opened</th></tr></thead>'+
    '<tbody>'+rows+'</tbody></table></div>';
}
</script>
@endsection