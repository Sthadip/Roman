@extends('layouts.wallet')
@section('title','All Investments — NEXUS')
@section('page-title','All Investments')
@section('content')

<div class="card" style="margin-bottom:18px">
  <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
    <div style="flex:1;min-width:120px"><label class="fl">Status</label>
      <select name="status" class="fi">
        <option value="">All</option>
        <option value="active" {{ request('status')==='active'?'selected':'' }}>Active</option>
        <option value="completed" {{ request('status')==='completed'?'selected':'' }}>Completed</option>
        <option value="cancelled" {{ request('status')==='cancelled'?'selected':'' }}>Cancelled</option>
      </select>
    </div>
    <button type="submit" class="btn bp bsm">Filter</button>
    <a href="{{ route('superadmin.investments') }}" class="btn bg bsm">Clear</a>
    <form method="POST" action="{{ route('superadmin.process-investments') }}" style="margin:0">
      @csrf
      <button type="submit" class="btn btn-green bsm">⚡ Process Matured</button>
    </form>
  </form>
</div>

<div class="card tw desk-tbl">
  @if($investments->count())
  <table>
    <thead><tr style="background:#040f1c">
      <th>User</th><th>Package</th><th>Amount</th><th>Profit</th><th>Return</th><th>Starts</th><th>Matures</th><th>Status</th>
    </tr></thead>
    <tbody>
      @foreach($investments as $inv)
      <tr>
        <td>
          <div style="font-weight:600">{{ $inv->user->name }}</div>
          <div style="font-size:11.5px;color:var(--muted)">{{ $inv->user->email }}</div>
        </td>
        <td><span style="font-weight:600">{{ $inv->package->name ?? 'N/A' }}</span></td>
        <td><span class="mono" style="color:var(--accent);font-weight:600">${{ number_format((float)$inv->amount,2) }}</span></td>
        <td><span class="mono" style="color:var(--green);font-weight:600">+${{ number_format((float)$inv->profit,2) }}</span></td>
        <td><span class="mono" style="font-weight:700">${{ number_format((float)$inv->expected_return,2) }}</span></td>
        <td style="font-size:12.5px">{{ $inv->starts_at->format('M d, Y') }}</td>
        <td style="font-size:12.5px">{{ $inv->ends_at->format('M d, Y') }}</td>
        <td><span class="badge badge-{{ $inv->status }}">{{ ucfirst($inv->status) }}</span></td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div style="text-align:center;padding:48px;color:var(--muted)">No investments found.</div>
  @endif
</div>

{{-- Mobile --}}
<div class="mob-list">
  @forelse($investments as $inv)
  <div class="card" style="margin-bottom:10px">
    <div style="display:flex;justify-content:space-between;margin-bottom:8px">
      <div><div style="font-weight:700">{{ $inv->user->name }}</div><div style="font-size:12px;color:var(--muted)">{{ $inv->package->name ?? 'N/A' }}</div></div>
      <span class="badge badge-{{ $inv->status }}">{{ ucfirst($inv->status) }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:13px">
      <span style="color:var(--muted)">Invested</span><span class="mono" style="color:var(--accent);font-weight:600">${{ number_format((float)$inv->amount,2) }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:13px">
      <span style="color:var(--muted)">Return</span><span class="mono" style="color:var(--green);font-weight:600">${{ number_format((float)$inv->expected_return,2) }}</span>
    </div>
  </div>
  @empty
  <div class="card" style="text-align:center;padding:40px;color:var(--muted)">No investments found.</div>
  @endforelse
</div>

@if($investments->hasPages())<div class="pg">{!! $investments->links()->toHtml() !!}</div>@endif
@endsection
