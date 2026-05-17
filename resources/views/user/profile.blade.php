@extends('layouts.wallet')
@section('title','Profile — NEXUS Exchange')
@section('page-title','Profile')

@section('content')
<div style="max-width:600px">
  {{-- Profile info --}}
  <div class="card" style="margin-bottom:20px">
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px">
      <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:#030a12;overflow:hidden;flex-shrink:0">
        @if($user->avatar)
          <img src="{{ $user->avatar }}" alt="" style="width:100%;height:100%;object-fit:cover">
        @else
          {{ strtoupper(substr($user->name,0,1)) }}
        @endif
      </div>
      <div>
        <div style="font-size:18px;font-weight:700">{{ $user->name }}</div>
        <div style="font-size:14px;color:var(--muted)">{{ $user->email }}</div>
        <div style="margin-top:6px;display:flex;gap:8px">
          <span class="badge badge-confirmed" style="font-size:11px">{{ ucfirst($user->role) }}</span>
          @if($user->isGoogleUser())
          <span class="badge" style="background:#4285F422;color:#4285F4;border:1px solid #4285F444;font-size:11px">Google</span>
          @endif
          @if($user->email_verified_at)
          <span class="badge badge-confirmed" style="font-size:11px">✓ Verified</span>
          @endif
        </div>
      </div>
    </div>

    <form method="POST" action="{{ route('user.profile.update') }}">
      @csrf @method('PATCH')
      <div class="fg">
        <label class="fl">Full Name</label>
        <input type="text" name="name" class="fi @error('name') fi-err @enderror"
          value="{{ old('name',$user->name) }}">
        @error('name')<div class="err">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label class="fl">Email Address</label>
        <input type="email" class="fi" value="{{ $user->email }}" disabled style="opacity:.6;cursor:not-allowed">
        <div style="font-size:12px;color:var(--dim);margin-top:4px">Email cannot be changed</div>
      </div>
      <div class="fg">
        <label class="fl">Member Since</label>
        <input type="text" class="fi" value="{{ $user->created_at->format('F j, Y') }}" disabled style="opacity:.6;cursor:not-allowed">
      </div>
      <button type="submit" class="btn bp">Save Changes</button>
    </form>
  </div>

  {{-- Password --}}
  <div class="card">
    <div style="font-size:17px;font-weight:700;margin-bottom:6px">Change Password</div>
    @if($user->isGoogleUser())
    <div style="background:#00e5ff11;border:1px solid #00e5ff33;border-radius:10px;padding:14px;color:var(--accent);font-size:14px">
      ℹ You signed in with Google. Password management is handled through your Google account.
    </div>
    @else
    <form method="POST" action="{{ route('user.password.update') }}" style="margin-top:16px">
      @csrf @method('PATCH')
      <div class="fg">
        <label class="fl">Current Password</label>
        <input type="password" name="current_password" class="fi @error('current_password') fi-err @enderror" placeholder="••••••••">
        @error('current_password')<div class="err">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label class="fl">New Password</label>
        <input type="password" name="password" class="fi @error('password') fi-err @enderror" placeholder="Min. 8 characters">
        @error('password')<div class="err">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label class="fl">Confirm New Password</label>
        <input type="password" name="password_confirmation" class="fi" placeholder="Repeat new password">
      </div>
      <button type="submit" class="btn bp">Update Password</button>
    </form>
    @endif
  </div>
</div>
@endsection
