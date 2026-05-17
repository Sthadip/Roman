@extends('layouts.wallet')
@section('title','Withdraw — NEXUS')
@section('page-title','Withdraw Funds')
@section('content')

<div style="max-width:560px">
  @if(session('wd_error'))
  <div class="fz-inner er" style="margin-bottom:18px">{{ session('wd_error') }}</div>
  @endif

  <div class="card">
    <div style="font-size:18px;font-weight:700;margin-bottom:4px">Withdraw Funds</div>
    <div style="font-size:13.5px;color:var(--muted);margin-bottom:22px">
      Enter the amount in <strong style="color:#26A17B">USDT</strong> you want to withdraw.
      Choose which coin you want to receive — for BTC or ETH, the live conversion is shown below.
    </div>

    {{-- USDT balance --}}
    <div style="background:#26A17B0f;border:1px solid #26A17B33;border-radius:12px;padding:14px 16px;margin-bottom:22px;display:flex;align-items:center;justify-content:space-between">
      <div>
        <div style="font-size:11px;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:2px">Your USDT Balance</div>
        <div class="mono" style="font-size:22px;font-weight:800;color:#26A17B">${{ number_format($usdtBal, 2) }}</div>
      </div>
      <div style="font-size:28px;opacity:.4">$</div>
    </div>

    <form method="POST" action="{{ route('user.withdraw.store') }}">
      @csrf

      {{-- Step 1: Coin selector --}}
      <div class="fg">
        <label class="fl">① Select Destination Coin</label>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">
          @foreach($coinMeta as $code => $meta)
          <label style="cursor:pointer">
            <input type="radio" name="coin" value="{{ $code }}" style="display:none"
              {{ (old('coin','USDT')===$code)?'checked':'' }} onchange="onCoinChange('{{ $code }}')">
            <div class="coin-chip" data-coin="{{ $code }}"
              style="border:2px solid {{ old('coin','USDT')===$code ? $meta['color'] : 'var(--border2)' }};
                     background:{{ old('coin','USDT')===$code ? $meta['color'].'18' : '' }};
                     border-radius:12px;padding:14px 8px;text-align:center;transition:all .2s">
              <div style="font-size:24px;font-weight:800;color:{{ $meta['color'] }}">{{ $meta['icon'] }}</div>
              <div style="font-size:13px;font-weight:700;margin-top:3px">{{ $code }}</div>
              <div style="font-size:10.5px;color:var(--muted);margin-top:1px">{{ $meta['name'] }}</div>
            </div>
          </label>
          @endforeach
        </div>
      </div>

      {{-- Step 2: USDT amount (always) --}}
      <div class="fg" style="margin-top:4px">
        <label class="fl">② Amount to Withdraw (USDT)</label>
        <div style="position:relative">
          <span style="position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#26A17B;font-size:17px;font-weight:700;pointer-events:none">$</span>
          <input type="number" name="usdt_amount" id="usdt-input"
            class="fi @error('usdt_amount') fi-err @enderror"
            style="padding-left:32px"
            placeholder="0.00" step="0.01" min="1"
            max="{{ $usdtBal }}"
            value="{{ old('usdt_amount') }}"
            oninput="updateConversion()">
        </div>
        @error('usdt_amount')<div class="err">{{ $message }}</div>@enderror
        <div style="font-size:12px;color:var(--dim);margin-top:4px">
          Max: <a href="#" onclick="setMax(event)" style="color:var(--accent)">${{ number_format($usdtBal,2) }}</a>
        </div>
      </div>

      {{-- Conversion panel — always visible, updates based on coin --}}
      <div id="conv-panel" style="border-radius:12px;overflow:hidden;margin-bottom:18px">

        {{-- USDT selected: simple summary --}}
        <div id="conv-usdt" style="background:#040f1c;border:1px solid #26A17B33;border-radius:12px;padding:14px 16px">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <span style="font-size:13px;color:var(--muted)">You will receive</span>
            <span class="mono" style="font-size:18px;font-weight:800;color:#26A17B" id="recv-usd">$0.00 USDT</span>
          </div>
        </div>

        {{-- BTC/ETH selected: full breakdown --}}
        <div id="conv-crypto" style="display:none;background:#040f1c;border:1px solid var(--border2);border-radius:12px;padding:16px">
          <div style="font-size:11px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px">Live Conversion</div>

          <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
            {{-- From --}}
            <div style="flex:1;background:var(--surface);border:1px solid var(--border2);border-radius:10px;padding:10px 12px;text-align:center">
              <div style="font-size:11px;color:var(--dim);margin-bottom:3px">You pay</div>
              <div class="mono" style="font-size:16px;font-weight:800;color:#26A17B" id="conv-from-val">$0.00</div>
              <div style="font-size:11px;color:var(--muted)">USDT</div>
            </div>
            {{-- Arrow --}}
            <div style="font-size:20px;color:var(--dim)">→</div>
            {{-- To --}}
            <div style="flex:1;background:var(--surface);border:1px solid var(--border2);border-radius:10px;padding:10px 12px;text-align:center">
              <div style="font-size:11px;color:var(--dim);margin-bottom:3px">You receive</div>
              <div class="mono" style="font-size:16px;font-weight:800;color:var(--accent)" id="conv-to-val">0.00000000</div>
              <div style="font-size:11px;color:var(--muted)" id="conv-to-coin">BTC</div>
            </div>
          </div>

          <div style="display:flex;justify-content:space-between;padding:8px 0;border-top:1px solid var(--border)">
            <span style="font-size:12px;color:var(--dim)">Exchange rate</span>
            <span class="mono" style="font-size:12px;color:var(--muted)" id="conv-rate-lbl">—</span>
          </div>
          <div style="margin-top:10px;padding:8px 10px;background:#ffd6000a;border:1px solid #ffd60022;border-radius:8px;font-size:12px;color:var(--yellow)">
            ⚠ Indicative rate only. Final settlement at admin-confirmed rate.
          </div>
        </div>
      </div>

      {{-- Wallet address --}}
      <div class="fg">
        <label class="fl">③ Destination Wallet Address</label>
        <input type="text" name="wallet_address" id="wallet-addr"
          class="fi mono @error('wallet_address') fi-err @enderror"
          placeholder="Enter your wallet address" value="{{ old('wallet_address') }}">
        <div style="font-size:12px;color:var(--dim);margin-top:4px" id="addr-hint">
          Enter your USDT wallet address
        </div>
        @error('wallet_address')<div class="err">{{ $message }}</div>@enderror
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div class="fg">
          <label class="fl">Network <span style="color:var(--dim)">(optional)</span></label>
          <input type="text" name="network" id="network-input" class="fi"
            placeholder="e.g. TRC20" value="{{ old('network') }}">
        </div>
        <div class="fg">
          <label class="fl">Note <span style="color:var(--dim)">(optional)</span></label>
          <input type="text" name="note" class="fi" placeholder="Reference note" value="{{ old('note') }}">
        </div>
      </div>

      <div style="background:#ff52520a;border:1px solid #ff525222;border-radius:10px;padding:12px 14px;margin-bottom:18px;font-size:13px;color:var(--red)">
        ⚠ USDT will be locked from your balance until admin approves your withdrawal.
      </div>

      <button type="submit" class="btn bp" style="width:100%">Submit Withdrawal Request</button>
    </form>
  </div>

  <div style="margin-top:14px;text-align:center">
    <a href="{{ route('user.withdraw.history') }}" class="btn bg bsm">View Withdrawal History →</a>
  </div>
