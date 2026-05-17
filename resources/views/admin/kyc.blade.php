@extends('layouts.wallet')
@section('title','KYC Reviews — Admin')
@section('page-title','KYC Verifications')

@section('content')
<div class="card" style="margin-bottom:20px">
  <form method="GET" action="{{ route('admin.kyc') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
    <div style="flex:1;min-width:140px">
      <label class="fl">Status</label>
      <select name="status" class="fi">
        <option value="">All</option>
        <option value="pending"  {{ request('status')==='pending' ?'selected':'' }}>Pending</option>
        <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Approved</option>
        <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Rejected</option>
      </select>
    </div>
    <button type="submit" class="btn bp bsm">Filter</button>
    <a href="{{ route('admin.kyc') }}" class="btn bg bsm">Clear</a>
  </form>
</div>

@forelse($kycs as $kyc)
<div class="card" style="margin-bottom:14px">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap">
    <div style="display:flex;gap:14px;align-items:flex-start;flex:1;min-width:0">
      <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-weight:700;color:#030a12;font-size:18px;flex-shrink:0;overflow:hidden">
        @if($kyc->user->avatar)<img src="{{ $kyc->user->avatar }}" style="width:100%;height:100%;object-fit:cover">@else{{ strtoupper(substr($kyc->user->name,0,1)) }}@endif
      </div>
      <div style="flex:1;min-width:0">
        <div style="font-size:15px;font-weight:700">{{ $kyc->user->name }}</div>
        <div style="font-size:13px;color:var(--muted)">{{ $kyc->user->email }}</div>
        <div style="margin-top:10px;display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:8px">
          <div class="info-row" style="margin-bottom:0"><span class="info-lbl">Full Name</span><span class="info-val" style="font-size:13px">{{ $kyc->full_name }}</span></div>
          <div class="info-row" style="margin-bottom:0"><span class="info-lbl">ID Number</span><span class="info-val mono" style="font-size:13px">{{ $kyc->id_number }}</span></div>
          <div class="info-row" style="margin-bottom:0"><span class="info-lbl">Address</span><span class="info-val" style="font-size:13px">{{ Str::limit($kyc->address,40) }}</span></div>
          <div class="info-row" style="margin-bottom:0"><span class="info-lbl">Submitted</span><span class="info-val" style="font-size:13px">{{ $kyc->created_at->diffForHumans() }}</span></div>
        </div>
        <div style="margin-top:12px;display:flex;gap:10px;flex-wrap:wrap">
          <a href="{{ Storage::url($kyc->id_front_path) }}" target="_blank" class="btn bg bsm">
            📄 ID Front
          </a>
          <a href="{{ Storage::url($kyc->id_back_path) }}" target="_blank" class="btn bg bsm">
            📄 ID Back
          </a>
        </div>
      </div>
    </div>
    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:10px;flex-shrink:0">
      <span class="badge badge-{{ $kyc->status }}">{{ ucfirst($kyc->status) }}</span>
      @if($kyc->isPending())
      <div style="display:flex;gap:8px">
        <form method="POST" action="{{ route('admin.kyc.approve',$kyc->id) }}">
          @csrf @method('PATCH')
          <button type="submit" class="btn btn-green bsm">✓ Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.kyc.reject',$kyc->id) }}">
          @csrf @method('PATCH')
          <button type="submit" class="btn btn-red bsm" onclick="return confirm('Reject this KYC?')">✕ Reject</button>
        </form>
      </div>
      @else
      <div style="font-size:12px;color:var(--dim)">Reviewed {{ $kyc->reviewed_at?->diffForHumans() }}</div>
      @endif
    </div>
  </div>
</div>
@empty
<div class="card" style="text-align:center;padding:48px;color:var(--muted)">No KYC submissions found.</div>
@endforelse

@if($kycs->hasPages())
<div class="pg">{!! $kycs->links()->toHtml() !!}</div>
@endif
@endsection
