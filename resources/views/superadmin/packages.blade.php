@extends('layouts.wallet')
@section('title','Investment Packages — NEXUS')
@section('page-title','Investment Packages')
@section('content')

<div class="sh"><h2></h2><a href="{{ route('superadmin.packages.create') }}" class="btn bp">+ Create Package</a></div>

<div class="card">
  @forelse($packages as $p)
  <div style="display:flex;align-items:center;gap:12px;padding:14px 0;border-bottom:1px solid var(--border);flex-wrap:wrap">
    <div style="flex:1;min-width:0">
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px">
        <span style="font-size:15px;font-weight:700">{{ $p->name }}</span>
        <span class="badge {{ $p->is_active?'badge-confirmed':'badge-rejected' }}">{{ $p->is_active?'Active':'Inactive' }}</span>
      </div>
      @if($p->description)<div style="font-size:12.5px;color:var(--muted);margin-bottom:6px">{{ $p->description }}</div>@endif
      <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:12.5px">
        <span style="color:var(--dim)">Duration: <span style="color:var(--text);font-weight:600">{{ $p->duration_days }}d</span></span>
        <span style="color:var(--dim)">Return: <span style="color:var(--green);font-weight:700">{{ $p->return_rate }}%</span></span>
        <span style="color:var(--dim)">Min: <span style="color:var(--text);font-weight:600">${{ $p->min_amount }}</span></span>
        @if($p->max_amount)<span style="color:var(--dim)">Max: <span style="color:var(--text);font-weight:600">${{ $p->max_amount }}</span></span>@endif
        <span style="color:var(--dim)">Investments: <span style="color:var(--accent);font-weight:600">{{ $p->investments_count }}</span></span>
      </div>
    </div>
    <div style="display:flex;gap:7px;flex-shrink:0;flex-wrap:wrap">
      <a href="{{ route('superadmin.packages.edit',$p->id) }}" class="btn bg bsm">✏ Edit</a>
      <form method="POST" action="{{ route('superadmin.packages.toggle',$p->id) }}">
        @csrf @method('PATCH')
        <button type="submit" class="btn {{ $p->is_active?'btn-yellow':'btn-green' }} bsm">{{ $p->is_active?'Disable':'Enable' }}</button>
      </form>
      <form method="POST" action="{{ route('superadmin.packages.delete',$p->id) }}">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-red bsm" onclick="return confirm('Delete this package?')">Delete</button>
      </form>
    </div>
  </div>
  @empty
  <div style="text-align:center;padding:48px;color:var(--muted)">No packages yet. <a href="{{ route('superadmin.packages.create') }}">Create one →</a></div>
  @endforelse
  @if($packages->hasPages())<div class="pg">{!! $packages->links()->toHtml() !!}</div>@endif
</div>
@endsection
