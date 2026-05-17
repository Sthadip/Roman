@extends('layouts.wallet')
@section('title','Dashboard — NEXUS')
@section('page-title','Dashboard')
@section('content')
@php $coinMeta = $coinMeta ?? \App\Models\Wallet::supportedCoins(); @endphp

{{-- Hero balance --}}
<div class="card" style="background:linear-gradient(135deg,#091525,#0b2035);margin-bottom:18px;position:relative;overflow:hidden">
  <div style="position:absolute;right:-30px;top:-30px;width:200px;height:200px;background:radial-gradient(circle,#26A17B0a 0%,transparent 70%);pointer-events:none"></div>
  <div style="font-size:12px;color:var(--muted);margin-bottom:2px">USDT Balance</div>
  <div style="font-size:38px;font-weight:800;font-family:'DM Mono',monospace;color:#26A17B;line-height:1;margin-bottom:4px">
    ${{ number_format((float)($wallets['USDT']->available ?? 0),2) }}
  </div>
  @if(($wallets['USDT']->in_order ?? 0) > 0)
  <div style="font-size:13px;color:var(--yellow);margin-bottom:12px">+ ${{ number_format((float)$wallets['USDT']->in_order,2) }} locked</div>
  @else<div style="margin-bottom:12px"></div>@endif
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    <a href="{{ route('user.deposit.form') }}" class="btn bp bsm">↓ Deposit USDT</a>
    <a href="{{ route('user.withdraw.form') }}" class="btn bg bsm">↑ Withdraw</a>
  </div>
</div>

{{-- Stats --}}
<div class="sg" style="margin-bottom:18px">
  <div class="sc">
    <div class="sc-icon" style="color:var(--green)">↓</div>
    <div class="sc-val" style="color:var(--green)">${{ number_format((float)$totalDeposited,2) }}</div>
    <div class="sc-lbl">Total Confirmed Deposits</div>
  </div>
  <div class="sc">
    <div class="sc-icon" style="color:var(--red)">↑</div>
    <div class="sc-val" style="color:var(--red)">${{ number_format((float)$totalWithdrawn,2) }}</div>
    <div class="sc-lbl">Total Confirmed Withdrawals</div>
  </div>
  <div class="sc">
    <div class="sc-icon" style="color:var(--yellow)">⏳</div>
    <div class="sc-val" style="color:var(--yellow)">{{ $pendingDeposits }}</div>
    <div class="sc-lbl">Pending Deposits</div>
  </div>
  <div class="sc">
    <div class="sc-icon" style="color:var(--accent)">⏳</div>
    <div class="sc-val" style="color:var(--accent)">{{ $pendingWithdrawals }}</div>
    <div class="sc-lbl">Pending Withdrawals</div>
  </div>
</div>

<div class="tc">
  <div class="tc-main">
    {{-- Wallet balances --}}
    <div class="card" style="margin-bottom:18px">
      <div class="sh"><h2>Wallet Balances</h2><a href="{{ route('user.wallet') }}" class="btn bg bsm">View All</a></div>
      @foreach($coinMeta as $coin => $meta)
      @php $w = $wallets[$coin] ?? null; @endphp
      <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:{{ $meta['bg'] }};border:1px solid {{ $meta['color'] }}22;border-radius:12px;margin-bottom:8px">
        <div style="width:38px;height:38px;border-radius:50%;background:{{ $meta['color'] }}22;border:1px solid {{ $meta['color'] }}44;display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:700;color:{{ $meta['color'] }};flex-shrink:0">{{ $meta['icon'] }}</div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13.5px;font-weight:600">{{ $meta['name'] }}</div>
          <div style="font-size:11px;color:var(--muted)">{{ $coin }}</div>
        </div>
        <div style="text-align:right">
          <div class="mono" style="font-size:15px;font-weight:700;color:{{ $meta['color'] }}">{{ number_format((float)($w->available??0),($coin==='USDT'?2:6)) }}</div>
          @if(($w->in_order??0)>0)<div style="font-size:11px;color:var(--yellow)">{{ number_format((float)$w->in_order,6) }} locked</div>@endif
        </div>
      </div>
      @endforeach
    </div>

    {{-- Recent activity --}}
    <div class="card">
      <div class="sh"><h2>Recent Activity</h2>
        <div style="display:flex;gap:6px">
          <a href="{{ route('user.deposit.history') }}" class="btn bg bsm">Deposits</a>
          <a href="{{ route('user.withdraw.history') }}" class="btn bg bsm">Withdrawals</a>
        </div>
      </div>
      @forelse($recent as $item)
      <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border)">
        <div style="width:34px;height:34px;border-radius:50%;background:{{ $item['type']==='deposit'?'#00e5a018':'#ff525218' }};display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0">{{ $item['type']==='deposit'?'↓':'↑' }}</div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13.5px;font-weight:600">{{ ucfirst($item['type']) }} · {{ $item['coin'] }}</div>
          <div style="font-size:11.5px;color:var(--muted)">{{ $item['at']->diffForHumans() }}</div>
        </div>
        <div style="text-align:right">
          <div class="mono" style="font-size:13.5px;font-weight:600;color:{{ $item['type']==='deposit'?'var(--green)':'var(--red)' }}">
            {{ $item['type']==='deposit'?'+':'-' }}{{ number_format((float)$item['amount'],4) }}
          </div>
          <span class="badge badge-{{ $item['status'] }}">{{ $item['status'] }}</span>
        </div>
      </div>
      @empty
      <div style="text-align:center;padding:28px;color:var(--muted);font-size:14px">No recent activity</div>
      @endforelse
    </div>
  </div>

  <div class="tc-side">
    <div class="card">
      <div class="sh"><h2>Quick Actions</h2></div>
      <div style="display:flex;flex-direction:column;gap:8px">
        <a href="{{ route('user.deposit.form') }}" class="btn bp" style="width:100%">↓ Deposit USDT</a>
        <a href="{{ route('user.withdraw.form') }}" class="btn bg" style="width:100%">↑ Withdraw</a>
        <a href="{{ route('user.transactions') }}" class="btn bg" style="width:100%">↕ Transactions</a>
        <a href="{{ route('user.kyc') }}" class="btn bg" style="width:100%">✔ KYC Status</a>
        <a href="{{ route('user.deposit.history') }}" class="btn bg" style="width:100%">Deposit History</a>
        <a href="{{ route('user.withdraw.history') }}" class="btn bg" style="width:100%">Withdrawal History</a>
      </div>
    </div>
  </div>
</div>
@endsection
