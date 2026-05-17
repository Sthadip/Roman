@extends('layouts.wallet')
@section('title','Admin Dashboard — NEXUS')
@section('page-title','Admin Dashboard')
@section('content')

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:20px">
  <div class="sc"><div class="sc-icon" style="color:var(--green)">↓</div><div class="sc-val" style="color:var(--green)">${{ number_format((float)$stats['confirmed_deposits_sum'],2) }}</div><div class="sc-lbl">Confirmed Deposits</div><div style="font-size:11px;color:var(--dim)">{{ $stats['confirmed_deposits_count'] }} confirmed</div></div>
  <div class="sc"><div class="sc-icon" style="color:var(--yellow)">⏳</div><div class="sc-val" style="color:var(--yellow)">{{ $stats['pending_deposits_count'] }}</div><div class="sc-lbl">Pending Deposits</div></div>
  <div class="sc"><div class="sc-icon" style="color:var(--red)">↑</div><div class="sc-val" style="color:var(--red)">${{ number_format((float)$stats['confirmed_withdrawals_sum'],2) }}</div><div class="sc-lbl">Confirmed Withdrawals</div><div style="font-size:11px;color:var(--dim)">{{ $stats['confirmed_withdrawals_count'] }} approved</div></div>
  <div class="sc"><div class="sc-icon" style="color:var(--yellow)">🕐</div><div class="sc-val" style="color:var(--yellow)">{{ $stats['pending_withdrawals_count'] }}</div><div class="sc-lbl">Pending Withdrawals</div></div>
  <div class="sc"><div class="sc-icon" style="color:var(--accent)">👥</div><div class="sc-val" style="color:var(--accent)">{{ $stats['total_users'] }}</div><div class="sc-lbl">Total Users</div></div>
  <div class="sc"><div class="sc-icon" style="color:var(--purple)">✔</div><div class="sc-val" style="color:var(--purple)">{{ $stats['pending_kyc'] }}</div><div class="sc-lbl">Pending KYC</div></div>
</div>

