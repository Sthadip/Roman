@extends('layouts.wallet')
@section('title','Investments — Admin')
@section('page-title','Investments Overview')
@section('content')
<div class="card" style="margin-bottom:18px">
  <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
    <div style="flex:1;min-width:120px"><label class="fl">Status</label>
      <select name="status" class="fi">
        <option value="">All</option>
        <option value="active" {{ request('status')==='active'?'selected':'' }}>Active</option>
        <option value="completed" {{ request('status')==='completed'?'selected':'' }}>Completed</option>
      </select>
    </div>
    <button type="submit" class="btn bp bsm">Filter</button>
    <a href="{{ route('admin.investments') }}" class="btn bg bsm">Clear</a>
  </form>
</div>
<div class="card tw desk-tbl">
  @if($investments->count())
  <table>
    <thead><tr style="background:#040f1c"><th>User</th><th>Package</th><th>Amount</th><th>Profit</th><th>Matures</th><th>Status</th></tr></thead>
    <tbody>
      @foreach($investments as $inv)
      <tr>
        <td><div style="font-weight:600">{{ $inv->user->name }}</div><div style="font-size:11.5px;color:var(--muted)">{{ $inv->user->email }}</div></td>
        <td>{{ $inv->package->name ?? 'N/A' }}</td>
        <td><span class="mono" style="color:var(--accent);font-weight:600">${{ number_format((float)$inv->amount,2) }}</span></td>
        <td><span class="mono" style="color:var(--green);font-weight:600">+${{ number_format((float)$inv->profit,2) }}</span></td>
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
@if($investments->hasPages())<div class="pg">{!! $investments->links()->toHtml() !!}</div>@endif
@endsection
