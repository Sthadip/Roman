<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>NEXUS Exchange — Crypto Wallet Platform</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#030a12;--surface:#091525;--border:#0f2538;--border2:#1a3a50;--text:#e0f7fa;--muted:#5a8aa0;--accent:#00e5ff;--accent2:#00b4d8;--green:#00e5a0}
html,body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;min-height:100vh}
a{color:var(--accent);text-decoration:none}
.nav{display:flex;align-items:center;justify-content:space-between;padding:20px 40px;border-bottom:1px solid var(--border)}
.logo{display:flex;flex-direction:column}
.logo .lg1{font-size:20px;font-weight:800;letter-spacing:-1px}
.logo .lg2{font-size:9px;font-weight:600;letter-spacing:6px;color:var(--accent);text-transform:uppercase}
.nav-btns{display:flex;gap:12px}
.btn{display:inline-flex;align-items:center;justify-content:center;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:600;border-radius:10px;padding:10px 22px;text-decoration:none;transition:all .2s}
.bp{background:linear-gradient(135deg,var(--accent),var(--accent2));color:#030a12}
.bp:hover{opacity:.9}
.bg{background:transparent;border:1px solid var(--border2);color:var(--text)}
.bg:hover{border-color:var(--accent);color:var(--accent)}
.hero{text-align:center;padding:100px 24px 80px;max-width:800px;margin:0 auto}
.hero-badge{display:inline-flex;align-items:center;gap:8px;background:#00e5ff18;border:1px solid #00e5ff33;border-radius:20px;padding:6px 16px;font-size:13px;color:var(--accent);margin-bottom:24px}
.hero h1{font-size:clamp(36px,6vw,64px);font-weight:800;letter-spacing:-2px;line-height:1.1;margin-bottom:20px}
.hero h1 span{background:linear-gradient(135deg,var(--accent),var(--green));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.hero p{font-size:18px;color:var(--muted);max-width:520px;margin:0 auto 36px;line-height:1.7}
.hero-btns{display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap}
.features{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px;max-width:1100px;margin:0 auto;padding:0 24px 80px}
.feat{background:var(--surface);border:1px solid var(--border2);border-radius:16px;padding:28px}
.feat-icon{font-size:32px;margin-bottom:14px}
.feat h3{font-size:16px;font-weight:700;margin-bottom:8px}
.feat p{font-size:14px;color:var(--muted);line-height:1.6}
.coins{display:flex;align-items:center;justify-content:center;gap:16px;flex-wrap:wrap;padding:0 24px 60px}
.coin-badge{display:flex;align-items:center;gap:8px;background:var(--surface);border:1px solid var(--border2);border-radius:10px;padding:10px 16px;font-size:14px;font-weight:600}
footer{text-align:center;padding:30px;border-top:1px solid var(--border);color:var(--muted);font-size:14px}
@media(max-width:640px){.nav{padding:16px 20px}.nav-btns .bg{display:none}}
</style>
</head>
<body>
<nav class="nav">
  <div class="logo"><div class="lg1">NEXUS</div><div class="lg2">Exchange</div></div>
  <div class="nav-btns">
    <a href="{{ route('login') }}" class="btn bg">Sign In</a>
    <a href="{{ route('register') }}" class="btn bp">Get Started</a>
  </div>
</nav>

<div class="hero">
  <div class="hero-badge">⚡ Secure Crypto Exchange Platform</div>
  <h1>Trade & Manage <span>Crypto Assets</span> with Confidence</h1>
  <p>NEXUS Exchange gives you a secure multi-coin wallet with full deposit and withdrawal management — all in one sleek dashboard.</p>
  <div class="hero-btns">
    <a href="{{ route('register') }}" class="btn bp" style="padding:14px 32px;font-size:16px">Create Free Account</a>
    <a href="{{ route('login') }}" class="btn bg" style="padding:14px 28px;font-size:15px">Sign In</a>
  </div>
</div>

<div class="coins">
  <div class="coin-badge" style="color:#F7931A">₿ Bitcoin</div>
  <div class="coin-badge" style="color:#627EEA">Ξ Ethereum</div>
  <div class="coin-badge" style="color:#F3BA2F">B BNB</div>
  <div class="coin-badge" style="color:#00AAE4">✕ XRP</div>
  <div class="coin-badge" style="color:#26A17B">$ USDT</div>
  <div class="coin-badge" style="color:#85BB65">$ USDT</div>
</div>

<div class="features">
  <div class="feat">
    <div class="feat-icon">🔐</div>
    <h3>Secure Authentication</h3>
    <p>Email/password or Google OAuth login with mandatory email verification to protect your account.</p>
  </div>
  <div class="feat">
    <div class="feat-icon">💳</div>
    <h3>Multi-Coin Wallet</h3>
    <p>Manage BTC, ETH, BNB, XRP, USDT balances from a single, beautiful dashboard.</p>
  </div>
  <div class="feat">
    <div class="feat-icon">✅</div>
    <h3>Admin Verification</h3>
    <p>All deposits and withdrawals are reviewed and confirmed by our admin team for maximum safety.</p>
  </div>
  <div class="feat">
    <div class="feat-icon">📊</div>
    <h3>Full Ledger</h3>
    <p>Complete transaction history with credit/debit tracking, balance snapshots, and status updates.</p>
  </div>
</div>

<footer>© {{ date('Y') }} NEXUS Exchange. All rights reserved.</footer>
</body>
</html>
