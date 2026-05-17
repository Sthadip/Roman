@extends('layouts.wallet')
@section('title','Deposit Detail — NEXUS')
@section('page-title','Deposit Detail')
@section('content')

@php
$netMeta = [
  'BTC' => ['icon'=>'₿','color'=>'#F7931A','bg'=>'#F7931A22','name'=>'Bitcoin'],
  'ETH' => ['icon'=>'Ξ','color'=>'#627EEA','bg'=>'#627EEA22','name'=>'Ethereum'],
];
$nm = $netMeta[$deposit->network] ?? ['icon'=>$deposit->network,'color'=>'#5a8aa0','bg'=>'#5a8aa022','name'=>$deposit->network];
@endphp

<div style="margin-bottom:18px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
  <a href="{{ route('user.deposit.history') }}" class="btn bg bsm">← Back to History</a>
  <span class="badge badge-{{ $deposit->status }}" style="font-size:13px;padding:5px 14px">{{ ucfirst($deposit->status) }}</span>
  <span style="font-size:13px;color:var(--muted)"># {{ $deposit->id }}</span>
</div>

{{-- Hero --}}
<div style="background:linear-gradient(135deg,#091525,#0d2035);border:1px solid #26A17B33;border-radius:16px;padding:24px 28px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px">
  <div>
    <div style="font-size:11px;color:var(--dim);text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px;font-weight:700">Deposit Amount — USDT</div>
    <div style="display:flex;align-items:baseline;gap:6px">
      <span style="font-size:28px;font-weight:800;color:#26A17B">$</span>
      <span class="mono" style="font-size:42px;font-weight:900;color:#26A17B;line-height:1">{{ number_format((float)$deposit->amount, 2) }}</span>
      <span style="font-size:16px;font-weight:700;color:#26A17B">USDT</span>
    </div>
    <div style="display:flex;align-items:center;gap:8px;margin-top:10px">
      <div style="width:22px;height:22px;border-radius:50%;background:{{ $nm['bg'] }};border:1px solid {{ $nm['color'] }}44;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:900;color:{{ $nm['color'] }}">{{ $nm['icon'] }}</div>
      <span style="font-size:13px;color:var(--muted)">sent via <strong style="color:{{ $nm['color'] }}">{{ $nm['name'] }} ({{ $deposit->network }})</strong> network</span>
    </div>
    <div style="font-size:13px;color:var(--muted);margin-top:6px">
      Submitted {{ $deposit->created_at->format('M d, Y') }} at {{ $deposit->created_at->format('h:i A') }}
      · {{ $deposit->created_at->diffForHumans() }}
    </div>
  </div>
  <div style="display:flex;flex-direction:column;align-items:center;gap:6px">
    <div style="width:64px;height:64px;border-radius:50%;background:#26A17B18;border:2px solid #26A17B44;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:900;color:#26A17B">$</div>
    <div style="font-size:11px;font-weight:700;color:#26A17B;letter-spacing:.04em">USDT</div>
  </div>
</div>

