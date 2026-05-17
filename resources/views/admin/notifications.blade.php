@extends('layouts.wallet')
@section('title','Notifications — NEXUS')
@section('page-title','Notifications')
@section('content')

<div class="sh">
  <div></div>
  <form method="POST" action="{{ auth()->user()->isSuperAdmin() ? route('superadmin.notifications.read') : route('admin.notifications.read') }}">
    @csrf
    <button type="submit" class="btn bg bsm">✓ Mark All Read</button>
  </form>
</div>

<div class="card">
  @forelse($notifications as $n)
  <div style="display:flex;gap:12px;padding:14px 0;border-bottom:1px solid var(--border);{{ !$n->is_read?'background:#00e5ff04;':'opacity:.7' }}">
    <div style="width:38px;height:38px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:18px;
      {{ $n->type==='new_user'?'background:#00e5a018':'background:#00e5ff18' }}">
      {{ $n->type === 'new_user' ? '👤' : '🔑' }}
    </div>
    <div style="flex:1;min-width:0">
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:3px">
        <span style="font-size:14px;font-weight:700">{{ $n->title }}</span>
        @if(!$n->is_read)<span style="width:7px;height:7px;border-radius:50%;background:var(--accent);display:inline-block"></span>@endif
      </div>
      <div style="font-size:13.5px;color:var(--muted)">{{ $n->body }}</div>
      <div style="font-size:11.5px;color:var(--dim);margin-top:4px">{{ $n->created_at->diffForHumans() }} · {{ $n->created_at->format('M d, Y H:i') }}</div>
      @if($n->refUser && $n->ref_user_id)
      <div style="margin-top:8px">
        <a href="{{ route('admin.users.detail', $n->ref_user_id) }}" class="btn bg bsm">View User →</a>
      </div>
      @endif
    </div>
  </div>
  @empty
  <div style="text-align:center;padding:48px;color:var(--muted)">No notifications yet.</div>
  @endforelse
</div>
@if($notifications->hasPages())<div class="pg">{!! $notifications->links()->toHtml() !!}</div>@endif
@endsection
