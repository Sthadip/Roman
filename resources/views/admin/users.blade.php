@extends('layouts.wallet')
@section('title','Users — NEXUS')
@section('page-title','Users')
@section('content')

<div class="card" style="margin-bottom:18px">
  <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
    <div style="flex:1;min-width:200px"><label class="fl">Search</label>
      <input type="text" name="search" class="fi" placeholder="Name or email..." value="{{ request('search') }}">
    </div>
    <button type="submit" class="btn bp bsm">Search</button>
    <a href="{{ request()->routeIs('superadmin.*') ? route('superadmin.users') : route('admin.users') }}" class="btn bg bsm">Clear</a>
  </form>
</div>

<div class="card desk-tbl tw">
  <table>
    <thead><tr style="background:#040f1c"><th>User</th><th>Email</th><th>KYC</th><th>Activity</th><th>Joined</th><th>Actions</th></tr></thead>
    <tbody>
      @forelse($users as $u)
      @php $act = \App\Models\UserActivity::where('user_id',$u->id)->first(); @endphp
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:8px">
            <div style="position:relative;width:32px;height:32px;flex-shrink:0">
              <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#030a12;overflow:hidden">
                @if($u->avatar)<img src="{{ $u->avatar }}" style="width:100%;height:100%;object-fit:cover">@else{{ strtoupper(substr($u->name,0,1)) }}@endif
              </div>
              @if($act && $act->isOnline())
              <div style="position:absolute;bottom:0;right:0;width:9px;height:9px;border-radius:50%;background:var(--green);border:2px solid var(--surface)"></div>
              @endif
            </div>
            <span style="font-weight:600">{{ $u->name }}</span>
          </div>
        </td>
        <td style="color:var(--muted);font-size:13px">{{ $u->email }}</td>
        <td>@php $kyc=$u->kyc; @endphp
          @if($kyc)<span class="badge badge-{{ $kyc->status }}">{{ ucfirst($kyc->status) }}</span>
          @else<span style="font-size:12px;color:var(--dim)">—</span>@endif
        </td>
        <td>
          @if($act)
          <div style="font-size:12.5px;{{ $act->isOnline()?'color:var(--green);font-weight:600':' color:var(--muted)' }}">
            {{ $act->isOnline() ? '● '.$act->page : $act->last_seen_at->diffForHumans() }}
          </div>
          @else<span style="font-size:12px;color:var(--dim)">No activity</span>@endif
        </td>
        <td style="font-size:12.5px;color:var(--muted)">{{ $u->created_at->format('M d, Y') }}</td>
        <td>
          <div style="display:flex;gap:6px;flex-wrap:wrap">
            <a href="{{ route('admin.users.detail',$u->id) }}" class="btn bg bsm">Detail</a>
            @if(auth()->user()->isSuperAdmin() && $u->isUser())
            <form method="POST" action="{{ route('superadmin.promote',$u->id) }}">@csrf @method('PATCH')
              <button type="submit" class="btn btn-purple bsm">Promote</button>
            </form>
            @endif
            @if($u->id !== auth()->id())
            <form method="POST" action="{{ route('admin.users.delete',$u->id) }}">@csrf @method('DELETE')
              <button type="submit" class="btn btn-red bsm" onclick="return confirm('Delete this user?')">Delete</button>
            </form>
            @endif
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" style="text-align:center;padding:48px;color:var(--muted)">No users found.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- Mobile --}}
<div class="mob-list">
  @forelse($users as $u)
  @php $act = \App\Models\UserActivity::where('user_id',$u->id)->first(); @endphp
  <div class="card" style="margin-bottom:10px">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
      <div style="position:relative;width:38px;height:38px;flex-shrink:0">
        <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:#030a12;overflow:hidden">
          @if($u->avatar)<img src="{{ $u->avatar }}" style="width:100%;height:100%;object-fit:cover">@else{{ strtoupper(substr($u->name,0,1)) }}@endif
        </div>
        @if($act && $act->isOnline())<div style="position:absolute;bottom:0;right:0;width:10px;height:10px;border-radius:50%;background:var(--green);border:2px solid var(--surface)"></div>@endif
      </div>
      <div style="flex:1;min-width:0">
        <div style="font-weight:700">{{ $u->name }}</div>
        <div style="font-size:12px;color:var(--muted)">{{ $u->email }}</div>
        @if($act)<div style="font-size:11.5px;{{ $act->isOnline()?'color:var(--green)':'color:var(--dim)' }}">{{ $act->isOnline()?'● Online · '.$act->page : $act->last_seen_at->diffForHumans() }}</div>@endif
      </div>
    </div>
    <div style="display:flex;gap:7px">
      <a href="{{ route('admin.users.detail',$u->id) }}" class="btn bg bsm">Detail</a>
      @if(auth()->user()->isSuperAdmin() && $u->isUser())
      <form method="POST" action="{{ route('superadmin.promote',$u->id) }}">@csrf @method('PATCH')<button type="submit" class="btn btn-purple bsm">Promote</button></form>
      @endif
    </div>
  </div>
  @empty
  <div class="card" style="text-align:center;padding:40px;color:var(--muted)">No users found.</div>
  @endforelse
</div>

@if($users->hasPages())<div class="pg">{!! $users->links()->toHtml() !!}</div>@endif
@endsection
