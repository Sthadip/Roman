@extends('layouts.auth')
@section('title','Create Account — NEXUS Exchange')
@section('content')
<div class="auth-title">Create your account</div>
<div class="auth-sub">Join NEXUS Exchange today</div>

<form method="POST" action="{{ route('register') }}">
  @csrf
  <div class="fg">
    <label class="fl">Full Name</label>
    <input type="text" name="name" class="fi @error('name') fi-err @enderror"
      placeholder="John Doe" value="{{ old('name') }}" autocomplete="name">
    @error('name')<div class="err">{{ $message }}</div>@enderror
  </div>
  <div class="fg">
    <label class="fl">Email Address</label>
    <input type="email" name="email" class="fi @error('email') fi-err @enderror"
      placeholder="you@example.com" value="{{ old('email') }}" autocomplete="email">
    @error('email')<div class="err">{{ $message }}</div>@enderror
  </div>
  <div class="fg">
    <label class="fl">Password</label>
    <div style="position:relative">
      <input type="password" name="password" id="reg-pw" class="fi @error('password') fi-err @enderror"
        placeholder="Min. 8 chars" autocomplete="new-password" style="padding-right:44px">
      <button type="button" onclick="togglePw('reg-pw')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);font-size:16px;padding:0">👁</button>
    </div>
    <div style="margin-top:8px;background:#00e5ff11;border:1px solid #00e5ff22;border-radius:8px;padding:10px 12px">
      <div style="font-size:11px;font-weight:600;color:var(--accent);margin-bottom:6px;letter-spacing:.04em">PASSWORD REQUIREMENTS</div>
      <div id="rule-len"  class="pw-rule">✗ At least 8 characters</div>
      <div id="rule-alpha" class="pw-rule">✗ At least one letter (a-z or A-Z)</div>
      <div id="rule-num"  class="pw-rule">✗ At least one number (0-9)</div>
      <div id="rule-sym"  class="pw-rule">✗ At least one symbol (!@#$%^&*...)</div>
    </div>
    @error('password')<div class="err">{{ $message }}</div>@enderror
  </div>
  <div class="fg">
    <label class="fl">Confirm Password</label>
    <div style="position:relative">
      <input type="password" name="password_confirmation" id="reg-pw2" class="fi"
        placeholder="Repeat password" autocomplete="new-password" style="padding-right:44px">
      <button type="button" onclick="togglePw('reg-pw2')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);font-size:16px;padding:0">👁</button>
    </div>
  </div>
  <button type="submit" class="btn bp">Create Account</button>
</form>

<div class="div">or</div>

<a href="{{ route('google.redirect') }}" class="btn bg google-btn">
  <svg width="18" height="18" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z"/><path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"/><path fill="#FBBC05" d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z"/><path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 6.29C4.672 4.163 6.656 3.58 9 3.58z"/></svg>
  Sign up with Google
</a>

<div class="auth-foot">Already have an account? <a href="{{ route('login') }}">Sign in</a></div>

<style>
.pw-rule{font-size:12px;color:var(--muted);padding:2px 0;transition:color .2s}
.pw-rule.ok{color:var(--green)}
</style>
<script>
function togglePw(id) {
  var el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
}
var pwInput = document.getElementById('reg-pw');
if (pwInput) {
  pwInput.addEventListener('input', function() {
    var v = this.value;
    setRule('rule-len',   v.length >= 8);
    setRule('rule-alpha', /[a-zA-Z]/.test(v));
    setRule('rule-num',   /[0-9]/.test(v));
    setRule('rule-sym',   /[^a-zA-Z0-9]/.test(v));
  });
}
function setRule(id, ok) {
  var el = document.getElementById(id);
  if (!el) return;
  el.classList.toggle('ok', ok);
  el.textContent = (ok ? '✓ ' : '✗ ') + el.textContent.slice(2);
}
</script>
@endsection
