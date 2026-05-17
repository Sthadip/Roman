@extends('layouts.wallet')
@section('title','Withdrawal History — NEXUS')
@section('page-title','Withdrawal History')
@section('content')

<div class="sh">
  <div></div>
  <a href="{{ route('user.withdraw.form') }}" class="btn bp bsm">↑ New Withdrawal</a>
</div>

<div class="card desk-tbl tw">
  @if($withdrawals->count())
  <table>
    <thead>
      <tr style="background:#040f1c">
        <th>Date</th>
        <th>Paid (USDT)</th>
        <th>Receive</th>
        <th>Rate</th>
        <th>Destination</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($withdrawals as $w)
      <tr>
        <td>
          <div style="font-size:13px">{{ $w->created_at->format('M d, Y') }}</div>
          <div style="font-size:11px;color:var(--muted)">{{ $w->created_at->format('h:i A') }}</div>
        </td>
        <td>
          <span class="mono" style="font-weight:700;color:#26A17B">${{ number_format((float)($w->usdt_amount ?: $w->amount),2) }}</span>
        </td>
        <td>
          @php $m = $coinMeta[$w->coin] ?? ['color'=>'#fff','icon'=>'?']; @endphp
          <span style="color:{{ $m['color'] }};font-weight:700">{{ $m['icon'] }}</span>
          <span class="mono" style="font-weight:600">
            {{ number_format((float)($w->coin_amount ?: $w->amount), $w->coin==='USDT'?2:8) }}
          </span>
          <span style="font-size:12px;color:var(--muted)">{{ $w->coin }}</span>
        </td>
        <td>
          @if($w->isCryptoWithdrawal() && $w->rate_used)
            <span class="mono" style="font-size:12px;color:var(--dim)">${{ number_format((float)$w->rate_used,0) }}</span>
          @else
            <span style="color:var(--dim)">—</span>
          @endif
        </td>
        <td>
          <span class="mono" style="font-size:12px;color:var(--muted)">{{ Str::limit($w->wallet_address,22) }}</span>
          @if($w->network)<div style="font-size:11px;color:var(--dim)">{{ $w->network }}</div>@endif
        </td>
        <td><span class="badge badge-{{ $w->status }}">{{ ucfirst($w->status) }}</span></td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div style="text-align:center;padding:48px;color:var(--muted)">No withdrawals yet.</div>
  @endif
</div>

{{-- Mobile cards --}}
<div class="mob-list">
  @forelse($withdrawals as $w)
  @php $m = $coinMeta[$w->coin] ?? ['color'=>'#fff','icon'=>'?']; @endphp
  <div class="card" style="margin-bottom:10px">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px">
      <div>
        <div style="font-size:13px;font-weight:700;color:#26A17B">${{ number_format((float)($w->usdt_amount ?: $w->amount),2) }} USDT</div>
        <div style="font-size:12px;color:var(--muted)">{{ $w->created_at->diffForHumans() }}</div>
      </div>
      <span class="badge badge-{{ $w->status }}">{{ ucfirst($w->status) }}</span>
    </div>
    {{-- Conversion arrow if crypto --}}
    @if($w->isCryptoWithdrawal())
    <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:#040f1c;border:1px solid var(--border2);border-radius:8px;margin-bottom:8px;font-size:13px">
      <span style="color:#26A17B">${{ number_format((float)($w->usdt_amount ?: $w->amount),2) }}</span>
      <span style="color:var(--dim)">→</span>
      <span style="color:{{ $m['color'] }};font-weight:700">{{ $m['icon'] }} {{ number_format((float)($w->coin_amount ?: $w->amount),8) }} {{ $w->coin }}</span>
    </div>
    @endif
    <div class="mono" style="font-size:11.5px;color:var(--muted);word-break:break-all">{{ $w->wallet_address }}</div>
  </div>
  @empty
  <div class="card" style="text-align:center;padding:40px;color:var(--muted)">No withdrawals yet.</div>
  @endforelse
</div>

@if($withdrawals->hasPages())<div class="pg">{!! $withdrawals->links()->toHtml() !!}</div>@endif
@endsection
