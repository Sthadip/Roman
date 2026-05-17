@extends('layouts.wallet')
@section('title','Withdrawals — Admin')
@section('page-title','Withdrawals')
@section('content')

<div class="card" style="margin-bottom:18px">
  <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
    <div style="min-width:130px"><label class="fl">Status</label>
      <select name="status" class="fi">
        <option value="">All Status</option>
        <option value="pending"  {{ request('status')==='pending' ?'selected':'' }}>Pending</option>
        <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Approved</option>
        <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Rejected</option>
      </select>
    </div>
    <div style="min-width:120px"><label class="fl">Coin</label>
      <select name="coin" class="fi">
        <option value="">All Coins</option>
        @foreach($coinMeta as $code => $meta)
        <option value="{{ $code }}" {{ request('coin')===$code?'selected':'' }}>{{ $code }}</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn bp bsm">Filter</button>
    <a href="{{ route('admin.withdrawals') }}" class="btn bg bsm">Clear</a>
  </form>
</div>

<div class="card tw desk-tbl">
  @if($withdrawals->count())
  <table>
    <thead>
      <tr style="background:#040f1c">
        <th>User</th>
        <th>USDT Deducted</th>
        <th>Send to User</th>
        <th>Rate</th>
        <th>Destination</th>
        <th>Date</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach($withdrawals as $w)
      @php $m = $coinMeta[$w->coin] ?? ['color'=>'#fff','icon'=>'?']; @endphp
      <tr>
        <td>
          <div style="font-weight:600">{{ $w->user->name }}</div>
          <div style="font-size:11.5px;color:var(--muted)">{{ $w->user->email }}</div>
        </td>
        {{-- USDT deducted from user's account --}}
        <td>
          <span class="mono" style="font-weight:700;color:#26A17B">
            ${{ number_format((float)($w->usdt_amount ?: $w->amount),2) }}
          </span>
        </td>
        {{-- Coin + amount admin must send --}}
        <td>
          <span style="color:{{ $m['color'] }};font-weight:700;font-size:15px">{{ $m['icon'] }}</span>
          <span class="mono" style="font-weight:700;color:{{ $m['color'] }}">
            {{ number_format((float)($w->coin_amount ?: $w->amount), $w->coin==='USDT'?2:8) }}
          </span>
          <span style="font-size:12px;color:var(--muted)">{{ $w->coin }}</span>
        </td>
        {{-- Rate used --}}
        <td>
          @if($w->isCryptoWithdrawal() && $w->rate_used)
          <span class="mono" style="font-size:12px;color:var(--dim)">${{ number_format((float)$w->rate_used,0) }}/{{ $w->coin }}</span>
          @else<span style="color:var(--dim)">—</span>@endif
        </td>
        <td>
          <span class="mono" style="font-size:12px">{{ Str::limit($w->wallet_address,20) }}</span>
          @if($w->network)<div style="font-size:11px;color:var(--dim)">{{ $w->network }}</div>@endif
        </td>
        <td style="font-size:12.5px;color:var(--muted)">{{ $w->created_at->format('M d, Y') }}</td>
        <td><span class="badge badge-{{ $w->status }}">{{ ucfirst($w->status) }}</span></td>
        <td>
          @if($w->isPending())
          <div style="display:flex;gap:6px">
            <form method="POST" action="{{ route('admin.withdrawals.approve',$w->id) }}">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-green bsm">✓ Approve</button>
            </form>
            <form method="POST" action="{{ route('admin.withdrawals.reject',$w->id) }}">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-red bsm">✕ Reject</button>
            </form>
          </div>
          @else
          <div style="font-size:12px;color:var(--dim)">Reviewed {{ $w->reviewed_at?->diffForHumans() }}</div>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div style="text-align:center;padding:48px;color:var(--muted)">No withdrawals found.</div>
  @endif
</div>

{{-- Mobile --}}
<div class="mob-list">
  @forelse($withdrawals as $w)
  @php $m = $coinMeta[$w->coin] ?? ['color'=>'#fff','icon'=>'?']; @endphp
  <div class="card" style="margin-bottom:10px">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px">
      <div>
        <div style="font-weight:700">{{ $w->user->name }}</div>
        <div style="font-size:12px;color:var(--muted)">{{ $w->created_at->diffForHumans() }}</div>
      </div>
      <span class="badge badge-{{ $w->status }}">{{ ucfirst($w->status) }}</span>
    </div>
    {{-- Conversion breakdown --}}
    <div style="display:flex;align-items:center;gap:8px;padding:10px 12px;background:#040f1c;border:1px solid var(--border2);border-radius:10px;margin-bottom:10px">
      <div style="text-align:center;flex:1">
        <div style="font-size:10px;color:var(--dim);margin-bottom:2px">DEDUCT</div>
        <div class="mono" style="font-weight:700;color:#26A17B">${{ number_format((float)($w->usdt_amount ?: $w->amount),2) }}</div>
      </div>
      <div style="color:var(--dim);font-size:18px">→</div>
      <div style="text-align:center;flex:1">
        <div style="font-size:10px;color:var(--dim);margin-bottom:2px">SEND</div>
        <div class="mono" style="font-weight:700;color:{{ $m['color'] }}">{{ $m['icon'] }} {{ number_format((float)($w->coin_amount ?: $w->amount),$w->coin==='USDT'?2:8) }} {{ $w->coin }}</div>
      </div>
    </div>
    <div class="mono" style="font-size:11.5px;color:var(--muted);margin-bottom:10px;word-break:break-all">{{ $w->wallet_address }}</div>
    @if($w->isPending())
    <div style="display:flex;gap:8px">
      <form method="POST" action="{{ route('admin.withdrawals.approve',$w->id) }}" style="flex:1">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-green" style="width:100%">✓ Approve</button>
      </form>
      <form method="POST" action="{{ route('admin.withdrawals.reject',$w->id) }}" style="flex:1">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-red" style="width:100%">✕ Reject</button>
      </form>
    </div>
    @endif
  </div>
  @empty
  <div class="card" style="text-align:center;padding:40px;color:var(--muted)">No withdrawals found.</div>
  @endforelse
</div>

@if($withdrawals->hasPages())<div class="pg">{!! $withdrawals->links()->toHtml() !!}</div>@endif
@endsection
