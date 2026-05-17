@extends('layouts.wallet')
@section('title','Deposit Settings — Admin')
@section('page-title','Deposit Settings')
@section('content')

<div class="tc">
  <div class="tc-main">
    <div class="card">
      {{-- Header --}}
      <div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;padding-bottom:18px;border-bottom:1px solid var(--border)">
        <div style="width:52px;height:52px;border-radius:50%;background:#26A17B22;border:1px solid #26A17B44;display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;color:#26A17B;flex-shrink:0">$</div>
        <div style="flex:1">
          <div style="font-size:18px;font-weight:800">USDT Deposit Settings</div>
          <div style="font-size:13px;color:var(--muted)">Configure the wallet address and payment details users will see when depositing</div>
        </div>
        @if($setting)
          <span class="badge badge-confirmed">Configured</span>
        @else
          <span class="badge badge-pending">Not Set</span>
        @endif
      </div>

      <form method="POST" action="{{ route('admin.deposit-settings.update') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="coin" value="USDT">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
          <div class="fg">
            <label class="fl">Account Name</label>
            <input type="text" name="account_name" class="fi"
              value="{{ old('account_name', $setting->account_name ?? '') }}"
              placeholder="e.g. NEXUS Exchange">
            @error('account_name')<div class="err">{{ $message }}</div>@enderror
          </div>
          <div class="fg">
            <label class="fl">Account / Wallet Number</label>
            <input type="text" name="account_number" class="fi mono"
              value="{{ old('account_number', $setting->account_number ?? '') }}"
              placeholder="Account or wallet number">
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
          <div class="fg">
            <label class="fl">Network <span style="color:var(--dim)">(e.g. TRC20, ERC20)</span></label>
            <input type="text" name="network" class="fi"
              value="{{ old('network', $setting->network ?? 'TRC20') }}"
              placeholder="TRC20">
          </div>
          <div class="fg">
            <label class="fl">Bank Name <span style="color:var(--dim)">(optional)</span></label>
            <input type="text" name="bank_name" class="fi"
              value="{{ old('bank_name', $setting->bank_name ?? '') }}"
              placeholder="e.g. Binance">
          </div>
        </div>

        <div class="fg">
          <label class="fl">Full Wallet Address <span style="color:var(--red)">*</span></label>
          <input type="text" name="wallet_address" class="fi mono"
            value="{{ old('wallet_address', $setting->wallet_address ?? '') }}"
            placeholder="Full USDT wallet address">
          @error('wallet_address')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="fg">
          <label class="fl">Instructions for Users</label>
          <textarea name="instructions" class="fi" rows="4"
            placeholder="Step-by-step deposit instructions shown to users…">{{ old('instructions', $setting->instructions ?? '') }}</textarea>
        </div>

        {{-- QR Code --}}
        <div class="fg">
          <label class="fl">QR Code Image</label>
          @if($setting && $setting->qr_image_path)
          <div style="display:flex;align-items:center;gap:16px;margin-bottom:12px;background:#040f1c;border:1px solid var(--border2);border-radius:10px;padding:14px">
            <img src="{{ Storage::url($setting->qr_image_path) }}" alt="QR"
              style="width:90px;height:90px;object-fit:contain;border-radius:8px;background:#fff;padding:4px">
            <div>
              <div style="font-size:13.5px;font-weight:600;margin-bottom:6px">Current QR Code</div>
              <div style="font-size:12.5px;color:var(--muted);margin-bottom:10px">Displayed to users on the deposit page</div>
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:var(--red)">
                <input type="checkbox" name="remove_qr" value="1" style="accent-color:var(--red)">
                Remove QR image
              </label>
            </div>
          </div>
          @endif
          <input type="file" name="qr_image" class="fi" accept="image/*">
          <div style="font-size:12px;color:var(--dim);margin-top:4px">Max 2MB. PNG recommended for best quality.</div>
        </div>

        <div style="border-top:1px solid var(--border);padding-top:18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
          <div style="font-size:13px;color:var(--muted)">
            @if($setting && $setting->exists)
              Last updated: {{ $setting->updated_at->diffForHumans() }}
            @else
              Not configured yet
            @endif
          </div>
          <button type="submit" class="btn bp">💾 Save USDT Settings</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Right: Live preview --}}
  <div class="tc-side">
    <div class="card" style="margin-bottom:16px">
      <div style="font-size:12px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">Live Preview</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:12px">What users see on the deposit page:</div>

      @if($setting && $setting->wallet_address)
      <div style="background:#040f1c;border:1px solid #26A17B33;border-radius:10px;overflow:hidden">
        <div style="background:#26A17B18;border-bottom:1px solid #26A17B33;padding:10px 14px;font-size:12px;font-weight:700;color:#26A17B;text-transform:uppercase;letter-spacing:.04em">
          $ USDT Payment Details
        </div>
        @if($setting->network)
        <div style="display:flex;justify-content:space-between;padding:10px 14px;border-bottom:1px solid var(--border);font-size:13px">
          <span style="color:var(--dim)">Network</span>
          <span class="badge" style="background:var(--accent2)22;color:var(--accent2);border:1px solid var(--accent2)44">{{ $setting->network }}</span>
        </div>
        @endif
        @if($setting->account_name)
        <div style="display:flex;justify-content:space-between;padding:10px 14px;border-bottom:1px solid var(--border);font-size:13px">
          <span style="color:var(--dim)">Account</span>
          <span style="font-weight:600">{{ $setting->account_name }}</span>
        </div>
        @endif
        <div style="padding:10px 14px;font-size:12px">
          <div style="color:var(--dim);margin-bottom:5px">Wallet Address</div>
          <div class="mono" style="font-size:11.5px;word-break:break-all;color:var(--muted)">{{ $setting->wallet_address }}</div>
        </div>
      </div>

      @if($setting->qr_image_path)
      <div style="text-align:center;margin-top:14px">
        <img src="{{ Storage::url($setting->qr_image_path) }}" alt="QR"
          style="max-width:130px;border-radius:8px;border:2px solid var(--border2);padding:6px;background:#fff">
      </div>
      @endif
      @else
      <div style="text-align:center;padding:24px;color:var(--dim);font-size:13.5px">
        <div style="font-size:28px;margin-bottom:8px;opacity:.4">$</div>
        Save settings to see preview
      </div>
      @endif
    </div>

    <div class="card" style="background:#26A17B08;border-color:#26A17B22">
      <div style="font-size:12px;font-weight:700;color:#26A17B;margin-bottom:8px;text-transform:uppercase;letter-spacing:.06em">ℹ Note</div>
      <div style="font-size:12.5px;color:var(--muted);line-height:1.7">
        Only USDT deposits are accepted. Configure the wallet address users will send funds to, then review and approve incoming deposits manually.
      </div>
    </div>
  </div>
</div>
@endsection
