@extends('layouts.wallet')
@section('title','Manage Admins — NEXUS')
@section('page-title','Manage Admins')
@section('content')
<div class="card" style="margin-bottom:18px">
  <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
    <div style="flex:1;min-width:200px"><label class="fl">Search</label><input type="text" name="search" class="fi" placeholder="Name or email..." value="{{ request('search') }}"></div>
    <button type="submit" class="btn bp bsm">Search</button>
    <a href="{{ route('superadmin.admins') }}" class="btn bg bsm">Clear</a>
  </form>
</div>
<div class="card">
  <div class="sh"><h2>Admin Accounts ({{ $admins->total() }})</h2></div>
  @forelse($admins as $a)
  <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border);flex-wrap:wrap">
    <div style="width:40px;height:40px;border-radius:50%;background:{{ $a->isSuperAdmin()?'linear-gradient(135deg,#ff6d99,#ff9eb5)':'linear-gradient(135deg,var(--purple),#b390ff)' }};display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#fff;overflow:hidden;flex-shrink:0">
      @if($a->avatar)<img src="{{ $a->avatar }}" style="width:100%;height:100%;object-fit:cover">@else{{ strtoupper(substr($a->name,0,1)) }}@endif
    </div>
    <div style="flex:1;min-width:0">
      <div style="font-size:14px;font-weight:700">{{ $a->name }}</div>
      <div style="font-size:12px;color:var(--muted)">{{ $a->email }}</div>
      <div style="font-size:11.5px;color:var(--dim)">Joined {{ $a->created_at->format('M d, Y') }}</div>
    </div>
    <span class="badge badge-{{ $a->role }}">{{ $a->role_label }}</span>
    @if(!$a->isSuperAdmin() && $a->id !== auth()->id())
    <form method="POST" action="{{ route('superadmin.demote',$a->id) }}">@csrf @method('PATCH')
      <button type="submit" class="btn btn-red bsm" onclick="return confirm('Demote {{ addslashes($a->name) }} to User?')">Demote to User</button>
    </form>
    @elseif($a->id === auth()->id())
    <span style="font-size:12px;color:var(--dim)">(you)</span>
    @endif
  </div>
  @empty<div style="text-align:center;padding:32px;color:var(--muted)">No admin accounts found.</div>@endforelse
  @if($admins->hasPages())<div class="pg">{!! $admins->links()->toHtml() !!}</div>@endif
</div>
@endsection
