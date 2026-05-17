@extends('layouts.wallet')
@section('title',($package?'Edit':'Create').' Package — NEXUS')
@section('page-title',($package?'Edit':'Create').' Investment Package')
@section('content')

<div style="max-width:580px">
  <div style="margin-bottom:16px">
    <a href="{{ route('superadmin.packages') }}" class="btn bg bsm">← Back to Packages</a>
  </div>
  <div class="card">
    <div style="font-size:17px;font-weight:700;margin-bottom:4px">{{ $package ? 'Edit: '.$package->name : 'New Investment Package' }}</div>
    <div style="font-size:13.5px;color:var(--muted);margin-bottom:22px">{{ $package ? 'Update the package details below.' : 'Define a new investment plan for users.' }}</div>

    <form method="POST" action="{{ $package ? route('superadmin.packages.update',$package->id) : route('superadmin.packages.store') }}">
      @csrf
      @if($package) @method('PATCH') @endif

      <div class="fg">
        <label class="fl">Package Name *</label>
        <input type="text" name="name" class="fi @error('name') fi-err @enderror" placeholder="e.g. 7-Day Starter" value="{{ old('name',$package->name??'') }}">
        @error('name')<div class="err">{{ $message }}</div>@enderror
      </div>

      <div class="fg">
        <label class="fl">Description</label>
        <textarea name="description" class="fi" rows="3" placeholder="Brief description for users...">{{ old('description',$package->description??'') }}</textarea>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div class="fg">
          <label class="fl">Duration (Days) *</label>
          <input type="number" name="duration_days" class="fi @error('duration_days') fi-err @enderror" placeholder="e.g. 7" min="1" value="{{ old('duration_days',$package->duration_days??'') }}">
          @error('duration_days')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="fg">
          <label class="fl">Return Rate (%) *</label>
          <input type="number" name="return_rate" class="fi @error('return_rate') fi-err @enderror" placeholder="e.g. 5.00" step="0.01" min="0.01" value="{{ old('return_rate',$package->return_rate??'') }}">
          @error('return_rate')<div class="err">{{ $message }}</div>@enderror
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div class="fg">
          <label class="fl">Min Amount (USDT) *</label>
          <input type="number" name="min_amount" class="fi @error('min_amount') fi-err @enderror" placeholder="e.g. 50" step="0.01" min="0.01" value="{{ old('min_amount',$package->min_amount??'') }}">
          @error('min_amount')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="fg">
          <label class="fl">Max Amount (USDT) <span style="color:var(--dim)">(optional)</span></label>
          <input type="number" name="max_amount" class="fi @error('max_amount') fi-err @enderror" placeholder="Leave blank = unlimited" step="0.01" value="{{ old('max_amount',$package->max_amount??'') }}">
          @error('max_amount')<div class="err">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- Live preview --}}
      <div style="background:#040f1c;border:1px solid var(--border2);border-radius:12px;padding:16px;margin-bottom:18px" id="preview">
        <div style="font-size:11px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px">Return Preview</div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;text-align:center">
          <div><div style="font-size:11.5px;color:var(--dim)">On $100</div><div class="mono" style="font-weight:700;color:var(--green)" id="prev100">$0.00</div></div>
          <div><div style="font-size:11.5px;color:var(--dim)">On $500</div><div class="mono" style="font-weight:700;color:var(--green)" id="prev500">$0.00</div></div>
          <div><div style="font-size:11.5px;color:var(--dim)">On $1000</div><div class="mono" style="font-weight:700;color:var(--green)" id="prev1k">$0.00</div></div>
        </div>
      </div>

      <div class="fg" style="display:flex;align-items:center;gap:10px">
        <input type="checkbox" name="is_active" value="1" id="is_active"
          {{ old('is_active',$package?$package->is_active:true) ? 'checked' : '' }}
          style="accent-color:var(--accent);width:16px;height:16px;cursor:pointer">
        <label for="is_active" style="font-size:13.5px;font-weight:500;cursor:pointer">Package is Active (visible to users)</label>
      </div>

      <div style="display:flex;gap:10px;margin-top:8px">
        <a href="{{ route('superadmin.packages') }}" class="btn bg" style="flex:1">Cancel</a>
        <button type="submit" class="btn bp" style="flex:2">{{ $package ? 'Update Package' : 'Create Package' }}</button>
      </div>
    </form>
  </div>
</div>
<script>
var rateInput = document.querySelector('[name="return_rate"]');
function updatePreview(){
  var r = parseFloat(rateInput.value)||0;
  document.getElementById('prev100').textContent = '$'+(100*(r/100)).toFixed(2)+' → $'+(100+100*(r/100)).toFixed(2);
  document.getElementById('prev500').textContent = '$'+(500*(r/100)).toFixed(2)+' → $'+(500+500*(r/100)).toFixed(2);
  document.getElementById('prev1k').textContent  = '$'+(1000*(r/100)).toFixed(2)+' → $'+(1000+1000*(r/100)).toFixed(2);
}
if(rateInput) rateInput.addEventListener('input', updatePreview);
updatePreview();
</script>
@endsection
