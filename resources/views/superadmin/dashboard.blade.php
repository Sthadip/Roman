@extends('layouts.wallet')
@section('title','Super Admin — NEXUS')
@section('page-title','Super Admin Dashboard')
@section('content')

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:22px">
  <div class="sc"><div class="sc-icon" style="color:var(--accent)">👥</div><div class="sc-val" style="color:var(--accent)">{{ $stats['total_users'] }}</div><div class="sc-lbl">Total Users</div></div>
  <div class="sc"><div class="sc-icon" style="color:var(--purple)">🛡</div><div class="sc-val" style="color:var(--purple)">{{ $stats['total_admins'] }}</div><div class="sc-lbl">Total Admins</div></div>
  <div class="sc"><div class="sc-icon" style="color:var(--green)">↓</div><div class="sc-val" style="color:var(--green)">${{ number_format((float)$stats['confirmed_deposits'],2) }}</div><div class="sc-lbl">Total Deposits</div><div style="font-size:11px;color:var(--dim)">{{ $stats['pending_deposits'] }} pending</div></div>
  <div class="sc"><div class="sc-icon" style="color:var(--red)">↑</div><div class="sc-val" style="color:var(--red)">${{ number_format((float)$stats['confirmed_withdrawals'],2) }}</div><div class="sc-lbl">Total Withdrawals</div><div style="font-size:11px;color:var(--dim)">{{ $stats['pending_withdrawals'] }} pending</div></div>
</div>

<div class="tc">
  <div class="tc-main">
    <div class="card" style="margin-bottom:18px">
      <div class="sh"><h2>Admin Accounts</h2><a href="{{ route('superadmin.admins') }}" class="btn bg bsm">Manage</a></div>
      @foreach($admins as $a)
      <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border)">
        <div style="width:36px;height:36px;border-radius:50%;background:{{ $a->isSuperAdmin()?'linear-gradient(135deg,#ff6d99,#ff9eb5)':'linear-gradient(135deg,var(--purple),#b390ff)' }};display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:#fff;overflow:hidden;flex-shrink:0">
          @if($a->avatar)<img src="{{ $a->avatar }}" style="width:100%;height:100%;object-fit:cover">@else{{ strtoupper(substr($a->name,0,1)) }}@endif
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13.5px;font-weight:600">{{ $a->name }}</div>
          <div style="font-size:12px;color:var(--muted)">{{ $a->email }}</div>
        </div>
        <span class="badge badge-{{ $a->role }}">{{ $a->role_label }}</span>
      </div>
      @endforeach
    </div>

    <div class="card">
      <div class="sh"><h2>Recent Notifications</h2><a href="{{ route('superadmin.notifications') }}" class="btn bg bsm">All</a></div>
      @forelse($notifications as $n)
      <div style="display:flex;gap:10px;padding:10px 0;border-bottom:1px solid var(--border)">
        <div style="font-size:18px;flex-shrink:0">{{ $n->type==='new_user'?'👤':'🔑' }}</div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13.5px;font-weight:{{ !$n->is_read?'700':'500' }}">{{ $n->title }}</div>
          <div style="font-size:12px;color:var(--muted)">{{ $n->body }}</div>
          <div style="font-size:11px;color:var(--dim)">{{ $n->created_at->diffForHumans() }}</div>
        </div>
        @if(!$n->is_read)<div style="width:7px;height:7px;border-radius:50%;background:var(--accent);margin-top:6px;flex-shrink:0"></div>@endif
      </div>
      @empty<div style="padding:24px;text-align:center;color:var(--muted)">No notifications yet.</div>@endforelse
    </div>
  </div>

  <div class="tc-side">
    <div class="card" style="margin-bottom:16px">
      <div class="sh"><h2>Quick Actions</h2></div>
      <div style="display:flex;flex-direction:column;gap:7px">
        <a href="{{ route('superadmin.admins') }}" class="btn btn-purple" style="width:100%">👑 Manage Admins</a>
        <a href="{{ route('admin.dashboard') }}" class="btn bg" style="width:100%">⊞ Admin Panel</a>
        <a href="{{ route('admin.users') }}" class="btn bg" style="width:100%">👥 All Users</a>
        <a href="{{ route('admin.deposits') }}" class="btn bg" style="width:100%">↓ Deposits</a>
        <a href="{{ route('admin.withdrawals') }}" class="btn bg" style="width:100%">↑ Withdrawals</a>
        <a href="{{ route('superadmin.notifications') }}" class="btn bg" style="width:100%">
          🔔 Notifications @if($unreadCount)<span style="background:var(--red);color:#fff;border-radius:8px;padding:1px 6px;font-size:10px;font-weight:700;margin-left:4px">{{ $unreadCount }}</span>@endif
        </a>
      </div>
    </div>
    <div class="card" style="background:#7c4dff08;border-color:#7c4dff22">
      <div style="font-size:12px;font-weight:700;color:#b390ff;margin-bottom:8px;text-transform:uppercase;letter-spacing:.06em">Super Admin Access</div>
      <div style="font-size:12.5px;color:var(--muted);line-height:1.7">Full platform access including promoting/demoting admins and viewing all operations.</div>
    </div>
  </div>
</div>
@endsection