<div class="tc">
  <div class="tc-main">

    {{-- Online users --}}
    @if($onlineUsers->count())
    <div class="card" style="margin-bottom:18px;border-color:#00e5ff22">
      <div class="sh">
        <h2 style="display:flex;align-items:center;gap:8px">
          <span style="width:9px;height:9px;border-radius:50%;background:var(--green);display:inline-block;box-shadow:0 0 0 3px #00e5a033"></span>
          Live — {{ $onlineUsers->count() }} Online Now
        </h2>
        <a href="{{ route('admin.users') }}" class="btn bg bsm">All Users</a>
      </div>
      @foreach($onlineUsers as $act)
      <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border)">
        <div style="position:relative;width:34px;height:34px;flex-shrink:0">
          <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#030a12;overflow:hidden">
            @if($act->user && $act->user->avatar)<img src="{{ $act->user->avatar }}" style="width:100%;height:100%;object-fit:cover">
            @else{{ strtoupper(substr($act->user->name ?? '?', 0, 1)) }}@endif
          </div>
          <div style="position:absolute;bottom:0;right:0;width:9px;height:9px;border-radius:50%;background:var(--green);border:2px solid var(--surface)"></div>
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13.5px;font-weight:600">{{ $act->user->name ?? 'Unknown' }}</div>
          <div style="font-size:12px;color:var(--accent)">● {{ $act->page }}</div>
        </div>
        <div style="text-align:right;font-size:11.5px;color:var(--muted)">{{ $act->last_seen_at->diffForHumans() }}</div>
        <a href="{{ route('admin.users.detail', $act->user_id) }}" class="btn bg bsm">View</a>
      </div>
      @endforeach
    </div>
    @endif

    {{-- Pending deposits --}}
    <div class="card" style="margin-bottom:18px">
      <div class="sh"><h2>Pending Deposits</h2><a href="{{ route('admin.deposits',['status'=>'pending']) }}" class="btn bg bsm">View All</a></div>
      @forelse($pendingDeposits as $d)
      <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border)">
        <div style="width:34px;height:34px;border-radius:50%;background:#00e5a018;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--green);flex-shrink:0">$</div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13.5px;font-weight:600">{{ $d->user->name }}</div>
          <div style="font-size:12px;color:var(--muted)">{{ $d->created_at->diffForHumans() }}</div>
        </div>
        <div class="mono" style="font-weight:600;color:var(--green);margin-right:8px">{{ number_format((float)$d->amount,2) }} {{ $d->coin }}</div>
        <a href="{{ route('admin.deposits.detail',$d->id) }}" class="btn bg bsm">Detail</a>
      </div>
      @empty<div style="text-align:center;padding:24px;color:var(--muted);font-size:13.5px">No pending deposits 🎉</div>@endforelse
    </div>

    {{-- Pending withdrawals --}}
    <div class="card" style="margin-bottom:18px">
      <div class="sh"><h2>Pending Withdrawals</h2><a href="{{ route('admin.withdrawals',['status'=>'pending']) }}" class="btn bg bsm">View All</a></div>
      @forelse($pendingWithdrawals as $w)
      <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border)">
        <div style="width:34px;height:34px;border-radius:50%;background:#ff525218;display:flex;align-items:center;justify-content:center;font-size:15px;color:var(--red);flex-shrink:0">↑</div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13.5px;font-weight:600">{{ $w->user->name }}</div>
          <div class="mono" style="font-size:11px;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $w->wallet_address }}</div>
        </div>
        <div class="mono" style="font-weight:600;color:var(--red);margin-right:8px">{{ number_format((float)$w->amount,6) }} {{ $w->coin }}</div>
        <div style="display:flex;gap:6px">
          <form method="POST" action="{{ route('admin.withdrawals.approve',$w->id) }}">@csrf @method('PATCH')<button type="submit" class="btn btn-green bsm">✓</button></form>
          <form method="POST" action="{{ route('admin.withdrawals.reject',$w->id) }}">@csrf @method('PATCH')<button type="submit" class="btn btn-red bsm">✕</button></form>
        </div>
      </div>
      @empty<div style="text-align:center;padding:24px;color:var(--muted);font-size:13.5px">No pending withdrawals 🎉</div>@endforelse
    </div>

    {{-- Recent notifications --}}
    <div class="card">
      <div class="sh">
        <h2>Recent Notifications</h2>
        <a href="{{ auth()->user()->isSuperAdmin() ? route('superadmin.notifications') : route('admin.notifications') }}" class="btn bg bsm">View All</a>
      </div>
      @forelse($notifications->take(5) as $n)
      <div style="display:flex;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);{{ !$n->is_read?'':'opacity:.65' }}">
        <div style="font-size:18px;flex-shrink:0;margin-top:2px">{{ $n->type==='new_user'?'👤':'🔑' }}</div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13.5px;font-weight:{{ !$n->is_read?'700':'500' }}">{{ $n->title }}</div>
          <div style="font-size:12px;color:var(--muted)">{{ $n->body }}</div>
          <div style="font-size:11px;color:var(--dim);margin-top:2px">{{ $n->created_at->diffForHumans() }}</div>
        </div>
        @if(!$n->is_read)<div style="width:7px;height:7px;border-radius:50%;background:var(--accent);flex-shrink:0;margin-top:6px"></div>@endif
      </div>
      @empty<div style="text-align:center;padding:24px;color:var(--muted);font-size:13.5px">No notifications yet.</div>@endforelse
    </div>

  </div>

  <div class="tc-side">
    <div class="card" style="margin-bottom:16px">
      <div class="sh"><h2>Quick Links</h2></div>
      <div style="display:flex;flex-direction:column;gap:7px">
        <a href="{{ route('admin.deposits',['status'=>'pending']) }}" class="btn bg" style="width:100%">↓ Review Deposits ({{ $stats['pending_deposits_count'] }})</a>
        <a href="{{ route('admin.withdrawals',['status'=>'pending']) }}" class="btn bg" style="width:100%">↑ Review Withdrawals ({{ $stats['pending_withdrawals_count'] }})</a>
        <a href="{{ route('admin.kyc',['status'=>'pending']) }}" class="btn bg" style="width:100%">✔ Review KYC ({{ $stats['pending_kyc'] }})</a>
        <a href="{{ route('admin.users') }}" class="btn bg" style="width:100%">👥 Users</a>      </div>
    </div>
    <div class="card">
      <div class="sh"><h2>Recent Users</h2></div>
      @foreach($recentUsers as $u)
      <div style="display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid var(--border)">
        <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#030a12;overflow:hidden;flex-shrink:0">
          @if($u->avatar)<img src="{{ $u->avatar }}" style="width:100%;height:100%;object-fit:cover">@else{{ strtoupper(substr($u->name,0,1)) }}@endif
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $u->name }}</div>
          <div style="font-size:11px;color:var(--muted)">{{ $u->created_at->diffForHumans() }}</div>
        </div>
        <a href="{{ route('admin.users.detail',$u->id) }}" class="btn bg bsm" style="padding:4px 8px;font-size:11px">View</a>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endsection
