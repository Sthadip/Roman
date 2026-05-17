@extends('layouts.wallet')
@section('title','Wallet — NEXUS Exchange')
@section('page-title','Wallet')
@section('content')

<div class="sh">
  <h2>Your Wallets</h2>
  <div style="display:flex;gap:8px">
    <a href="{{ route('user.deposit.form') }}" class="btn bp bsm">↓ Deposit USDT</a>
    <a href="{{ route('user.withdraw.form') }}" class="btn bg bsm">↑ Withdraw</a>
  </div>
</div>

{{-- Info strip --}}
<div style="background:#26A17B0a;border:1px solid #26A17B22;border-radius:10px;padding:10px 16px;margin-bottom:20px;font-size:13px;color:var(--muted)">
  💡 <strong style="color:#26A17B">Deposits</strong> are accepted in USDT only.
  <strong style="color:var(--accent)">Withdrawals</strong> can be sent as USDT, BTC, or ETH — always deducted from your USD balance.
</div>

{{-- Wallet cards (mobile) --}}
<div class="mob-list">
  @foreach($wallets as $wallet)
  @php
    $meta    = $coinMeta[$wallet->coin] ?? ['name'=>$wallet->coin,'icon'=>'?','color'=>'#fff','bg'=>'#ffffff11'];
    $isUsdt  = $wallet->coin === 'USDT';
    $prec    = $isUsdt ? 2 : 8;
    $avail   = number_format((float)$wallet->available, $prec);
    $locked  = number_format((float)$wallet->in_order, $prec);
    $total   = number_format((float)$wallet->available + (float)$wallet->in_order, $prec);
  @endphp
  <div class="card" style="margin-bottom:12px;{{ $isUsdt?'border-color:#26A17B33':'border-color:var(--border2)' }}">
    <div style="display:flex;align-items:center;gap:14px;margin-bottom:14px">
      <div style="width:46px;height:46px;border-radius:50%;background:{{ $meta['bg'] }};border:1px solid {{ $meta['color'] }}44;display:flex;align-items:center;justify-content:center;font-size:21px;font-weight:700;color:{{ $meta['color'] }}">{{ $meta['icon'] }}</div>
      <div style="flex:1">
        <div style="font-size:15px;font-weight:700">{{ $meta['name'] }}</div>
        <div style="font-size:12px;color:var(--muted)">{{ $wallet->coin }}</div>
      </div>
      @if($isUsdt)
      <span style="font-size:10px;font-weight:700;background:#26A17B18;color:#26A17B;border:1px solid #26A17B33;padding:3px 9px;border-radius:20px;letter-spacing:.04em">DEPOSIT COIN</span>
      @endif
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;text-align:center">
      <div style="background:var(--border);border-radius:8px;padding:10px">
        <div style="font-size:9.5px;color:var(--dim);margin-bottom:4px;text-transform:uppercase">Available</div>
        <div class="mono" style="font-size:13px;font-weight:600;color:var(--green)">{{ $avail }}</div>
      </div>
      <div style="background:var(--border);border-radius:8px;padding:10px">
        <div style="font-size:9.5px;color:var(--dim);margin-bottom:4px;text-transform:uppercase">Locked</div>
        <div class="mono" style="font-size:13px;font-weight:600;color:{{ (float)$wallet->in_order>0?'var(--yellow)':'var(--muted)' }}">{{ $locked }}</div>
      </div>
      <div style="background:{{ $meta['bg'] }};border:1px solid {{ $meta['color'] }}22;border-radius:8px;padding:10px">
        <div style="font-size:9.5px;color:var(--dim);margin-bottom:4px;text-transform:uppercase">Total</div>
        <div class="mono" style="font-size:13px;font-weight:600;color:{{ $meta['color'] }}">{{ $total }}</div>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- Desktop table --}}
<div class="card tw desk-tbl">
  <table>
    <thead>
      <tr style="background:#040f1c">
        <th>Coin</th>
        <th>Available</th>
        <th>Locked (In Order)</th>
        <th>Total Balance</th>
        <th>Role</th>
      </tr>
    </thead>
    <tbody>
      @foreach($wallets as $wallet)
      @php
        $meta   = $coinMeta[$wallet->coin] ?? ['name'=>$wallet->coin,'icon'=>'?','color'=>'#fff','bg'=>'#ffffff11'];
        $isUsdt = $wallet->coin === 'USDT';
        $prec   = $isUsdt ? 2 : 8;
      @endphp
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:10px">
            <div style="width:34px;height:34px;border-radius:50%;background:{{ $meta['bg'] }};border:1px solid {{ $meta['color'] }}44;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:{{ $meta['color'] }}">{{ $meta['icon'] }}</div>
            <div>
              <div style="font-size:14px;font-weight:600">{{ $meta['name'] }}</div>
              <div style="font-size:11px;color:var(--muted)">{{ $wallet->coin }}</div>
            </div>
          </div>
        </td>
        <td><span class="mono" style="color:var(--green);font-weight:600">{{ number_format((float)$wallet->available,$prec) }}</span></td>
        <td><span class="mono" style="color:{{ (float)$wallet->in_order>0?'var(--yellow)':'var(--muted)' }}">{{ number_format((float)$wallet->in_order,$prec) }}</span></td>
        <td><span class="mono" style="font-weight:700;color:{{ $meta['color'] }}">{{ number_format((float)$wallet->available+(float)$wallet->in_order,$prec) }}</span></td>
        <td>
          @if($isUsdt)
          <span style="font-size:11px;font-weight:700;background:#26A17B18;color:#26A17B;border:1px solid #26A17B33;padding:3px 10px;border-radius:20px">Deposit + Withdraw</span>
          @else
          <span style="font-size:11px;color:var(--dim)">Withdraw only</span>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
