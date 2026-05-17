@extends('layouts.wallet')
@section('title','Deposit Detail — Admin')
@section('page-title','Deposit Detail')
@section('content')

@php
$networkMeta = [
  'BTC' => ['icon'=>'₿','color'=>'#F7931A','bg'=>'#F7931A22','name'=>'Bitcoin'],
  'ETH' => ['icon'=>'Ξ','color'=>'#627EEA','bg'=>'#627EEA22','name'=>'Ethereum'],
];
$nm = $networkMeta[$deposit->network] ?? ['icon'=>$deposit->network,'color'=>'#5a8aa0','bg'=>'#5a8aa022','name'=>$deposit->network];
@endphp

<div style="margin-bottom:18px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
  <a href="{{ route('admin.deposits') }}" class="btn bg bsm">← Back to Deposits</a>
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
  <div style="display:flex;flex-direction:column;align-items:center;gap:4px">
    <div style="width:60px;height:60px;border-radius:50%;background:#26A17B18;border:2px solid #26A17B44;display:flex;align-items:center;justify-content:center;font-size:30px;font-weight:900;color:#26A17B">$</div>
    <div style="font-size:11px;font-weight:700;color:#26A17B;letter-spacing:.04em">USDT</div>
  </div>
</div>

<div class="tc">
  {{-- Left: Info + Screenshot --}}
  <div class="tc-main">

    <div class="card" style="margin-bottom:18px">
      <div style="font-size:12px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px">Deposit Information</div>

      {{-- User --}}
      <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:11px 0;border-bottom:1px solid var(--border);gap:12px">
        <span style="font-size:13px;color:var(--muted);flex-shrink:0">User</span>
        <span style="font-size:13.5px;font-weight:600;text-align:right">{{ $deposit->user->name }}</span>
      </div>

      {{-- Email --}}
      <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:11px 0;border-bottom:1px solid var(--border);gap:12px">
        <span style="font-size:13px;color:var(--muted);flex-shrink:0">Email</span>
        <span style="font-size:13px;font-weight:500;text-align:right;color:var(--muted)">{{ $deposit->user->email }}</span>
      </div>

      {{-- Amount --}}
      <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:11px 0;border-bottom:1px solid var(--border);gap:12px">
        <span style="font-size:13px;color:var(--muted);flex-shrink:0">Amount (USDT)</span>
        <span style="font-size:14px;font-weight:700;color:#26A17B;text-align:right">${{ number_format((float)$deposit->amount, 2) }} USDT</span>
      </div>

      {{-- Credited As --}}
      <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:11px 0;border-bottom:1px solid var(--border);gap:12px">
        <span style="font-size:13px;color:var(--muted);flex-shrink:0">Credited As</span>
        <span style="font-size:13.5px;font-weight:600;color:#26A17B;text-align:right">USDT (USDT wallet)</span>
      </div>

      {{-- Network --}}
      <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border);gap:12px">
        <span style="font-size:13px;color:var(--muted);flex-shrink:0">Sending Network</span>
        <div style="display:flex;align-items:center;gap:7px">
          <div style="width:24px;height:24px;border-radius:50%;background:{{ $nm['bg'] }};border:1px solid {{ $nm['color'] }}33;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:900;color:{{ $nm['color'] }}">{{ $nm['icon'] }}</div>
          <span style="font-size:13.5px;font-weight:600;color:{{ $nm['color'] }}">{{ $nm['name'] }} ({{ $deposit->network }})</span>
        </div>
      </div>

      {{-- Submitted --}}
      <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:11px 0;border-bottom:1px solid var(--border);gap:12px">
        <span style="font-size:13px;color:var(--muted);flex-shrink:0">Submitted</span>
        <span style="font-size:13px;font-weight:600;text-align:right">
          {{ $deposit->created_at->format('Y-m-d H:i:s') }}<br>
          <span style="font-size:11.5px;color:var(--muted);font-weight:400">({{ $deposit->created_at->diffForHumans() }})</span>
        </span>
      </div>

      {{-- Transaction ID --}}
      <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:11px 0;border-bottom:1px solid var(--border);gap:12px">
        <span style="font-size:13px;color:var(--muted);flex-shrink:0">Transaction ID</span>
        <span class="mono" style="font-size:12.5px;font-weight:600;word-break:break-all;text-align:right;max-width:280px">{{ $deposit->transaction_id }}</span>
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
        Reviewed by <strong style="color:var(--muted)">{{ $deposit->reviewer->name ?? 'Admin' }}</strong>
        on {{ $deposit->reviewed_at->format('M d, Y') }} at {{ $deposit->reviewed_at->format('h:i A') }}
      </div>
      @endif
    </div>

    {{-- Screenshot --}}
    @if($deposit->screenshot_path)
    @php $ext = strtolower(pathinfo($deposit->screenshot_path, PATHINFO_EXTENSION)); @endphp
    <div class="card">
      <div style="font-size:12px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">
        📎 Payment Proof
      </div>
      @if(in_array($ext,['jpg','jpeg','png','gif','webp']))
      <a href="{{ Storage::url($deposit->screenshot_path) }}" target="_blank">
        <img src="{{ Storage::url($deposit->screenshot_path) }}" alt="Screenshot"
          style="width:100%;border-radius:10px;border:1px solid var(--border2);max-height:400px;object-fit:contain;background:#040f1c;display:block">
      </a>
      <div style="margin-top:10px;display:flex;gap:8px">
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

  {{-- Right: Actions + User Summary --}}
  <div class="tc-side">

    {{-- Action card --}}
    @if($deposit->isPending())
    <div class="card" style="margin-bottom:16px;border-color:#26A17B44;background:#26A17B05">
      <div style="font-size:15px;font-weight:700;margin-bottom:6px">Review Deposit</div>

      {{-- What will happen summary --}}
      <div style="background:#040f1c;border:1px solid var(--border2);border-radius:10px;padding:12px 14px;margin-bottom:16px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid var(--border)">
          <span style="font-size:12px;color:var(--muted)">User</span>
          <span style="font-size:13px;font-weight:600">{{ $deposit->user->name }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid var(--border)">
          <span style="font-size:12px;color:var(--muted)">Network</span>
          <span style="font-size:13px;font-weight:600;color:{{ $nm['color'] }}">{{ $nm['icon'] }} {{ $deposit->network }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid var(--border)">
          <span style="font-size:12px;color:var(--muted)">To Credit</span>
          <span style="font-size:15px;font-weight:800;color:#26A17B">${{ number_format((float)$deposit->amount, 2) }} USDT</span>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center">
          <span style="font-size:12px;color:var(--muted)">Credited Into</span>
          <span style="font-size:12px;font-weight:600;color:var(--muted)">USDT Wallet</span>
        </div>
      </div>

      <form method="POST" action="{{ route('admin.deposits.approve', $deposit->id) }}" style="margin-bottom:10px">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-green" style="width:100%;padding:13px;font-size:14px">
          ✓ Approve — Credit ${{ number_format((float)$deposit->amount,2) }} USDT
        </button>
      </form>
      <form method="POST" action="{{ route('admin.deposits.reject', $deposit->id) }}">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-red" style="width:100%;padding:13px;font-size:14px"
          onclick="return confirm('Reject this deposit? No funds will be credited.')">
          ✕ Reject — Do Not Credit
        </button>
      </form>

      <div style="margin-top:14px;padding:10px 12px;background:#ffd6000a;border:1px solid #ffd60022;border-radius:8px;font-size:12px;color:var(--yellow)">
        ⚠ Verify the payment proof and transaction ID carefully before approving.
      </div>
    </div>

    @else
    <div class="card" style="margin-bottom:16px;border-color:{{ $deposit->isConfirmed()?'#00e5a044':'#ff525244' }};background:{{ $deposit->isConfirmed()?'#00e5a005':'#ff525205' }}">
      <div style="text-align:center;padding:18px 0">
        <div style="font-size:36px;margin-bottom:10px">{{ $deposit->isConfirmed()?'✅':'❌' }}</div>
        <div style="font-size:16px;font-weight:800;color:{{ $deposit->isConfirmed()?'var(--green)':'var(--red)' }}">
          {{ $deposit->isConfirmed()?'Deposit Approved':'Deposit Rejected' }}
        </div>
        @if($deposit->isConfirmed())
        <div style="font-size:13px;color:#26A17B;margin-top:8px;font-weight:700">
          ${{ number_format((float)$deposit->amount,2) }} USDT credited
        </div>
        @endif
        @if($deposit->reviewed_at)
        <div style="font-size:12.5px;color:var(--muted);margin-top:6px">{{ $deposit->reviewed_at->diffForHumans() }}</div>
        <div style="font-size:12px;color:var(--dim)">by {{ $deposit->reviewer->name ?? 'Admin' }}</div>
        @endif
      </div>
    </div>
    @endif

    {{-- User summary --}}
    <div class="card">
      <div style="font-size:12px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">Account</div>

      <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--border)">
        <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-weight:700;color:#030a12;font-size:17px;overflow:hidden;flex-shrink:0">
          @if($deposit->user->avatar)<img src="{{ $deposit->user->avatar }}" style="width:100%;height:100%;object-fit:cover">
          @else{{ strtoupper(substr($deposit->user->name,0,1)) }}@endif
        </div>
        <div style="min-width:0">
          <div style="font-weight:700;font-size:14px">{{ $deposit->user->name }}</div>
          <div style="font-size:12px;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $deposit->user->email }}</div>
        </div>
      </div>

      @php
        $totalDeps = $deposit->user->deposits()->where('status','confirmed')->count();
        $totalVol  = $deposit->user->deposits()->where('status','confirmed')->sum('amount');
        $kyc       = $deposit->user->kyc;
        $usdWallet = \App\Models\Wallet::where('user_id',$deposit->user_id)->where('coin','USDT')->first();
      @endphp

      @foreach([
        ['USDT Balance',        '$'.number_format((float)($usdWallet->available??0),2)],
        ['Member since',        $deposit->user->created_at->format('M d, Y')],
        ['KYC',                 $kyc ? ucfirst($kyc->status) : 'Not submitted'],
        ['Confirmed deposits',  $totalDeps.' deposits'],
        ['Total USDT deposited', '$'.number_format((float)$totalVol,2)],
      ] as [$l,$v])
      <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px">
        <span style="color:var(--muted)">{{ $l }}</span>
        <span style="font-weight:600">{{ $v }}</span>
      </div>
      @endforeach

      <div style="margin-top:14px">
        <a href="{{ route('admin.users.detail', $deposit->user_id) }}" class="btn bg bsm" style="width:100%">
          View Full Profile →
        </a>
      </div>
    </div>

  </div>
</div>
@endsection
