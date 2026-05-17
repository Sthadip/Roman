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
}
html,body{height:100%;background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;font-size:15px;line-height:1.6}
a{color:var(--accent);text-decoration:none}a:hover{text-decoration:underline}
.auth-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
.auth-card{background:var(--surface);border:1px solid var(--border2);border-radius:16px;padding:40px;width:100%;max-width:440px;box-shadow:0 8px 40px #000a}
.auth-logo{text-align:center;margin-bottom:32px}
.auth-logo .lg1{font-size:28px;font-weight:800;letter-spacing:-1px;color:var(--text)}
.auth-logo .lg2{font-size:10px;font-weight:600;letter-spacing:6px;color:var(--accent);text-transform:uppercase}
.auth-title{font-size:20px;font-weight:700;margin-bottom:6px}
.auth-sub{color:var(--muted);font-size:14px;margin-bottom:28px}
.fl{display:block;font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--dim);margin-bottom:6px}
.fi{width:100%;background:#040f1c;border:1px solid var(--border2);border-radius:10px;padding:11px 14px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:15px;outline:none;transition:border-color .2s}
.fi:focus{border-color:var(--accent)}
.fi::placeholder{color:var(--dim)}
.fg{margin-bottom:18px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:15px;font-weight:600;border-radius:10px;padding:11px 20px;transition:all .2s}
.bp{background:linear-gradient(135deg,var(--accent),var(--accent2));color:#030a12;width:100%}
.bp:hover{opacity:.9}
.bg{background:transparent;border:1px solid var(--border2);color:var(--text);width:100%}
.bg:hover{border-color:var(--accent);color:var(--accent)}
.div{display:flex;align-items:center;gap:12px;margin:20px 0;color:var(--muted);font-size:13px}
.div::before,.div::after{content:'';flex:1;height:1px;background:var(--border2)}
.google-btn{background:#fff;color:#1a1a1a;border:none;gap:10px}
.google-btn:hover{background:#f5f5f5}
.err{color:var(--red);font-size:13px;margin-top:4px}
.fz{padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:14px;font-weight:500}
.fz.ok{background:#00e5a022;border:1px solid #00e5a044;color:var(--green)}
.fz.er{background:#ff525222;border:1px solid #ff525244;color:var(--red)}
.auth-foot{text-align:center;margin-top:24px;color:var(--muted);font-size:14px}
</style>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="lg1">NEXUS</div>
      <div class="lg2">Exchange</div>
    </div>
    @if(session('success'))
    <div class="fz ok">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="fz er">{{ session('error') }}</div>
    @endif
    @yield('content')
  </div>
</div>
</body>
</html>
