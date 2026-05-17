@extends('layouts.auth')
@section('title','Sign In — NEXUS Exchange')
@section('content')
<div class="auth-title">Welcome back</div>
<div class="auth-sub">Sign in to your NEXUS account</div>

<form method="POST" action="{{ route('login') }}">
  @csrf
  <div class="fg">
    <label class="fl">Email Address</label>
    <input type="email" name="email" class="fi @error('email') fi-err @enderror"
      placeholder="you@example.com" value="{{ old('email') }}" autocomplete="email">
    @error('email')<div class="err">{{ $message }}</div>@enderror
  </div>
  <div class="fg">
    <label class="fl">Password</label>
    <div style="position:relative">
      <input type="password" name="password" id="login-pw" class="fi @error('password') fi-err @enderror"
        placeholder="••••••••" autocomplete="current-password" style="padding-right:44px">
      <button type="button" onclick="togglePw('login-pw','login-eye')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);font-size:16px;padding:0" id="login-eye">👁</button>
    </div>
    @error('password')<div class="err">{{ $message }}</div>@enderror
  </div>
  <div class="fg" style="display:flex;align-items:center;gap:8px">
    <input type="checkbox" name="remember" id="remember" style="accent-color:var(--accent)">
    <label for="remember" style="font-size:14px;color:var(--muted);cursor:pointer">Remember me</label>
  </div>
  <button type="submit" class="btn bp">Sign In</button>
</form>

<div class="div">or</div>

<a href="{{ route('google.redirect') }}" class="btn bg google-btn">
  <svg width="18" height="18" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z"/><path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"/><path fill="#FBBC05" d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z"/><path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 6.29C4.672 4.163 6.656 3.58 9 3.58z"/></svg>
  Sign in with Google
</a>

<div class="auth-foot">Don't have an account? <a href="{{ route('register') }}">Create one</a></div>

<script>
function togglePw(inputId, btnId) {
  var inp = document.getElementById(inputId);
  inp.type = inp.type === 'password' ? 'text' : 'password';
}
</script>
@endsection
