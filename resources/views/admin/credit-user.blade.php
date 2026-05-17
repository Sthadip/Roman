@extends('layouts.wallet')
@section('title','Credit User — Admin')
@section('page-title','Manual USDT Credit')
@section('content')

@php
$isSuperAdmin = Auth::user()->isSuperAdmin();
$backRoute    = $isSuperAdmin ? 'admin.users.detail' : 'admin.users.detail';
@endphp

{{-- Back --}}
<div style="margin-bottom:18px;display:flex;align-items:center;gap:10px">
  <a href="{{ route('admin.users.detail', $user->id) }}" class="btn bg bsm">← Back to Profile</a>
</div>

<div class="tc">
  <div class="tc-main">

    {{-- Form card --}}
    <div class="card">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px;padding-bottom:18px;border-bottom:1px solid var(--border)">
        <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:#030a12;overflow:hidden;flex-shrink:0">
          @if($user->avatar)<img src="{{ $user->avatar }}" style="width:100%;height:100%;object-fit:cover">
          @else{{ strtoupper(substr($user->name,0,1)) }}@endif
        </div>
        <div>
          <div style="font-size:17px;font-weight:800">Manually Credit {{ $user->name }}</div>
          <div style="font-size:13px;color:var(--muted)">{{ $user->email }}</div>
        </div>
      </div>

      {{-- Warning banner --}}
      <div style="background:#ffd6000a;border:1px solid #ffd60033;border-radius:10px;padding:13px 16px;margin-bottom:22px;font-size:13px;color:var(--yellow);line-height:1.7">
        ⚠ <strong>Admin Action:</strong> This will directly add USDT to the user's wallet without requiring a deposit request.
        The transaction will be recorded in the system with your name as the authorising admin.
      </div>

      @if($errors->any())
      <div style="background:#ff52520a;border:1px solid #ff525233;border-radius:10px;padding:13px 16px;margin-bottom:18px;font-size:13px;color:var(--red)">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
      </div>
      @endif

      <form method="POST" action="{{ route('admin.users.credit.submit', $user->id) }}" id="credit-form">
        @csrf

        {{-- Amount --}}
        <div class="fg">
          <label class="fl">Amount to Credit (USDT) <span style="color:var(--red)">*</span></label>
          <div style="position:relative">
            <span style="position:absolute;left:13px;top:50%;transform:translateY(-50%);font-size:18px;font-weight:800;color:#26A17B;pointer-events:none">$</span>
            <input type="number" name="amount" id="credit-amount" class="fi @error('amount') fi-err @enderror"
              style="padding-left:34px;font-size:20px;font-weight:700;color:#26A17B"
              placeholder="0.00" step="0.01" min="0.01" max="999999.99"
              value="{{ old('amount') }}" oninput="updatePreview()">
          </div>
          @error('amount')<div class="err">{{ $message }}</div>@enderror
        </div>

        {{-- Reason --}}
        <div class="fg">
          <label class="fl">Reason <span style="color:var(--red)">*</span></label>
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:2px" id="reason-grid">
            @foreach([
              ['bonus',      '★',  'Bonus',      '#7c4dff'],
              ['reward',     '🏆', 'Reward',     '#ffd600'],
              ['correction', '⚙',  'Correction', '#00e5ff'],
              ['refund',     '↩',  'Refund',     '#00e5a0'],
              ['other',      '•',  'Other',      '#5a8aa0'],
            ] as [$val, $icon, $label, $color])
            <label style="cursor:pointer">
              <input type="radio" name="reason" value="{{ $val }}" {{ old('reason')===$val?'checked':'' }} style="display:none" onchange="selectReason(this,'{{ $color }}')">
              <div class="reason-card" id="reason-{{ $val }}" style="border:2px solid var(--border2);border-radius:10px;padding:10px 8px;text-align:center;transition:all .2s">
                <div style="font-size:20px;margin-bottom:4px">{{ $icon }}</div>
                <div style="font-size:12px;font-weight:700">{{ $label }}</div>
              </div>
            </label>
            @endforeach
          </div>
          @error('reason')<div class="err" style="margin-top:6px">{{ $message }}</div>@enderror
        </div>

        {{-- Note --}}
        <div class="fg">
          <label class="fl">Internal Note <span style="color:var(--red)">*</span></label>
          <textarea name="note" class="fi @error('note') fi-err @enderror"
            placeholder="e.g. Compensating for missed bonus in November campaign"
            rows="3" style="resize:vertical">{{ old('note') }}</textarea>
          <div style="font-size:11.5px;color:var(--dim);margin-top:5px">This note will appear in the transaction history visible to the user.</div>
          @error('note')<div class="err">{{ $message }}</div>@enderror
        </div>

        {{-- Preview --}}
        <div id="preview-box" style="background:#26A17B0a;border:1px solid #26A17B33;border-radius:12px;padding:16px 18px;margin-bottom:20px;display:none">
          <div style="font-size:11px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px">Transaction Preview</div>
          <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #26A17B22;font-size:13px">
            <span style="color:var(--muted)">Recipient</span>
            <span style="font-weight:600">{{ $user->name }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #26A17B22;font-size:13px">
            <span style="color:var(--muted)">Current Balance</span>
            <span style="font-weight:600">${{ number_format((float)($usdWallet->available??0),2) }} USDT</span>
          </div>
          <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #26A17B22;font-size:13px">
            <span style="color:var(--muted)">Credit Amount</span>
            <span style="font-weight:700;color:#26A17B">+ $<span id="prev-amount">0.00</span> USDT</span>
          </div>
          <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:14px">
            <span style="color:var(--muted);font-weight:600">Balance After</span>
            <span style="font-weight:800;color:#26A17B;font-size:16px">$<span id="prev-after">{{ number_format((float)($usdWallet->available??0),2) }}</span> USDT</span>
          </div>
        </div>

        <button type="button" class="btn bp" style="width:100%;padding:14px;font-size:15px" onclick="confirmCredit()">
          ⊕ Credit USDTT to {{ $user->name }}
        </button>
      </form>
    </div>

  </div>

  {{-- Right sidebar --}}
  <div class="tc-side">

    {{-- User wallet summary --}}
    <div class="card" style="margin-bottom:16px">
      <div style="font-size:12px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">Current Wallet</div>
      <div style="background:#26A17B0a;border:1px solid #26A17B33;border-radius:10px;padding:14px 16px;text-align:center;margin-bottom:12px">
        <div style="font-size:11px;color:var(--muted);margin-bottom:4px">USDT Balance</div>
        <div style="display:flex;align-items:baseline;gap:4px;justify-content:center">
          <span style="font-size:22px;font-weight:800;color:#26A17B">$</span>
          <span class="mono" style="font-size:28px;font-weight:900;color:#26A17B">{{ number_format((float)($usdWallet->available??0),2) }}</span>
        </div>
        <div style="font-size:11px;color:var(--muted);margin-top:4px">Available</div>
      </div>
      @if(($usdWallet->in_order??0) > 0)
      <div style="display:flex;justify-content:space-between;font-size:13px;padding:8px 0;border-top:1px solid var(--border)">
        <span style="color:var(--muted)">In Orders</span>
        <span style="font-weight:600;color:var(--yellow)">${{ number_format((float)$usdWallet->in_order,2) }}</span>
      </div>
      @endif
    </div>

    {{-- User info --}}
    <div class="card" style="margin-bottom:16px">
      <div style="font-size:12px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">Account Info</div>
      @php
        $totalDeps = $user->deposits()->where('status','confirmed')->count();
        $totalVol  = $user->deposits()->where('status','confirmed')->sum('amount');
        $kyc       = $user->kyc;
      @endphp
      @foreach([
        ['Member Since',   $user->created_at->format('M d, Y')],
        ['KYC Status',     $kyc ? ucfirst($kyc->status) : 'Not submitted'],
        ['Total Deposits', $totalDeps.' deposits'],
        ['Total Deposited','$'.number_format((float)$totalVol,2).' USDT'],
      ] as [$l,$v])
      <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px">
        <span style="color:var(--muted)">{{ $l }}</span>
        <span style="font-weight:600">{{ $v }}</span>
      </div>
      @endforeach
    </div>

    {{-- Recent manual credits --}}
    @if($recentCredits->count())
    <div class="card">
      <div style="font-size:12px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">Recent Manual Credits</div>
      @foreach($recentCredits as $tx)
      <div style="{{ $loop->last ? 'padding:10px 0' : 'padding:10px 0;border-bottom:1px solid var(--border)' }}">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
          <div style="font-size:12px;color:var(--muted);flex:1">{{ Str::limit($tx->description,40) }}</div>
          <div style="font-size:13px;font-weight:700;color:#26A17B;flex-shrink:0">+${{ number_format((float)$tx->amount,2) }}</div>
        </div>
        <div style="font-size:11px;color:var(--dim);margin-top:3px">{{ $tx->created_at->diffForHumans() }}</div>
      </div>
      @endforeach
    </div>
    @endif

  </div>
</div>

<script>
var currentBalance = {{ (float)($usdWallet->available??0) }};

function updatePreview() {
  var amt = parseFloat(document.getElementById('credit-amount').value);
  var box = document.getElementById('preview-box');
  if (isNaN(amt) || amt <= 0) { box.style.display = 'none'; return; }
  box.style.display = 'block';
  document.getElementById('prev-amount').textContent = amt.toFixed(2);
  document.getElementById('prev-after').textContent  = (currentBalance + amt).toFixed(2);
}

function selectReason(radio, color) {
  document.querySelectorAll('.reason-card').forEach(function(c) {
    c.style.borderColor = '';
    c.style.background  = '';
    c.style.color       = '';
  });
  var card = document.getElementById('reason-' + radio.value);
  card.style.borderColor = color;
  card.style.background  = color + '18';
}

function confirmCredit() {
  var amt    = parseFloat(document.getElementById('credit-amount').value);
  var reason = document.querySelector('input[name="reason"]:checked');
  if (!amt || amt <= 0) { alert('Please enter a valid amount.'); return; }
  if (!reason)          { alert('Please select a reason.'); return; }
  var note = document.querySelector('textarea[name="note"]').value.trim();
  if (!note)            { alert('Please enter a note.'); return; }
  if (confirm('Credit $' + amt.toFixed(2) + ' USDT to {{ $user->name }}?\n\nThis action cannot be undone.')) {
    document.getElementById('credit-form').submit();
  }
}

// Restore reason selection on validation error
@php $oldReason = old('reason', ''); @endphp
@if($oldReason)
(function(){
  var r = document.querySelector('input[name="reason"][value="{{ $oldReason }}"]');
  if (r) { r.checked = true; selectReason(r, '#5a8aa0'); }
})();
@endif
</script>
@endsection
