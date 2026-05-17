@extends('layouts.wallet')
@section('title','Transactions — NEXUS Exchange')
@section('page-title','Transaction History')

@section('content')
<div class="sh"><h2>All Transactions</h2></div>

<div class="mob-list">
  @forelse($transactions as $t)
  @php $meta = $coinMeta[$t->coin] ?? ['name'=>$t->coin,'color'=>'#fff']; @endphp
  <div class="card" style="margin-bottom:10px">
    <div style="display:flex;align-items:center;gap:12px">
      <div style="width:40px;height:40px;border-radius:50%;background:{{ $t->isCredit()?'#00e5a022':'#ff525222' }};display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:{{ $t->type_color }};flex-shrink:0">{{ $t->type_icon }}</div>
      <div style="flex:1;min-width:0">
        <div style="font-size:14px;font-weight:600;text-transform:capitalize">{{ $t->type }}</div>
        <div style="font-size:12px;color:var(--muted)">{{ Str::limit($t->description,40) }}</div>
        <div style="font-size:11px;color:var(--dim)">{{ $t->created_at->format('M d, Y H:i') }}</div>
      </div>
      <div style="text-align:right">
        <div class="mono" style="font-size:14px;font-weight:700;color:{{ $t->isCredit()?'var(--green)':'var(--red)' }}">
          {{ $t->isCredit()?'+':'-' }}{{ number_format((float)$t->amount,8) }}
        </div>
        <div style="font-size:11px;color:var(--muted)">{{ $t->coin }}</div>
      </div>
    </div>
  </div>
  @empty
  <div class="card" style="text-align:center;padding:40px;color:var(--muted)">
    <div style="font-size:40px;margin-bottom:12px">↕</div>
    No transactions yet
  </div>
  @endforelse
</div>

<div class="card tw desk-tbl">
  @if($transactions->count())
  <table>
    <thead>
      <tr><th>Type</th><th>Description</th><th>Coin</th><th>Amount</th><th>Balance After</th><th>Date</th></tr>
    </thead>
    <tbody>
      @foreach($transactions as $t)
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:8px">
            <span style="color:{{ $t->type_color }};font-size:16px">{{ $t->type_icon }}</span>
            <span style="text-transform:capitalize;font-weight:600;font-size:13px">{{ $t->type }}</span>
          </div>
        </td>
        <td style="font-size:13px;color:var(--muted);max-width:200px">{{ Str::limit($t->description,50) }}</td>
        <td style="font-weight:600">{{ $t->coin }}</td>
        <td>
          <span class="mono" style="font-weight:700;color:{{ $t->isCredit()?'var(--green)':'var(--red)' }}">
            {{ $t->isCredit()?'+':'-' }}{{ number_format((float)$t->amount,8) }}
          </span>
        </td>
        <td><span class="mono" style="color:var(--muted);font-size:12px">{{ number_format((float)$t->balance_after,8) }}</span></td>
        <td style="color:var(--muted);font-size:13px;white-space:nowrap">{{ $t->created_at->format('M d, Y H:i') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div style="text-align:center;padding:48px;color:var(--muted)">No transactions yet</div>
  @endif
</div>

@if($transactions->hasPages())
<div class="pg">{!! $transactions->links()->toHtml() !!}</div>
@endif
@endsection
