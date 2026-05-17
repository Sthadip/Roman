<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title','NEXUS Exchange')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#030a12;--surface:#091525;--border:#0f2538;--border2:#1a3a50;
  --text:#e0f7fa;--muted:#5a8aa0;--dim:#2a5a7a;
  --accent:#00e5ff;--accent2:#00b4d8;--green:#00e5a0;--yellow:#ffd600;--red:#ff5252;
  --purple:#7c4dff;--sb:272px;
}
html,body{height:100%;background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;font-size:15px;line-height:1.6}
a{color:var(--accent);text-decoration:none}
.shell{display:flex;min-height:100vh}
#ov{display:none;position:fixed;inset:0;background:#000b;z-index:200}
#ov.on{display:block}
/* Sidebar */
#sb{position:fixed;top:0;left:0;width:var(--sb);height:100vh;background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;z-index:300;transition:transform .3s}
.sb-logo{padding:20px 18px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px}
.sb-logo .l1{font-size:20px;font-weight:800;letter-spacing:-1px}
.sb-logo .l2{font-size:8px;font-weight:700;letter-spacing:5px;color:var(--accent);text-transform:uppercase;margin-top:1px}
.sb-nav{flex:1;overflow-y:auto;padding:12px 10px;scrollbar-width:thin;scrollbar-color:var(--border2) transparent}
.sb-nav a{display:flex;align-items:center;gap:9px;padding:9px 11px;border-radius:9px;color:var(--muted);font-size:13.5px;font-weight:500;margin-bottom:1px;transition:all .2s;white-space:nowrap}
.sb-nav a:hover,.sb-nav a.act{background:var(--border);color:var(--text)}
.sb-nav a.act{color:var(--accent)}
.sb-nav a.warn-link{color:#ffd60077}
.sb-nav a.warn-link:hover,.sb-nav a.warn-link.act{background:#ffd60014;color:var(--yellow)}
.sb-nav a.admin-link{color:#9c6dff66}
.sb-nav a.admin-link:hover,.sb-nav a.admin-link.act{background:#7c4dff14;color:#b390ff}
.sb-nav a.super-link{color:#ff6d9966}
.sb-nav a.super-link:hover,.sb-nav a.super-link.act{background:#ff6d9914;color:#ff9eb5}
.nav-i{width:18px;text-align:center;font-size:14px;flex-shrink:0}
.sb-grp{font-size:9.5px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--dim);padding:14px 11px 5px;margin-top:6px}
/* Main */
.main{flex:1;margin-left:var(--sb);min-width:0;display:flex;flex-direction:column}
/* Topbar */
.topbar{position:sticky;top:0;z-index:100;background:var(--surface);border-bottom:1px solid var(--border);padding:0 22px;height:58px;display:flex;align-items:center;gap:12px}
.hamburger{display:none;background:none;border:none;color:var(--text);font-size:21px;cursor:pointer;padding:4px 8px;border-radius:8px}
.hamburger:hover{background:var(--border)}
.topbar-title{font-size:16px;font-weight:700;flex:1}
/* Notification bell */
.notif-btn{position:relative;background:none;border:1px solid var(--border2);color:var(--muted);cursor:pointer;width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;transition:all .2s;flex-shrink:0}
.notif-btn:hover{border-color:var(--accent);color:var(--accent)}
.notif-dot{position:absolute;top:5px;right:5px;min-width:16px;height:16px;border-radius:8px;background:var(--red);color:#fff;font-size:9px;font-weight:700;display:flex;align-items:center;justify-content:center;padding:0 3px;border:2px solid var(--surface)}
/* Header dropdown */
.hdr-user{position:relative}
.hdr-btn{display:flex;align-items:center;gap:9px;cursor:pointer;padding:5px 10px;border-radius:10px;border:1px solid var(--border2);background:var(--bg);transition:all .2s;user-select:none}
.hdr-btn:hover{border-color:var(--accent)}
.hdr-av{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#030a12;overflow:hidden;flex-shrink:0}
.hdr-av img{width:100%;height:100%;object-fit:cover}
.hdr-name{font-size:13px;font-weight:600;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.hdr-chev{font-size:10px;color:var(--muted);transition:transform .2s}
.hdr-drop{display:none;position:absolute;top:calc(100% + 6px);right:0;min-width:190px;background:var(--surface);border:1px solid var(--border2);border-radius:12px;box-shadow:0 8px 32px #000a;z-index:200;overflow:hidden}
.hdr-drop.open{display:block}
.hdr-drop-head{padding:12px 14px;border-bottom:1px solid var(--border)}
.hdr-drop-head .dn{font-size:14px;font-weight:700}
.hdr-drop-head .de{font-size:11px;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:160px}
.hdr-drop-head .dr{display:inline-flex;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;margin-top:4px}
.hdr-drop a,.hdr-drop button{display:flex;align-items:center;gap:9px;padding:10px 14px;font-size:13.5px;font-weight:500;color:var(--muted);background:none;border:none;cursor:pointer;width:100%;text-align:left;font-family:inherit;transition:all .2s;text-decoration:none}
.hdr-drop a:hover,.hdr-drop button:hover{background:var(--border);color:var(--text)}
.hdr-drop .logout-btn:hover{color:var(--red)}
/* KYC Banner */
.kyc-banner{background:linear-gradient(135deg,#ffd60014,#ffa00014);border-bottom:1px solid #ffd60033;padding:10px 22px;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.kyc-banner-text{flex:1;font-size:13.5px;color:var(--yellow);min-width:200px}
.kyc-banner a.kyc-btn{background:var(--yellow);color:#030a12;padding:6px 16px;border-radius:8px;font-size:12.5px;font-weight:700;white-space:nowrap;flex-shrink:0}
/* Flash */
.fz{padding:0 22px}
.fz-inner{padding:11px 15px;border-radius:10px;margin-top:14px;font-size:13.5px;font-weight:500}
.fz-inner.ok{background:#00e5a018;border:1px solid #00e5a033;color:var(--green)}
.fz-inner.er{background:#ff525218;border:1px solid #ff525233;color:var(--red)}
.pc{padding:22px;flex:1}
/* Cards */
.card{background:var(--surface);border:1px solid var(--border2);border-radius:14px;padding:20px;box-shadow:0 2px 14px #0003}
/* Buttons */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:600;border-radius:10px;padding:9px 18px;transition:all .2s;text-decoration:none;white-space:nowrap}
.bp{background:linear-gradient(135deg,var(--accent),var(--accent2));color:#030a12}.bp:hover{opacity:.9}
.bg{background:transparent;border:1px solid var(--border2);color:var(--text)}.bg:hover{border-color:var(--accent);color:var(--accent)}
.bsm{padding:6px 12px;font-size:12.5px;border-radius:8px}
.btn-green{background:#00e5a018;border:1px solid #00e5a033;color:var(--green)}.btn-green:hover{background:#00e5a028}
.btn-red{background:#ff525218;border:1px solid #ff525233;color:var(--red)}.btn-red:hover{background:#ff525228}
.btn-yellow{background:#ffd60018;border:1px solid #ffd60033;color:var(--yellow)}.btn-yellow:hover{background:#ffd60028}
.btn-purple{background:#7c4dff18;border:1px solid #7c4dff33;color:#b390ff}.btn-purple:hover{background:#7c4dff28}
/* Badges */
.badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:20px;font-size:11.5px;font-weight:600}
.badge-pending{background:#ffd60018;color:var(--yellow);border:1px solid #ffd60028}
.badge-confirmed,.badge-approved,.badge-completed,.badge-active{background:#00e5a018;color:var(--green);border:1px solid #00e5a028}
.badge-rejected,.badge-cancelled{background:#ff525218;color:var(--red);border:1px solid #ff525228}
.badge-admin{background:#7c4dff18;color:#b390ff;border:1px solid #7c4dff28}
.badge-super_admin{background:#ff6d9918;color:#ff9eb5;border:1px solid #ff6d9928}
/* Forms */
.fl{display:block;font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--dim);margin-bottom:5px}
.fi{width:100%;background:#040f1c;border:1px solid var(--border2);border-radius:10px;padding:10px 13px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:14.5px;outline:none;transition:border-color .2s}
.fi:focus{border-color:var(--accent)}.fi::placeholder{color:var(--dim)}.fi-err{border-color:var(--red)!important}
select.fi{appearance:none} textarea.fi{resize:vertical}
.fg{margin-bottom:16px}
.err{color:var(--red);font-size:12px;margin-top:3px}
/* Stats grid */
.sg{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
@media(min-width:900px){.sg{grid-template-columns:repeat(4,1fr)}}
.sc{background:var(--surface);border:1px solid var(--border2);border-radius:14px;padding:16px 18px}
.sc-val{font-size:26px;font-weight:800;font-family:'DM Mono',monospace;line-height:1}
.sc-lbl{font-size:11.5px;color:var(--muted);margin-top:5px}
.sc-icon{font-size:22px;margin-bottom:8px}
/* Two col */
.tc{display:flex;flex-direction:column;gap:18px}
@media(min-width:1000px){.tc{flex-direction:row}.tc-main{flex:1;min-width:0}.tc-side{width:290px;flex-shrink:0}}
/* Table */
.tw{overflow-x:auto}
table{width:100%;border-collapse:collapse}
th{font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--dim);padding:10px 12px;text-align:left;border-bottom:1px solid var(--border)}
td{padding:11px 12px;border-bottom:1px solid var(--border);font-size:13.5px;color:var(--text)}
tr:last-child td{border-bottom:none}
tr:hover td{background:#ffffff03}
.mono{font-family:'DM Mono',monospace}
.sh{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:10px;flex-wrap:wrap}
.sh h2{font-size:16px;font-weight:700}
/* Modal */
.modal-bg{display:none;position:fixed;inset:0;background:#000c;z-index:500;align-items:center;justify-content:center;padding:18px}
.modal-bg.open{display:flex}
.modal-box{background:var(--surface);border:1px solid var(--border2);border-radius:16px;width:100%;max-width:460px;max-height:92vh;overflow-y:auto;position:relative}
.modal-head{padding:18px 18px 0;display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
.modal-head h3{font-size:17px;font-weight:700}
.modal-close{background:none;border:none;color:var(--muted);font-size:20px;cursor:pointer;padding:4px;border-radius:6px;line-height:1}
.modal-close:hover{color:var(--text)}
.modal-body{padding:0 18px 18px}
/* Pagination */
.pg{display:flex;align-items:center;gap:7px;margin-top:18px;flex-wrap:wrap}
.pg a,.pg span{display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 7px;border-radius:8px;font-size:13px;font-weight:500;border:1px solid var(--border2);color:var(--muted);transition:all .2s;text-decoration:none}
.pg a:hover{border-color:var(--accent);color:var(--accent)}
.pg .pg-active{background:var(--accent);color:#030a12;border-color:var(--accent)}
/* Mobile */
.mob-list{display:block}.desk-tbl{display:none}
@media(min-width:640px){.mob-list{display:none}.desk-tbl{display:block}}
/* Responsive */
@media(max-width:1023px){#sb{transform:translateX(-100%)}#sb.open{transform:translateX(0)}.main{margin-left:0}.hamburger{display:block}}
@media(max-width:639px){.pc{padding:14px}.topbar{padding:0 14px}.sc-val{font-size:20px}}
</style>
</head>
<body>
<div class="shell">
  <div id="ov" onclick="closeSb()"></div>
  <aside id="sb">
    <div class="sb-logo">
      <div><div class="l1">NEXUS</div><div class="l2">Exchange</div></div>
    </div>
    <nav class="sb-nav">
      @php
        $r    = request()->route()->getName();
        $u    = auth()->user();
        $kycOk= $u->hasSubmittedKyc();
      @endphp

      @if($u->isSuperAdmin())
        <div class="sb-grp">Super Admin</div>
        <a href="{{ route('superadmin.dashboard') }}" class="super-link {{ $r==='superadmin.dashboard'?'act':'' }}"><span class="nav-i">◈</span>SA Dashboard</a>
        <a href="{{ route('superadmin.admins') }}" class="super-link {{ str_starts_with($r,'superadmin.admin')?'act':'' }}"><span class="nav-i">👑</span>Manage Admins</a>
        <a href="{{ route('superadmin.notifications') }}" class="super-link {{ $r==='superadmin.notifications'?'act':'' }}">
          <span class="nav-i">🔔</span>Notifications
          @php $unc = \App\Models\AdminNotification::unreadCount(); @endphp
          @if($unc)<span style="margin-left:auto;background:var(--red);color:#fff;border-radius:10px;padding:1px 6px;font-size:10px;font-weight:700">{{ $unc }}</span>@endif
        </a>
        <div class="sb-grp">Admin Panel</div>
        <a href="{{ route('admin.dashboard') }}" class="admin-link {{ $r==='admin.dashboard'?'act':'' }}"><span class="nav-i">⊞</span>Dashboard</a>
        <a href="{{ route('admin.users') }}" class="admin-link {{ str_starts_with($r,'admin.user')?'act':'' }}"><span class="nav-i">👥</span>Users</a>
        <a href="{{ route('admin.deposits') }}" class="admin-link {{ str_starts_with($r,'admin.deposit')?'act':'' }}"><span class="nav-i">↓</span>Deposits</a>
        <a href="{{ route('admin.withdrawals') }}" class="admin-link {{ $r==='admin.withdrawals'?'act':'' }}"><span class="nav-i">↑</span>Withdrawals</a>
        <a href="{{ route('admin.kyc') }}" class="admin-link {{ $r==='admin.kyc'?'act':'' }}"><span class="nav-i">✔</span>KYC</a>
        <a href="{{ route('admin.trade') }}" class="admin-link {{ str_starts_with($r,'admin.trade')?'act':'' }}"><span class="nav-i">📈</span>Trading Panel</a>

      @elseif($u->isAdmin())
        <div class="sb-grp">Admin</div>
        <a href="{{ route('admin.dashboard') }}" class="admin-link {{ $r==='admin.dashboard'?'act':'' }}"><span class="nav-i">⊞</span>Dashboard</a>
        <a href="{{ route('admin.users') }}" class="admin-link {{ str_starts_with($r,'admin.user')?'act':'' }}"><span class="nav-i">👥</span>Users</a>
        <a href="{{ route('admin.deposits') }}" class="admin-link {{ str_starts_with($r,'admin.deposit')?'act':'' }}"><span class="nav-i">↓</span>Deposits</a>
        <a href="{{ route('admin.withdrawals') }}" class="admin-link {{ $r==='admin.withdrawals'?'act':'' }}"><span class="nav-i">↑</span>Withdrawals</a>
        <a href="{{ route('admin.kyc') }}" class="admin-link {{ $r==='admin.kyc'?'act':'' }}"><span class="nav-i">✔</span>KYC</a>
        <a href="{{ route('admin.trade') }}" class="admin-link {{ str_starts_with($r,'admin.trade')?'act':'' }}"><span class="nav-i">📈</span>Trading Panel</a>
        <a href="{{ route('admin.notifications') }}" class="admin-link {{ $r==='admin.notifications'?'act':'' }}">
          <span class="nav-i">🔔</span>Notifications
          @php $unc = \App\Models\AdminNotification::unreadCount(); @endphp
          @if($unc)<span style="margin-left:auto;background:var(--red);color:#fff;border-radius:10px;padding:1px 6px;font-size:10px;font-weight:700">{{ $unc }}</span>@endif
        </a>

      @else
        <div class="sb-grp">Menu</div>
        <a href="{{ route('user.dashboard') }}" class="{{ $r==='user.dashboard'?'act':'' }}"><span class="nav-i">◈</span>Dashboard</a>
        <a href="{{ route('user.wallet') }}" class="{{ $r==='user.wallet'?'act':'' }}"><span class="nav-i">◎</span>Wallet</a>
        <a href="{{ route('user.deposit.form') }}" class="{{ str_starts_with($r,'user.deposit')?'act':'' }}"><span class="nav-i">↓</span>Deposit USDT</a>
        <a href="{{ route('user.withdraw.form') }}" class="{{ str_starts_with($r,'user.withdraw')?'act':'' }}"><span class="nav-i">↑</span>Withdraw</a>
        <a href="{{ route('user.transactions') }}" class="{{ $r==='user.transactions'?'act':'' }}"><span class="nav-i">↕</span>Transactions</a>
        <a href="{{ route('user.trade') }}" class="{{ str_starts_with($r,'user.trade')?'act':'' }}" style="color:{{ str_starts_with($r,'user.trade')?'var(--accent)':'var(--muted)' }}"><span class="nav-i">📈</span>Trade BTC/ETH</a>
        @if(!$kycOk)
        <a href="{{ route('user.kyc') }}" class="warn-link {{ $r==='user.kyc'?'act':'' }}">
          <span class="nav-i">⚠</span>Verify Identity
          <span style="margin-left:auto;background:var(--yellow);color:#030a12;border-radius:50%;width:16px;height:16px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800">!</span>
        </a>
        @else
        <a href="{{ route('user.kyc') }}" class="{{ $r==='user.kyc'?'act':'' }}"><span class="nav-i">✔</span>KYC Status</a>
        @endif
      @endif
    </nav>
  </aside>

  <div class="main">
    <header class="topbar">
      <button class="hamburger" onclick="openSb()">☰</button>
      <div class="topbar-title">@yield('page-title','Dashboard')</div>

      {{-- Notification bell (admin/superadmin only) --}}
      @if(auth()->user()->isAdmin())
      @php $bellCount = \App\Models\AdminNotification::unreadCount(); @endphp
      <a href="{{ auth()->user()->isSuperAdmin() ? route('superadmin.notifications') : route('admin.notifications') }}"
         class="notif-btn" title="Notifications">
        🔔
        @if($bellCount > 0)<span class="notif-dot">{{ $bellCount > 99 ? '99+' : $bellCount }}</span>@endif
      </a>
      @endif

      {{-- User dropdown --}}
      <div class="hdr-user">
        <div class="hdr-btn" onclick="toggleDrop()" id="hdr-btn">
          <div class="hdr-av">
            @if(auth()->user()->avatar)<img src="{{ auth()->user()->avatar }}" alt="">
            @else{{ strtoupper(substr(auth()->user()->name,0,1)) }}@endif
          </div>
          <span class="hdr-name">{{ auth()->user()->name }}</span>
          <span class="hdr-chev" id="hdr-chev">▾</span>
        </div>
        <div class="hdr-drop" id="hdr-drop">
          <div class="hdr-drop-head">
            <div class="dn">{{ auth()->user()->name }}</div>
            <div class="de">{{ auth()->user()->email }}</div>
            <span class="dr badge-{{ auth()->user()->role }}">{{ auth()->user()->role_label }}</span>
          </div>
          @if(auth()->user()->isUser())
          <a href="{{ route('user.profile') }}"><span>👤</span> Profile</a>
          @endif
          @if(auth()->user()->isAdmin())
          <a href="{{ auth()->user()->isSuperAdmin() ? route('superadmin.notifications') : route('admin.notifications') }}">
            <span>🔔</span> Notifications
            @if(($bellCount??0)>0)<span style="background:var(--red);color:#fff;border-radius:10px;padding:1px 6px;font-size:10px;font-weight:700;margin-left:4px">{{ $bellCount }}</span>@endif
          </a>
          @endif
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn"><span>⎋</span> Sign Out</button>
          </form>
        </div>
      </div>
    </header>

    {{-- KYC banner --}}
    @if(auth()->user()->isUser() && !auth()->user()->hasSubmittedKyc())
    <div class="kyc-banner">
      <span style="font-size:18px">⚠</span>
      <div class="kyc-banner-text"><strong>Identity Verification Required</strong> — Complete KYC to unlock withdrawals.</div>
      <a href="{{ route('user.kyc') }}" class="kyc-btn">Verify Now</a>
    </div>
    @endif

    {{-- Flash --}}
    <div class="fz">
      @if(session('success'))<div class="fz-inner ok">{{ session('success') }}</div>@endif
      @if(session('error'))<div class="fz-inner er">{{ session('error') }}</div>@endif
      @if($errors->any() && !session('wd_error'))<div class="fz-inner er">{{ $errors->first() }}</div>@endif
    </div>

    <main class="pc">@yield('content')</main>
  </div>
</div>

@stack('modals')
<script>
function openSb(){document.getElementById('sb').classList.add('open');document.getElementById('ov').classList.add('on');document.body.style.overflow='hidden'}
function closeSb(){document.getElementById('sb').classList.remove('open');document.getElementById('ov').classList.remove('on');document.body.style.overflow=''}
function toggleDrop(){
  var d=document.getElementById('hdr-drop'),c=document.getElementById('hdr-chev');
  d.classList.toggle('open');c.textContent=d.classList.contains('open')?'▴':'▾';
}
document.addEventListener('click',function(e){
  var btn=document.getElementById('hdr-btn'),drop=document.getElementById('hdr-drop');
  if(btn&&drop&&!btn.contains(e.target)&&!drop.contains(e.target)){drop.classList.remove('open');document.getElementById('hdr-chev').textContent='▾';}
});
function openModal(id){document.getElementById(id).classList.add('open');document.body.style.overflow='hidden'}
function closeModal(id){document.getElementById(id).classList.remove('open');document.body.style.overflow=''}
document.addEventListener('keydown',function(e){if(e.key==='Escape'){document.querySelectorAll('.modal-bg.open').forEach(function(m){m.classList.remove('open')});closeSb();document.body.style.overflow=''}});
function copyText(t,btn){var o=btn?btn.textContent:'';navigator.clipboard&&navigator.clipboard.writeText(t).then(function(){if(btn){btn.textContent='✓';setTimeout(function(){btn.textContent=o},1800)}})}
</script>
@stack('scripts')
</body>
</html>
