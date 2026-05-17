@extends('layouts.auth')
@section('title','Verify Email — NEXUS Exchange')
@section('content')
<div style="text-align:center;margin-bottom:24px">
  <div style="font-size:48px;margin-bottom:16px">✉</div>
  <div class="auth-title">Verify your email</div>
  <div class="auth-sub" style="max-width:320px;margin:0 auto">
    Thanks for registering! Please check your email and click the verification link to access your dashboard.
  </div>
</div>

@if(session('success'))
  <div style="background:#00e5a022;border:1px solid #00e5a044;color:var(--green);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:14px;font-weight:500">
    {{ session('success') }}
  </div>
@endif

<form method="POST" action="{{ route('verification.send') }}">
  @csrf
  <button type="submit" class="btn bp" style="width:100%">Resend Verification Email</button>
</form>

<div class="auth-foot" style="margin-top:20px">
  <form method="POST" action="{{ route('logout') }}" style="display:inline">
    @csrf
    <button type="submit" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:14px;text-decoration:underline">
      Sign out
    </button>
  </form>
</div>
@endsection
