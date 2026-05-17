@extends('layouts.wallet')
@section('title','Deposit History — NEXUS')
@section('page-title','Deposit History')
@section('content')

<div class="sh">
  <div></div>
  <a href="{{ route('user.deposit.form') }}" class="btn bp bsm">↓ New Deposit</a>
</div>

@php
$netMeta = [
  'BTC' => ['icon'=>'₿','color'=>'#F7931A','bg'=>'#F7931A22','name'=>'Bitcoin'],
  'ETH' => ['icon'=>'Ξ','color'=>'#627EEA','bg'=>'#627EEA22','name'=>'Ethereum'],
];
@endphp

{{-- Desktop --}}
<div class="card desk-tbl tw">
  @if($deposits->count())
  <table>
    <thead>
      <tr style="background:#040f1c">
        <th>Date</th>
        <th>Amount (USDT)</th>
        <th>Network</th>
        <th>Transaction ID</th>
        <th>Note</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach($deposits as $d)
      @php $nm = $netMeta[$d->network] ?? ['icon'=>$d->network,'color'=>'#5a8aa0','bg'=>'#5a8aa022','name'=>$d->network]; @endphp
      <tr>
        <td>
          <div style="font-size:13px">{{ $d->created_at->format('M d, Y') }}</div>
          <div style="font-size:11px;color:var(--muted)">{{ $d->created_at->format('h:i A') }}</div>
        </td>
        <td>
          <div style="display:flex;align-items:baseline;gap:4px">
            <span style="color:#26A17B;font-size:16px;font-weight:800">$</span>
            <span class="mono" style="font-size:16px;font-weight:800;color:#26A17B">{{ number_format((float)$d->amount, 2) }}</span>
            <span style="font-size:11px;font-weight:700;color:#26A17B">USDT</span>
          </div>
          <div style="font-size:11px;color:var(--muted);margin-top:2px">via {{ $d->network }} network</div>
        </td>
        <td>
          <div style="display:flex;align-items:center;gap:7px">
            <div style="width:26px;height:26px;border-radius:50%;background:{{ $nm['bg'] }};border:1px solid {{ $nm['color'] }}44;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:900;color:{{ $nm['color'] }}">{{ $nm['icon'] }}</div>
            <div>
              <div style="font-size:13px;font-weight:700;color:{{ $nm['color'] }}">{{ $d->network }}</div>
              <div style="font-size:11px;color:var(--muted)">{{ $nm['name'] }}</div>
            </div>
          </div>
        </td>
        <td><span class="mono" style="font-size:12px;color:var(--muted)">{{ Str::limit($d->transaction_id, 24) }}</span></td>
        <td style="font-size:13px;color:var(--muted)">{{ $d->note ?: '—' }}</td>
        <td><span class="badge badge-{{ $d->status }}">{{ ucfirst($d->status) }}</span></td>
        <td><a href="{{ route('user.deposit.detail', $d->id) }}" class="btn bg bsm">View →</a></td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div style="text-align:center;padding:48px;color:var(--muted)">
    <div style="font-size:32px;margin-bottom:12px">↓</div>
    No deposits yet. <a href="{{ route('user.deposit.form') }}">Make your first deposit →</a>
  </div>
  @endif
</div>

{{-- Mobile --}}
<div class="mob-list">
  @forelse($deposits as $d)
  @php $nm = $netMeta[$d->network] ?? ['icon'=>$d->network,'color'=>'#5a8aa0','bg'=>'#5a8aa022','name'=>$d->network]; @endphp
  <div class="card" style="margin-bottom:12px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
      <div style="display:flex;align-items:center;gap:8px">
        <div style="width:38px;height:38px;border-radius:50%;background:{{ $nm['bg'] }};border:1px solid {{ $nm['color'] }}44;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:900;color:{{ $nm['color'] }}">{{ $nm['icon'] }}</div>
        <div>
          <div style="font-size:13px;font-weight:700;color:{{ $nm['color'] }}">{{ $d->network }} Network</div>
          <div style="font-size:11px;color:var(--muted)">{{ $d->created_at->diffForHumans() }}</div>
        </div>
      </div>
      <span class="badge badge-{{ $d->status }}">{{ ucfirst($d->status) }}</span>
    </div>
    <div style="background:#26A17B0a;border:1px solid #26A17B22;border-radius:10px;padding:12px 14px;margin-bottom:12px">
      <div style="font-size:11px;color:var(--muted);margin-bottom:4px">Amount Deposited</div>
      <div style="display:flex;align-items:baseline;gap:5px">
        <span style="font-size:22px;font-weight:900;color:#26A17B">$</span>
        <span class="mono" style="font-size:22px;font-weight:900;color:#26A17B">{{ number_format((float)$d->amount, 2) }}</span>
        <span style="font-size:13px;font-weight:700;color:#26A17B">USDT</span>
      </div>
      <div style="font-size:12px;color:var(--muted);margin-top:4px">deposited via {{ $nm['name'] }} ({{ $d->network }}) network</div>
    </div>
    @if($d->transaction_id)
    <div style="font-size:11px;color:var(--dim);margin-bottom:10px">
      <span style="color:var(--muted);font-weight:600">TXN: </span>
      <span class="mono" style="word-break:break-all">{{ Str::limit($d->transaction_id, 40) }}</span>
    </div>
    @endif
    <a href="{{ route('user.deposit.detail', $d->id) }}" class="btn bg bsm" style="width:100%;text-align:center;display:block">View Full Details →</a>
  </div>
  @empty
  <div class="card" style="text-align:center;padding:40px;color:var(--muted)">
    No deposits yet. <a href="{{ route('user.deposit.form') }}">Deposit now →</a>
  </div>
  @endforelse
</div>

@if($deposits->hasPages())<div class="pg">{!! $deposits->links()->toHtml() !!}</div>@endif
@endsection