<div class="tc">
  {{-- Left --}}
  <div class="tc-main">
    <div class="card" style="margin-bottom:18px">
      <div style="font-size:12px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px">Deposit Information</div>

      <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:11px 0;border-bottom:1px solid var(--border);gap:12px">
        <span style="font-size:13px;color:var(--muted);flex-shrink:0">Amount Credited</span>
        <span style="font-size:14px;font-weight:700;color:#26A17B;text-align:right">${{ number_format((float)$deposit->amount, 2) }} USDT</span>
      </div>

      <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border);gap:12px">
        <span style="font-size:13px;color:var(--muted);flex-shrink:0">Sending Network</span>
        <div style="display:flex;align-items:center;gap:7px">
          <div style="width:24px;height:24px;border-radius:50%;background:{{ $nm['bg'] }};border:1px solid {{ $nm['color'] }}33;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:900;color:{{ $nm['color'] }}">{{ $nm['icon'] }}</div>
          <span style="font-size:13.5px;font-weight:600;color:{{ $nm['color'] }}">{{ $nm['name'] }} ({{ $deposit->network }})</span>
        </div>
      </div>

      <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:11px 0;border-bottom:1px solid var(--border);gap:12px">
        <span style="font-size:13px;color:var(--muted);flex-shrink:0">Credited As</span>
        <span style="font-size:13.5px;font-weight:600;color:#26A17B;text-align:right">USDT</span>
      </div>

      <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:11px 0;border-bottom:1px solid var(--border);gap:12px">
        <span style="font-size:13px;color:var(--muted);flex-shrink:0">Submitted</span>
        <span style="font-size:13px;font-weight:600;text-align:right">
          {{ $deposit->created_at->format('M d, Y · h:i A') }}<br>
          <span style="font-size:11.5px;color:var(--muted);font-weight:400">{{ $deposit->created_at->diffForHumans() }}</span>
        </span>
      </div>

      <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:11px 0;border-bottom:1px solid var(--border);gap:12px">
        <span style="font-size:13px;color:var(--muted);flex-shrink:0">Transaction ID</span>
        <span class="mono" style="font-size:12px;font-weight:600;word-break:break-all;text-align:right;max-width:280px">{{ $deposit->transaction_id }}</span>
      </div>

      @if($deposit->note)
      <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:11px 0;border-bottom:1px solid var(--border);gap:12px">
        <span style="font-size:13px;color:var(--muted);flex-shrink:0">Note</span>
        <span style="font-size:13px;text-align:right">{{ $deposit->note }}</span>
      </div>
      @endif

      <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 0">
        <span style="font-size:13px;color:var(--muted)">Status</span>
        <span class="badge badge-{{ $deposit->status }}" style="font-size:13px;padding:5px 14px">{{ ucfirst($deposit->status) }}</span>
      </div>

      @if($deposit->reviewed_at)
      <div style="margin-top:10px;padding:10px 14px;background:#040f1c;border:1px solid var(--border2);border-radius:8px;font-size:12.5px;color:var(--dim)">
        Reviewed by <strong style="color:var(--muted)">Admin</strong>
        on {{ $deposit->reviewed_at->format('M d, Y') }} at {{ $deposit->reviewed_at->format('h:i A') }}
      </div>
      @endif
    </div>

    {{-- Screenshot --}}
    @if($deposit->screenshot_path)
    @php $ext = strtolower(pathinfo($deposit->screenshot_path, PATHINFO_EXTENSION)); @endphp
    <div class="card">
      <div style="font-size:12px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">📎 Payment Proof</div>
      @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
      <a href="{{ Storage::url($deposit->screenshot_path) }}" target="_blank">
        <img src="{{ Storage::url($deposit->screenshot_path) }}" alt="Payment Screenshot"
          style="width:100%;border-radius:10px;border:1px solid var(--border2);max-height:420px;object-fit:contain;background:#040f1c;display:block">
      </a>
      <div style="margin-top:10px">
        <a href="{{ Storage::url($deposit->screenshot_path) }}" target="_blank" class="btn bg bsm">🔍 Open Full Size</a>
      </div>
      @else
      <a href="{{ Storage::url($deposit->screenshot_path) }}" target="_blank" class="btn bg" style="width:100%">
        📎 Download Attachment ({{ strtoupper($ext) }})
      </a>
      @endif
    </div>
    @else
    <div class="card" style="text-align:center;padding:28px;color:var(--dim)">
      <div style="font-size:24px;margin-bottom:8px;opacity:.4">📎</div>
      No payment proof uploaded
    </div>
    @endif
  </div>

  {{-- Right --}}
  <div class="tc-side">
    <div class="card" style="margin-bottom:16px">
      <div style="font-size:12px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px">Deposit Status</div>

      {{-- Timeline --}}
      <div style="display:flex;gap:12px;align-items:flex-start">
        <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0">
          <div style="width:28px;height:28px;border-radius:50%;background:#00e5a022;border:2px solid var(--green);display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--green);font-weight:700">✓</div>
          <div style="width:2px;height:32px;background:var(--border2);margin:4px 0"></div>
        </div>
        <div style="padding-top:3px;padding-bottom:16px">
          <div style="font-size:13px;font-weight:700">Submitted</div>
          <div style="font-size:11.5px;color:var(--muted);margin-top:2px">{{ $deposit->created_at->format('M d, Y · h:i A') }}</div>
        </div>
      </div>

      <div style="display:flex;gap:12px;align-items:flex-start">
        <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0">
          @if($deposit->isPending())
            <div style="width:28px;height:28px;border-radius:50%;background:#ffd60022;border:2px solid var(--yellow);display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--yellow);font-weight:700">⏳</div>
          @else
            <div style="width:28px;height:28px;border-radius:50%;background:#00e5a022;border:2px solid var(--green);display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--green);font-weight:700">✓</div>
          @endif
          <div style="width:2px;height:32px;background:var(--border2);margin:4px 0"></div>
        </div>
        <div style="padding-top:3px;padding-bottom:16px">
          <div style="font-size:13px;font-weight:700;color:{{ $deposit->isPending() ? 'var(--yellow)' : 'var(--text)' }}">
            {{ $deposit->isPending() ? 'Under Review' : 'Reviewed' }}
          </div>
          <div style="font-size:11.5px;color:var(--muted);margin-top:2px">
            @if($deposit->isPending()) Awaiting admin verification
            @else {{ $deposit->reviewed_at->format('M d, Y · h:i A') }} @endif
          </div>
        </div>
      </div>

      <div style="display:flex;gap:12px;align-items:flex-start">
        <div style="flex-shrink:0">
          @if($deposit->isPending())
            <div style="width:28px;height:28px;border-radius:50%;background:var(--border2);border:2px solid var(--border2);display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--muted);font-weight:700">3</div>
          @elseif($deposit->isConfirmed())
            <div style="width:28px;height:28px;border-radius:50%;background:#00e5a022;border:2px solid var(--green);display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--green);font-weight:700">✓</div>
          @else
            <div style="width:28px;height:28px;border-radius:50%;background:#ff525222;border:2px solid var(--red);display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--red);font-weight:700">✕</div>
          @endif
        </div>
        <div style="padding-top:3px">
          @if($deposit->isPending())
            <div style="font-size:13px;font-weight:700;color:var(--muted)">USDT Credited</div>
            <div style="font-size:11.5px;color:var(--dim);margin-top:2px">Pending admin approval</div>
          @elseif($deposit->isConfirmed())
            <div style="font-size:13px;font-weight:700;color:var(--green)">USDT Credited ✓</div>
            <div style="font-size:11.5px;color:var(--muted);margin-top:2px">
              ${{ number_format((float)$deposit->amount, 2) }} USDT added to your wallet
            </div>
          @else
            <div style="font-size:13px;font-weight:700;color:var(--red)">Deposit Rejected</div>
            <div style="font-size:11.5px;color:var(--muted);margin-top:2px">No funds were credited. Contact support.</div>
          @endif
        </div>
      </div>
    </div>

    {{-- Summary --}}
    <div class="card">
      <div style="font-size:12px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">Summary</div>
      <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border);font-size:13px">
        <span style="color:var(--muted)">Deposit #</span>
        <span style="font-weight:600">{{ $deposit->id }}</span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border);font-size:13px">
        <span style="color:var(--muted)">USDT Credited</span>
        <span style="font-weight:700;color:#26A17B">${{ number_format((float)$deposit->amount, 2) }}</span>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid var(--border);font-size:13px">
        <span style="color:var(--muted)">Network Used</span>
        <span style="font-weight:700;color:{{ $nm['color'] }}">{{ $nm['icon'] }} {{ $deposit->network }}</span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:9px 0;font-size:13px">
        <span style="color:var(--muted)">Status</span>
        <span class="badge badge-{{ $deposit->status }}">{{ ucfirst($deposit->status) }}</span>
      </div>

      @if($deposit->isPending())
      <div style="margin-top:14px;background:#ffd6000a;border:1px solid #ffd60022;border-radius:8px;padding:11px 13px;font-size:12.5px;color:var(--yellow);line-height:1.6">
        ⏳ Your deposit is being reviewed. This usually takes 1–24 hours.
      </div>
      @elseif($deposit->isConfirmed())
      <div style="margin-top:14px;background:#00e5a00a;border:1px solid #00e5a022;border-radius:8px;padding:11px 13px;font-size:12.5px;color:var(--green);line-height:1.6">
        ✓ Deposit confirmed. ${{ number_format((float)$deposit->amount, 2) }} USDT credited to your wallet.
      </div>
      @else
      <div style="margin-top:14px;background:#ff52520a;border:1px solid #ff525222;border-radius:8px;padding:11px 13px;font-size:12.5px;color:var(--red);line-height:1.6">
        ✕ This deposit was rejected. Please contact support if you believe this is an error.
      </div>
      @endif

      <div style="margin-top:14px;display:flex;flex-direction:column;gap:8px">
        <a href="{{ route('user.deposit.history') }}" class="btn bg bsm" style="text-align:center">← All Deposits</a>
        @if(!$deposit->isPending())
        <a href="{{ route('user.deposit.form') }}" class="btn bp bsm" style="text-align:center">↓ New Deposit</a>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
