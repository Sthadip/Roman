@extends('layouts.wallet')
@section('title','KYC Verification — NEXUS Exchange')
@section('page-title','Identity Verification')

@section('content')
<div style="max-width:640px">

@if($kyc && $kyc->isApproved())
  <div class="card" style="text-align:center;padding:48px">
    <div style="font-size:56px;margin-bottom:16px">✅</div>
    <div style="font-size:22px;font-weight:800;color:var(--green);margin-bottom:8px">KYC Verified</div>
    <div style="color:var(--muted);font-size:15px">Your identity has been verified. You have full access to all features.</div>
  </div>

@elseif($kyc && $kyc->isPending())
  <div class="card" style="text-align:center;padding:48px">
    <div style="font-size:56px;margin-bottom:16px">⏳</div>
    <div style="font-size:22px;font-weight:800;color:var(--yellow);margin-bottom:8px">Under Review</div>
    <div style="color:var(--muted);font-size:15px;margin-bottom:20px">Your KYC documents have been submitted and are being reviewed by our team.</div>
    <div style="background:#ffd60011;border:1px solid #ffd60033;border-radius:12px;padding:16px;text-align:left">
      <div class="info-row"><span class="info-lbl">Full Name</span><span class="info-val">{{ $kyc->full_name }}</span></div>
      <div class="info-row"><span class="info-lbl">ID Number</span><span class="info-val mono">{{ $kyc->id_number }}</span></div>
      <div class="info-row"><span class="info-lbl">Submitted</span><span class="info-val">{{ $kyc->created_at->format('M d, Y H:i') }}</span></div>
    </div>
  </div>

@elseif($kyc && $kyc->isRejected())
  <div style="background:#ff525218;border:1px solid #ff525244;border-radius:12px;padding:16px;margin-bottom:20px;color:var(--red);font-size:14px">
    ⚠ Your previous KYC submission was rejected. Please resubmit with valid documents.
  </div>
  @include('user._kyc-form')

@else
  @include('user._kyc-form')
@endif

</div>
@endsection
