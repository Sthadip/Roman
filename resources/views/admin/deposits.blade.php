@extends('layouts.wallet')
@section('title','Deposits — Admin')
@section('page-title','Deposits')
@section('content')

{{-- Stats bar --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:22px">
  <div class="sc">
    <div class="sc-icon" style="color:var(--yellow)">⏳</div>
    <div class="sc-val" style="color:var(--yellow)">{{ $counts['pending'] }}</div>
    <div class="sc-lbl">Pending Review</div>
  </div>
  <div class="sc">
    <div class="sc-icon" style="color:var(--green)">✓</div>
    <div class="sc-val" style="color:var(--green)">{{ $counts['confirmed'] }}</div>
    <div class="sc-lbl">Confirmed</div>
  </div>
  <div class="sc">
    <div class="sc-icon" style="color:var(--red)">✕</div>
    <div class="sc-val" style="color:var(--red)">{{ $counts['rejected'] }}</div>
    <div class="sc-lbl">Rejected</div>
  </div>
</div>

{{-- Filters --}}
<div class="card" style="margin-bottom:20px">
  <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
    <div style="flex:2;min-width:180px">
      <label class="fl">Search User / TXN ID</label>
      <input type="text" name="search" class="fi" placeholder="Name, email or transaction ID…" value="{{ request('search') }}">
    </div>
    <div style="min-width:140px">
      <label class="fl">Network</label>
      <select name="network" class="fi">
        <option value="">All Networks</option>
        @foreach($networkMeta as $ticker => $meta)
        <option value="{{ $ticker }}" {{ request('network')===$ticker?'selected':'' }}>{{ $meta['icon'] }} {{ $meta['name'] }} ({{ $ticker }})</option>
        @endforeach
      </select>
    </div>
    <div style="min-width:140px">
      <label class="fl">Status</label>
      <select name="status" class="fi">
        <option value="">All Statuses</option>
        <option value="pending"   {{ request('status')==='pending'  ?'selected':'' }}>⏳ Pending</option>
        <option value="confirmed" {{ request('status')==='confirmed'?'selected':'' }}>✓ Confirmed</option>
        <option value="rejected"  {{ request('status')==='rejected' ?'selected':'' }}>✕ Rejected</option>
      </select>
    </div>
    <button type="submit" class="btn bp bsm">Filter</button>
    <a href="{{ route('admin.deposits') }}" class="btn bg bsm">Clear</a>
  </form>
</div>

{{-- Desktop table --}}
<div class="card tw desk-tbl">
  @if($deposits->count())
  <table>
    <thead>
      <tr style="background:#040f1c">
        <th>User</th>
        <th>Amount (USDT)</th>
        <th>Network</th>
        <th>Transaction ID</th>
        <th>Proof</th>
        <th>Submitted</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach($deposits as $d)
      @php
        $nm = $networkMeta[$d->network] ?? ['icon'=>$d->network,'color'=>'#5a8aa0','bg'=>'#5a8aa022','name'=>$d->network];
      @endphp
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:9px">
            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#030a12;overflow:hidden;flex-shrink:0">
              @if($d->user->avatar)<img src="{{ $d->user->avatar }}" style="width:100%;height:100%;object-fit:cover">
              @else{{ strtoupper(substr($d->user->name,0,1)) }}@endif
            </div>
            <div>
              <div style="font-weight:600;font-size:13.5px">{{ $d->user->name }}</div>
              <div style="font-size:11.5px;color:var(--muted)">{{ $d->user->email }}</div>
            </div>
          </div>
        </td>
        <td>
          {{-- Always USDT --}}
          <div style="display:flex;align-items:baseline;gap:3px">
            <span style="color:#26A17B;font-size:15px;font-weight:800">$</span>
            <span class="mono" style="font-size:15px;font-weight:700;color:#26A17B">{{ number_format((float)$d->amount, 2) }}</span>
          </div>
          <div style="font-size:10.5px;color:var(--dim);margin-top:1px">USDT · via {{ $d->network }}</div>
        </td>
        <td>
          {{-- Network used to send --}}
          <div style="display:flex;align-items:center;gap:7px">
            <div style="width:26px;height:26px;border-radius:50%;background:{{ $nm['bg'] }};border:1px solid {{ $nm['color'] }}33;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:{{ $nm['color'] }}">{{ $nm['icon'] }}</div>
            <div>
              <div style="font-size:13px;font-weight:700;color:{{ $nm['color'] }}">{{ $d->network }}</div>
              <div style="font-size:10.5px;color:var(--muted)">{{ $nm['name'] }}</div>
            </div>
          </div>
        </td>
        <td>
          <span class="mono" style="font-size:12px;color:var(--muted)">{{ Str::limit($d->transaction_id,22) }}</span>
        </td>
        <td>
          @if($d->screenshot_path)
          <a href="{{ Storage::url($d->screenshot_path) }}" target="_blank"
             style="display:inline-flex;align-items:center;gap:4px;font-size:12px;color:var(--accent)">
            📎 View
          </a>
          @else
          <span style="font-size:12px;color:var(--dim)">—</span>
          @endif
        </td>
        <td>
          <div style="font-size:13px">{{ $d->created_at->format('M d, Y') }}</div>
          <div style="font-size:11px;color:var(--muted)">{{ $d->created_at->format('h:i A') }}</div>
        </td>
        <td><span class="badge badge-{{ $d->status }}">{{ ucfirst($d->status) }}</span></td>
        <td>
          <div style="display:flex;gap:6px;flex-wrap:wrap">
            <a href="{{ route('admin.deposits.detail', $d->id) }}" class="btn bg bsm">Details</a>
            @if($d->isPending())
            <form method="POST" action="{{ route('admin.deposits.approve',$d->id) }}">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-green bsm">✓</button>
            </form>
            <form method="POST" action="{{ route('admin.deposits.reject',$d->id) }}">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-red bsm" onclick="return confirm('Reject this deposit?')">✕</button>
            </form>
            @endif
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div style="text-align:center;padding:56px;color:var(--muted)">
    <div style="font-size:36px;margin-bottom:12px;opacity:.3">↓</div>
    <div style="font-size:15px;font-weight:600;margin-bottom:6px">No deposits found</div>
    <div style="font-size:13px">Try adjusting the filters above</div>
  </div>
  @endif
</div>

{{-- Mobile cards --}}
<div class="mob-list">
  @forelse($deposits as $d)
  @php $nm = $networkMeta[$d->network] ?? ['icon'=>$d->network,'color'=>'#5a8aa0','bg'=>'#5a8aa022','name'=>$d->network]; @endphp
  <div class="card" style="margin-bottom:12px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
      <div style="display:flex;align-items:center;gap:10px">
        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:#030a12;overflow:hidden;flex-shrink:0">
          @if($d->user->avatar)<img src="{{ $d->user->avatar }}" style="width:100%;height:100%;object-fit:cover">
          @else{{ strtoupper(substr($d->user->name,0,1)) }}@endif
        </div>
        <div>
          <div style="font-weight:700">{{ $d->user->name }}</div>
          <div style="font-size:12px;color:var(--muted)">{{ $d->created_at->diffForHumans() }}</div>
        </div>
      </div>
      <span class="badge badge-{{ $d->status }}">{{ ucfirst($d->status) }}</span>
    </div>

    {{-- USDT amount hero --}}
    <div style="background:#26A17B0a;border:1px solid #26A17B22;border-radius:10px;padding:12px 14px;margin-bottom:10px;display:flex;align-items:center;justify-content:space-between">
      <div>
        <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Amount (USDT)</div>
        <div style="display:flex;align-items:baseline;gap:3px">
          <span style="font-size:19px;font-weight:900;color:#26A17B">$</span>
          <span class="mono" style="font-size:19px;font-weight:900;color:#26A17B">{{ number_format((float)$d->amount, 2) }}</span>
        </div>
      </div>
      {{-- Network badge --}}
      <div style="display:flex;align-items:center;gap:6px;background:{{ $nm['bg'] }};border:1px solid {{ $nm['color'] }}33;border-radius:8px;padding:6px 10px">
        <span style="font-size:15px;color:{{ $nm['color'] }}">{{ $nm['icon'] }}</span>
        <div>
          <div style="font-size:12px;font-weight:700;color:{{ $nm['color'] }}">{{ $d->network }}</div>
          <div style="font-size:10px;color:var(--muted)">Network</div>
        </div>
      </div>
    </div>

    @if($d->transaction_id)
    <div class="mono" style="font-size:11px;color:var(--dim);margin-bottom:10px;word-break:break-all">{{ Str::limit($d->transaction_id,40) }}</div>
    @endif

    <div style="display:flex;gap:7px">
      <a href="{{ route('admin.deposits.detail', $d->id) }}" class="btn bg bsm" style="flex:1;justify-content:center">Details</a>
      @if($d->isPending())
      <form method="POST" action="{{ route('admin.deposits.approve',$d->id) }}" style="flex:1">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-green bsm" style="width:100%">✓ Approve</button>
      </form>
      <form method="POST" action="{{ route('admin.deposits.reject',$d->id) }}" style="flex:1">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-red bsm" style="width:100%" onclick="return confirm('Reject?')">✕ Reject</button>
      </form>
      @endif
    </div>
  </div>
  @empty
  <div class="card" style="text-align:center;padding:40px;color:var(--muted)">No deposits found.</div>
  @endforelse
</div>

@if($deposits->hasPages())<div class="pg">{!! $deposits->links()->toHtml() !!}</div>@endif
@endsection