</div>

<script>
var usdtBal  = {{ $usdtBal }};
var liveRates= @json($liveRates); // { BTC: 67500, ETH: 3500, USDT: 1 }
var coinMeta = @json(collect($coinMeta)->map(function($m){ return ['color'=>$m['color'],'icon'=>$m['icon'],'name'=>$m['name']]; })->toArray());
var selCoin  = '{{ old('coin','USDT') }}';

var addrHints = {
  USDT: 'Enter your USDT wallet address',
  BTC:  'Enter your Bitcoin (BTC) wallet address',
  ETH:  'Enter your Ethereum (ETH) wallet address',
};
var networkDefaults = { USDT: 'TRC20', BTC: '', ETH: 'ERC20' };

function onCoinChange(coin) {
  selCoin = coin;
  // Chip styles
  document.querySelectorAll('.coin-chip').forEach(function(el) {
    var c = el.dataset.coin;
    if (c === coin) { el.style.borderColor = coinMeta[c].color; el.style.background = coinMeta[c].color+'18'; }
    else            { el.style.borderColor = 'var(--border2)'; el.style.background = ''; }
  });
  // Address hint & network default
  document.getElementById('addr-hint').textContent = addrHints[coin] || '';
  var netInput = document.getElementById('network-input');
  if (!netInput.value || Object.values(networkDefaults).includes(netInput.value)) {
    netInput.value = networkDefaults[coin] || '';
  }
  updateConversion();
}

function updateConversion() {
  var usdt = parseFloat(document.getElementById('usdt-input').value) || 0;

  if (selCoin === 'USDT') {
    document.getElementById('conv-usdt').style.display   = 'block';
    document.getElementById('conv-crypto').style.display = 'none';
    document.getElementById('recv-usd').textContent = '$' + usdt.toFixed(2) + ' USDT';
    return;
  }

  // Crypto conversion
  document.getElementById('conv-usdt').style.display   = 'none';
  document.getElementById('conv-crypto').style.display = 'block';

  var rate     = liveRates[selCoin] || 1;
  var coinAmt  = usdt > 0 ? (usdt / rate).toFixed(8) : '0.00000000';

  document.getElementById('conv-from-val').textContent = '$' + usdt.toFixed(2);
  document.getElementById('conv-to-val').textContent   = coinAmt;
  document.getElementById('conv-to-coin').textContent  = selCoin;
  document.getElementById('conv-rate-lbl').textContent = '1 '+selCoin+' = $'+rate.toLocaleString();
}

function setMax(e) {
  e.preventDefault();
  document.getElementById('usdt-input').value = usdtBal.toFixed(2);
  updateConversion();
}

// Chip click delegates
document.querySelectorAll('.coin-chip').forEach(function(el) {
  el.addEventListener('click', function() {
    var c = this.dataset.coin;
    var r = document.querySelector('input[name="coin"][value="'+c+'"]');
    if (r) { r.checked = true; onCoinChange(c); }
  });
});

// Init on load
onCoinChange(selCoin);
updateConversion();
</script>
@endsection
