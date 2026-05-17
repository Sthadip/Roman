@extends('layouts.wallet')
@section('title','Deposit — NEXUS')
@section('page-title','Deposit Crypto')
@section('content')
<style>
.steps{display:flex;gap:0;margin-bottom:28px;position:relative}
.steps::before{content:'';position:absolute;top:18px;left:0;right:0;height:2px;background:var(--border2);z-index:0}
.step{flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;position:relative;z-index:1}
.step-dot{width:36px;height:36px;border-radius:50%;border:2px solid var(--border2);background:var(--bg);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--muted);transition:all .3s}
.step-dot.done{background:var(--green);border-color:var(--green);color:#030a12}
.step-dot.active{background:var(--accent);border-color:var(--accent);color:#030a12}
.step-lbl{font-size:11px;color:var(--muted);font-weight:500;text-align:center}
.step-panel{display:none}.step-panel.active{display:block}
.net-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:24px}
.net-card{border:2px solid var(--border2);border-radius:14px;padding:22px 16px;text-align:center;cursor:pointer;transition:all .25s;background:var(--card)}
.net-card:hover{transform:translateY(-2px)}
.net-icon{width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:900;margin:0 auto 12px;border:1px solid transparent}
.net-name{font-size:13px;font-weight:700;margin-bottom:3px}
.net-ticker{font-size:12px;color:var(--muted)}
.upload-zone{border:2px dashed var(--border2);border-radius:10px;padding:24px;text-align:center;cursor:pointer;transition:border-color .2s}
.upload-zone:hover{border-color:var(--accent)}
.upload-zone.has-file{border-color:var(--green);background:#00e5a011}
</style>

<div class="card" style="max-width:640px;margin:0 auto">
  <div class="steps">
    <div class="step"><div class="step-dot active" id="dot1">1</div><div class="step-lbl">Network &amp; Amount</div></div>
    <div class="step"><div class="step-dot" id="dot2">2</div><div class="step-lbl">Submit Proof</div></div>
  </div>

  {{-- Step 1 --}}
  <div class="step-panel active" id="panel1">
    <div style="margin-bottom:22px">
      <div style="font-size:18px;font-weight:700;margin-bottom:4px">Deposit USDT</div>
      <div style="font-size:14px;color:var(--muted)">Select the network you are sending on, then enter the USDT amount</div>
    </div>

    <div style="font-size:12px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px">Sending Network</div>
    <div class="net-grid">
      <div class="net-card" id="card-BTC" onclick="selectNetwork('BTC')">
        <div class="net-icon" style="background:#F7931A22;border-color:#F7931A33;color:#F7931A">₿</div>
        <div class="net-name">Bitcoin</div>
        <div class="net-ticker">BTC Network</div>
      </div>
      <div class="net-card" id="card-ETH" onclick="selectNetwork('ETH')">
        <div class="net-icon" style="background:#627EEA22;border-color:#627EEA33;color:#627EEA">Ξ</div>
        <div class="net-name">Ethereum</div>
        <div class="net-ticker">ETH Network</div>
      </div>
    </div>
    <div id="net-err" class="err" style="display:none;margin-top:-14px;margin-bottom:14px">Please select a network.</div>

    <div style="background:#26A17B0a;border:1px solid #26A17B33;border-radius:10px;padding:12px 14px;margin-bottom:18px;font-size:13px;color:#26A17B">
      $ All deposits are recorded and credited in <strong>USDT</strong> regardless of the network used.
    </div>

    <div class="fg">
      <label class="fl">Amount (USDT) <span style="color:var(--red)">*</span></label>
      <div style="position:relative">
        <span style="position:absolute;left:13px;top:50%;transform:translateY(-50%);font-size:17px;font-weight:800;pointer-events:none;color:#26A17B">$</span>
        <input type="number" id="s1-amount" class="fi" style="padding-left:34px" placeholder="0.00" step="0.01" min="1">
      </div>
      <div id="amt-err" class="err" style="display:none">Please enter a valid amount (minimum $1.00 USDT).</div>
    </div>

    <button class="btn bp" style="width:100%;margin-top:4px" onclick="goStep2()">Continue →</button>
  </div>

  {{-- Step 2 --}}
  <div class="step-panel" id="panel2">
    <div style="margin-bottom:20px">
      <div style="font-size:18px;font-weight:700;margin-bottom:4px">Submit Payment Proof</div>
      <div style="font-size:14px;color:var(--muted)">Upload your transaction receipt for admin verification</div>
    </div>

    <form method="POST" action="{{ route('user.deposit.submit') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="network" id="f-network">
      <input type="hidden" name="amount"  id="f-amount">

      {{-- Summary --}}
      <div style="background:#26A17B0a;border:1px solid #26A17B33;border-radius:12px;padding:16px 20px;margin-bottom:20px">
        <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;font-weight:700">Deposit Summary</div>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
          <div>
            <div style="font-size:12px;color:var(--muted);margin-bottom:3px">You are depositing</div>
            <div style="display:flex;align-items:baseline;gap:5px">
              <span style="font-size:28px;font-weight:900;color:#26A17B">$</span>
              <span class="mono" style="font-size:28px;font-weight:900;color:#26A17B" id="s2-amount">0.00</span>
              <span style="font-size:14px;font-weight:700;color:#26A17B">USDT</span>
            </div>
          </div>
          <div style="text-align:right">
            <div style="font-size:12px;color:var(--muted);margin-bottom:3px">via network</div>
            <div style="display:flex;align-items:center;gap:6px;justify-content:flex-end">
              <div id="sum-net-icon" style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:900;background:#F7931A22;color:#F7931A">₿</div>
              <span id="sum-net-label" style="font-size:14px;font-weight:700;color:#F7931A">BTC</span>
            </div>
          </div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px">
        <div>
          <label class="fl">Screenshot <span style="color:var(--red)">*</span></label>
          <div class="upload-zone" id="ss-zone" onclick="document.getElementById('ss-input').click()">
            <div style="font-size:28px;margin-bottom:6px">📸</div>
            <div style="font-size:13px;color:var(--muted)" id="ss-name">Click to upload</div>
            <div style="font-size:11px;color:var(--dim);margin-top:4px">JPG, PNG, PDF · max 5MB</div>
          </div>
          <input type="file" name="screenshot" id="ss-input" accept=".jpg,.jpeg,.png,.pdf" style="display:none"
            onchange="fileSelected()">
          @error('screenshot')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="fl">Transaction ID / Hash <span style="color:var(--red)">*</span></label>
          <textarea name="transaction_id" class="fi mono @error('transaction_id') fi-err @enderror"
            placeholder="Paste transaction ID or hash" rows="5" style="resize:none">{{ old('transaction_id') }}</textarea>
          @error('transaction_id')<div class="err">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="fg">
        <label class="fl">Note <span style="color:var(--dim)">(optional)</span></label>
        <input type="text" name="note" class="fi" placeholder="Any additional information" value="{{ old('note') }}">
      </div>

      <div style="background:#00e5ff11;border:1px solid #00e5ff33;border-radius:10px;padding:12px 14px;margin-bottom:18px;font-size:13px;color:var(--accent)">
        ℹ Your deposit will be reviewed and credited in USDT within 1–24 hours after admin confirmation.
      </div>

      <div style="display:flex;gap:10px">
        <button type="button" class="btn bg" style="flex:1" onclick="goStep(1)">← Back</button>
        <button type="submit" class="btn bp" style="flex:2">Submit Deposit</button>
      </div>
    </form>
  </div>
</div>

<script>
var selectedNetwork = null;
var netMeta = {
  'BTC': { color: '#F7931A', bg: '#F7931A22', name: 'Bitcoin',  icon: '₿' },
  'ETH': { color: '#627EEA', bg: '#627EEA22', name: 'Ethereum', icon: 'Ξ' },
};

function selectNetwork(n) {
  selectedNetwork = n;
  ['BTC','ETH'].forEach(function(k) {
    var c = document.getElementById('card-' + k);
    c.style.borderColor = (k === n) ? netMeta[k].color : '';
    c.style.background  = (k === n) ? netMeta[k].bg    : '';
  });
  document.getElementById('net-err').style.display = 'none';
}

function goStep2() {
  var ok = true;
  if (!selectedNetwork) { document.getElementById('net-err').style.display = 'block'; ok = false; }
  var amt = parseFloat(document.getElementById('s1-amount').value);
  if (!amt || amt < 1) { document.getElementById('amt-err').style.display = 'block'; ok = false; }
  else document.getElementById('amt-err').style.display = 'none';
  if (!ok) return;

  document.getElementById('f-network').value = selectedNetwork;
  document.getElementById('f-amount').value  = amt.toFixed(2);

  var meta = netMeta[selectedNetwork];
  document.getElementById('s2-amount').textContent       = amt.toFixed(2);
  document.getElementById('sum-net-icon').textContent    = meta.icon;
  document.getElementById('sum-net-icon').style.background = meta.bg;
  document.getElementById('sum-net-icon').style.color    = meta.color;
  document.getElementById('sum-net-label').textContent   = selectedNetwork;
  document.getElementById('sum-net-label').style.color   = meta.color;

  goStep(2);
}

function goStep(n) {
  document.querySelectorAll('.step-panel').forEach(function(p){ p.classList.remove('active'); });
  document.getElementById('panel' + n).classList.add('active');
  [1,2].forEach(function(i){
    var d = document.getElementById('dot' + i);
    d.classList.remove('active','done');
    if (i < n) d.classList.add('done');
    else if (i === n) d.classList.add('active');
  });
  window.scrollTo({ top:0, behavior:'smooth' });
}

function fileSelected() {
  var file = document.getElementById('ss-input').files[0];
  if (file) {
    document.getElementById('ss-name').textContent = file.name;
    document.getElementById('ss-zone').classList.add('has-file');
  }
}

@if($errors->any()) goStep(2); @endif
</script>
@endsection
