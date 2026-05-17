@extends('layouts.wallet')
@section('title','User Detail — NEXUS')
@section('page-title','User Detail')
@section('content')

<div style="margin-bottom:16px">
  <a href="{{ route('admin.users') }}" class="btn bg bsm">← Back to Users</a>
</div>

<div class="tc">
  <div class="tc-main">

    {{-- Profile card --}}
    <div class="card" style="margin-bottom:18px">
      <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
        <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:#030a12;overflow:hidden;flex-shrink:0">
          @if($user->avatar)<img src="{{ $user->avatar }}" style="width:100%;height:100%;object-fit:cover">
          @else{{ strtoupper(substr($user->name,0,1)) }}@endif
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-size:20px;font-weight:800">{{ $user->name }}</div>
          <div style="font-size:13.5px;color:var(--muted)">{{ $user->email }}</div>
          <div style="display:flex;gap:8px;margin-top:6px;flex-wrap:wrap">
            <span class="badge badge-{{ $user->role }}">{{ $user->role_label }}</span>
            @php $kyc = $user->kyc; @endphp
            @if($kyc)
            <span class="badge badge-{{ $kyc->status }}">KYC: {{ ucfirst($kyc->status) }}</span>
            @else
            <span class="badge" style="background:var(--border);color:var(--dim);border:1px solid var(--border2)">KYC: Not submitted</span>
            @endif
          </div>
        </div>
        <div style="text-align:right;font-size:12.5px;color:var(--muted)">
          Joined {{ $user->created_at->format('M d, Y') }}
        </div>
      </div>
    </div>

    {{-- Live Activity --}}
    <div class="card" style="margin-bottom:18px;border-color:{{ $activity && $activity->isOnline() ? '#00e5ff44' : 'var(--border2)' }}">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px">
        <div style="width:10px;height:10px;border-radius:50%;background:{{ $activity && $activity->isOnline() ? 'var(--green)' : 'var(--dim)' }};flex-shrink:0;
          {{ $activity && $activity->isOnline() ? 'box-shadow:0 0 0 3px #00e5a033;animation:pulse 2s infinite' : '' }}"></div>
        <div style="font-size:15px;font-weight:700">
          {{ $activity && $activity->isOnline() ? '🟢 Currently Online' : '⚫ Offline' }}
        </div>
      </div>
      @if($activity)
      <div style="margin-top:12px;display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <div style="background:#040f1c;border:1px solid var(--border2);border-radius:10px;padding:12px 14px">
          <div style="font-size:11px;color:var(--dim);margin-bottom:3px;text-transform:uppercase;letter-spacing:.06em">Current / Last Page</div>
          <div style="font-size:14px;font-weight:700;color:{{ $activity->isOnline()?'var(--accent)':'var(--text)' }}">{{ $activity->page }}</div>
          <div class="mono" style="font-size:11.5px;color:var(--muted);margin-top:2px">/{{ $activity->url }}</div>
        </div>
        <div style="background:#040f1c;border:1px solid var(--border2);border-radius:10px;padding:12px 14px">
          <div style="font-size:11px;color:var(--dim);margin-bottom:3px;text-transform:uppercase;letter-spacing:.06em">Last Seen</div>
          <div style="font-size:14px;font-weight:700">{{ $activity->last_seen_at->diffForHumans() }}</div>
          <div style="font-size:11.5px;color:var(--muted);margin-top:2px">{{ $activity->last_seen_at->format('M d, Y H:i') }}</div>
        </div>
      </div>
      @if($activity->ip)
      <div style="margin-top:10px;font-size:12.5px;color:var(--dim)">IP: <span class="mono" style="color:var(--muted)">{{ $activity->ip }}</span></div>
      @endif
      @else
      <div style="font-size:13.5px;color:var(--muted);margin-top:8px">No activity recorded yet.</div>
      @endif
    </div>

    {{-- Wallet balances --}}
    <div class="card" style="margin-bottom:18px">
      <div class="sh"><h2>Wallet Balances</h2></div>
      @foreach($coinMeta as $coin => $meta)
      @php $w = $wallets[$coin] ?? null; @endphp
      <div style="display:flex;align-items:center;gap:12px;padding:10px 14px;background:{{ $meta['bg'] }};border:1px solid {{ $meta['color'] }}22;border-radius:10px;margin-bottom:8px">
        <div style="font-size:20px;color:{{ $meta['color'] }};width:28px;text-align:center;font-weight:800">{{ $meta['icon'] }}</div>
        <div style="flex:1">
          <div style="font-size:13px;font-weight:600">{{ $coin }}</div>
          @if(($w->in_order??0)>0)<div style="font-size:11px;color:var(--yellow)">{{ number_format((float)$w->in_order,8) }} locked</div>@endif
        </div>
        <span class="mono" style="font-weight:700;color:{{ $meta['color'] }}">{{ number_format((float)($w->available??0),($coin==='USDT'?2:8)) }}</span>
      </div>
      @endforeach
    </div>

    {{-- Recent Deposits --}}
    <div class="card" style="margin-bottom:18px">
      <div class="sh"><h2>Recent Deposits</h2></div>
      @forelse($deposits as $d)
      <div style="display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid var(--border)">
        <div style="flex:1">
          <div style="font-size:13px;font-weight:600">{{ $d->coin }} · {{ number_format((float)$d->amount,4) }}</div>
          <div style="font-size:11.5px;color:var(--muted)">{{ $d->created_at->format('M d, Y H:i') }}</div>
        </div>
        <span class="badge badge-{{ $d->status }}">{{ ucfirst($d->status) }}</span>
      </div>
      @empty<div style="color:var(--muted);font-size:13.5px;padding:12px 0">No deposits yet.</div>@endforelse
    </div>

    {{-- Recent Withdrawals --}}
    <div class="card">
      <div class="sh"><h2>Recent Withdrawals</h2></div>
      @forelse($withdrawals as $w)
      <div style="display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid var(--border)">
        <div style="flex:1">
          <div style="font-size:13px;font-weight:600">{{ $w->coin }} · {{ number_format((float)$w->amount,4) }}</div>
          <div class="mono" style="font-size:11px;color:var(--muted)">{{ Str::limit($w->wallet_address,30) }}</div>
          <div style="font-size:11.5px;color:var(--muted)">{{ $w->created_at->format('M d, Y H:i') }}</div>
        </div>
        <span class="badge badge-{{ $w->status }}">{{ ucfirst($w->status) }}</span>
      </div>
      @empty<div style="color:var(--muted);font-size:13.5px;padding:12px 0">No withdrawals yet.</div>@endforelse
    </div>

  </div>

  <div class="tc-side">
    {{-- Quick stats --}}
    <div class="card" style="margin-bottom:16px">
      <div style="font-size:13px;font-weight:700;margin-bottom:14px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Account Stats</div>
      @foreach([
        ['Total Deposits',$deposits->count().' records'],
        ['Total Withdrawals',$withdrawals->count().' records'],
        ['Total Transactions',$txCount.' records'],
      ] as [$lbl,$val])
      <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border);font-size:13.5px">
        <span style="color:var(--muted)">{{ $lbl }}</span>
        <span style="font-weight:600">{{ $val }}</span>
      </div>
      @endforeach
    </div>

    {{-- Manual USDT Credit --}}
    <div class="card" style="margin-bottom:16px;border-color:#26A17B44;background:#26A17B05">
      <div style="font-size:12px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px">Manual Credit</div>
      @php
        $usdWal = $wallets->firstWhere('coin','USDT');
      @endphp
      <div style="background:#040f1c;border:1px solid var(--border2);border-radius:10px;padding:12px 14px;margin-bottom:12px;text-align:center">
        <div style="font-size:11px;color:var(--muted);margin-bottom:4px">Current USDT Balance</div>
        <div style="display:flex;align-items:baseline;gap:3px;justify-content:center">
          <span style="font-size:20px;font-weight:800;color:#26A17B">$</span>
          <span class="mono" style="font-size:24px;font-weight:900;color:#26A17B">{{ number_format((float)($usdWal->available??0),2) }}</span>
        </div>
      </div>
      <a href="{{ route('admin.users.credit', $user->id) }}" class="btn bp" style="width:100%;text-align:center;font-size:14px">
        ⊕ Credit USDTT to {{ explode(' ', $user->name)[0] }}
      </a>
      <div style="font-size:11.5px;color:var(--dim);margin-top:8px;text-align:center;line-height:1.5">
        Directly add USDT to this user's wallet without a deposit request
      </div>
    </div>

    {{-- Admin actions --}}
    @if(auth()->user()->isSuperAdmin())
    <div class="card" style="margin-bottom:16px">
      <div style="font-size:13px;font-weight:700;margin-bottom:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Admin Actions</div>
      @if($user->isUser())
      <form method="POST" action="{{ route('superadmin.promote',$user->id) }}" style="margin-bottom:8px">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-purple" style="width:100%">Promote to Admin</button>
      </form>
      @elseif($user->isRegularAdmin())
      <form method="POST" action="{{ route('superadmin.demote',$user->id) }}" style="margin-bottom:8px">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-yellow" style="width:100%">Demote to User</button>
      </form>
      @endif
      <form method="POST" action="{{ route('admin.users.delete',$user->id) }}">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-red" style="width:100%" onclick="return confirm('Delete this user permanently?')">Delete User</button>
      </form>
    </div>
    @endif

    {{-- KYC detail --}}
    @if($kyc)
    <div class="card">
      <div style="font-size:13px;font-weight:700;margin-bottom:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">KYC Submission</div>
      <div style="font-size:13px;margin-bottom:8px"><span style="color:var(--dim)">Name:</span> <span style="font-weight:600">{{ $kyc->full_name }}</span></div>
      <div style="font-size:13px;margin-bottom:8px"><span style="color:var(--dim)">ID #:</span> <span class="mono">{{ $kyc->id_number }}</span></div>
      <div style="margin-bottom:8px"><span class="badge badge-{{ $kyc->status }}">{{ ucfirst($kyc->status) }}</span></div>
      @if($kyc->isPending())
      <form method="POST" action="{{ route('admin.kyc.approve',$kyc->id) }}" style="margin-bottom:6px">@csrf @method('PATCH')<button type="submit" class="btn btn-green bsm" style="width:100%">Approve KYC</button></form>
      <form method="POST" action="{{ route('admin.kyc.reject',$kyc->id) }}">@csrf @method('PATCH')<button type="submit" class="btn btn-red bsm" style="width:100%">Reject KYC</button></form>
      @endif
    </div>
    @endif
  </div>
</div>

<style>
@keyframes pulse { 0%,100%{box-shadow:0 0 0 3px #00e5a033} 50%{box-shadow:0 0 0 6px #00e5a011} }
</style>
@endsection
