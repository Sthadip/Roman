<div class="card">
  <div style="font-size:18px;font-weight:700;margin-bottom:4px">Identity Verification (KYC)</div>
  <div style="font-size:14px;color:var(--muted);margin-bottom:24px">Please provide your details to verify your identity and unlock full platform access.</div>

  <form method="POST" action="{{ route('user.kyc.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="fg">
      <label class="fl">Full Legal Name</label>
      <input type="text" name="full_name" class="fi @error('full_name') fi-err @enderror"
        placeholder="As it appears on your ID" value="{{ old('full_name') }}">
      @error('full_name')<div class="err">{{ $message }}</div>@enderror
    </div>
    <div class="fg">
      <label class="fl">Identification Document Number</label>
      <input type="text" name="id_number" class="fi mono @error('id_number') fi-err @enderror"
        placeholder="Passport, National ID, or Driver's License number" value="{{ old('id_number') }}">
      @error('id_number')<div class="err">{{ $message }}</div>@enderror
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
      <div class="fg">
        <label class="fl">ID Document — Front</label>
        <div style="border:2px dashed var(--border2);border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:border-color .2s" onclick="document.getElementById('id-front').click()" id="front-drop">
          <div style="font-size:28px;margin-bottom:6px">📄</div>
          <div style="font-size:13px;color:var(--muted)" id="front-name">Click to upload front</div>
          <div style="font-size:11px;color:var(--dim);margin-top:4px">JPG, JPEG, PNG, PDF · Max 5MB</div>
        </div>
        <input type="file" name="id_front" id="id-front" accept=".jpg,.jpeg,.png,.pdf" style="display:none"
          onchange="document.getElementById('front-name').textContent=this.files[0]?this.files[0].name:'Click to upload front'">
        @error('id_front')<div class="err">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label class="fl">ID Document — Back</label>
        <div style="border:2px dashed var(--border2);border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:border-color .2s" onclick="document.getElementById('id-back').click()" id="back-drop">
          <div style="font-size:28px;margin-bottom:6px">📄</div>
          <div style="font-size:13px;color:var(--muted)" id="back-name">Click to upload back</div>
          <div style="font-size:11px;color:var(--dim);margin-top:4px">JPG, JPEG, PNG, PDF · Max 5MB</div>
        </div>
        <input type="file" name="id_back" id="id-back" accept=".jpg,.jpeg,.png,.pdf" style="display:none"
          onchange="document.getElementById('back-name').textContent=this.files[0]?this.files[0].name:'Click to upload back'">
        @error('id_back')<div class="err">{{ $message }}</div>@enderror
      </div>
    </div>
    <div class="fg">
      <label class="fl">Residential Address</label>
      <textarea name="address" class="fi @error('address') fi-err @enderror" rows="3"
        placeholder="Full residential address including city, state/province, and country">{{ old('address') }}</textarea>
      @error('address')<div class="err">{{ $message }}</div>@enderror
    </div>
    <div style="background:#00e5ff11;border:1px solid #00e5ff33;border-radius:10px;padding:12px 14px;margin-bottom:18px;font-size:13px;color:var(--accent)">
      ℹ Your documents are encrypted and stored securely. We only use them for identity verification purposes.
    </div>
    <button type="submit" class="btn bp" style="width:100%">Submit KYC Documents</button>
  </form>
</div>
