@extends('layouts.wallet')
@section('title','Invest — NEXUS')
@section('page-title','Investment Plans')
@section('content')

{{-- Stats --}}
<div class="sg" style="margin-bottom:20px">
  <div class="sc">
    <div class="sc-icon" style="color:var(--accent)">💵</div>
    <div class="sc-val" style="color:var(--accent)">${{ number_format((float)$usdBalance,2) }}</div>
    <div class="sc-lbl">USDT Available</div>
  </div>
  <div class="sc">
    <div class="sc-icon" style="color:var(--green)">📈</div>
    <div class="sc-val" style="color:var(--green)">${{ number_format((float)$totalInvested,2) }}</div>
    <div class="sc-lbl">Total Invested</div>
  </div>
  <div class="sc">
    <div class="sc-icon" style="color:var(--yellow)">⚡</div>
    <div class="sc-val" style="color:var(--yellow)">{{ $activeCount }}</div>
    <div class="sc-lbl">Active Plans</div>
  </div>
  <div class="sc">
    <div class="sc-icon" style="color:var(--purple)">💰</div>
    <div class="sc-val" style="color:var(--purple)">${{ number_format((float)$totalReturned,2) }}</div>
    <div class="sc-lbl">Total Returned</div>
  </div>
</div>

<div class="tc">
  <div class="tc-main">
    {{-- Packages --}}
    <div style="margin-bottom:24px">
      <div class="sh"><h2>Investment Packages</h2></div>
      @if($packages->isEmpty())
      <div class="card" style="text-align:center;padding:48px;color:var(--muted)">
        No investment packages available yet. Check back soon.
      </div>
      @else
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px">
        @foreach($packages as $pkg)
        <div class="card pkg-card" style="cursor:pointer;transition:all .25s;border-color:var(--border2)" data-id="{{ $pkg->id }}" data-name="{{ $pkg->name }}" data-min="{{ $pkg->min_amount }}" data-max="{{ $pkg->max_amount ?? '' }}" data-rate="{{ $pkg->return_rate }}" data-days="{{ $pkg->duration_days }}" onclick="selectPackage(this)">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px">
            <div>
              <div style="font-size:15px;font-weight:700">{{ $pkg->name }}</div>
              <div style="font-size:12px;color:var(--muted);margin-top:2px">{{ $pkg->duration_days }} day{{ $pkg->duration_days>1?'s':'' }} lock period</div>
            </div>
            <div style="background:#00e5a018;border:1px solid #00e5a033;border-radius:10px;padding:4px 10px;text-align:center">
              <div style="font-size:16px;font-weight:800;color:var(--green)">{{ number_format($pkg->return_rate,1) }}%</div>
              <div style="font-size:10px;color:var(--muted)">return</div>
            </div>
          </div>
          @if($pkg->description)
          <div style="font-size:12.5px;color:var(--muted);margin-bottom:12px">{{ $pkg->description }}</div>
          @endif
          <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:14px">
            <div style="display:flex;justify-content:space-between;font-size:12.5px">
              <span style="color:var(--dim)">Min invest</span>
              <span class="mono" style="font-weight:600;color:var(--text)">${{ number_format((float)$pkg->min_amount,2) }}</span>
            </div>
            @if($pkg->max_amount)
            <div style="display:flex;justify-content:space-between;font-size:12.5px">
              <span style="color:var(--dim)">Max invest</span>
              <span class="mono" style="font-weight:600;color:var(--text)">${{ number_format((float)$pkg->max_amount,2) }}</span>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;font-size:12.5px">
              <span style="color:var(--dim)">Profit on $100</span>
              <span class="mono" style="font-weight:600;color:var(--green)">+${{ number_format($pkg->calcProfit(100),2) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:12.5px">
              <span style="color:var(--dim)">Return on $100</span>
              <span class="mono" style="font-weight:600;color:var(--accent)">${{ number_format($pkg->calcReturn(100),2) }}</span>
            </div>
          </div>
          <button class="btn bp" style="width:100%;font-size:13px" onclick="selectPackage(document.querySelector('[data-id=\'{{ $pkg->id }}\']'));openModal('invest-modal')">
            Invest Now
          </button>
        </div>
        @endforeach
      </div>
      @endif
    </div>

    {{-- My investments --}}
    <div class="card">
      <div class="sh"><h2>My Investments</h2></div>
      @forelse($investments as $inv)
      <div style="padding:14px 0;border-bottom:1px solid var(--border)">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;gap:10px;flex-wrap:wrap">
          <div>
            <div style="font-size:14px;font-weight:700">{{ $inv->package->name ?? 'Package' }}</div>
            <div style="font-size:12px;color:var(--muted)">{{ $inv->starts_at->format('M d') }} → {{ $inv->ends_at->format('M d, Y') }}</div>
          </div>
          <div style="text-align:right">
            <div class="mono" style="font-size:15px;font-weight:700;color:var(--green)">${{ number_format((float)$inv->amount,2) }}</div>
            <div style="font-size:12px;color:var(--accent)">+${{ number_format((float)$inv->profit,2) }} profit</div>
          </div>
          <span class="badge badge-{{ $inv->status }}">{{ ucfirst($inv->status) }}</span>
        </div>
        @if($inv->isActive())
        <div style="background:var(--border);border-radius:100px;height:5px;overflow:hidden">
          <div style="width:{{ $inv->progress_percent }}%;height:100%;background:linear-gradient(90deg,var(--accent),var(--green));border-radius:100px"></div>
        </div>
        <div style="font-size:11.5px;color:var(--dim);margin-top:4px;display:flex;justify-content:space-between">
          <span>{{ $inv->progress_percent }}% complete</span>
          <span>{{ $inv->days_remaining }} day(s) left</span>
        </div>
        @elseif($inv->isCompleted())
        <div style="font-size:12.5px;color:var(--green)">✓ Completed · Returned ${{ number_format((float)$inv->expected_return,2) }}</div>
        @endif
      </div>
      @empty
      <div style="text-align:center;padding:32px;color:var(--muted);font-size:14px">No investments yet. Choose a package above to start.</div>
      @endforelse
      @if($investments->hasPages())<div class="pg">{!! $investments->links()->toHtml() !!}</div>@endif
    </div>
  </div>

  <div class="tc-side">
    <div class="card">
      <div style="font-size:14px;font-weight:700;margin-bottom:14px">How It Works</div>
      @foreach([['↓','Select a package','Choose from our available investment plans'],['💵','Invest USDT','Amount is deducted from your USDT balance and locked'],['⏳','Wait for maturity','Your funds grow during the lock period'],['💰','Auto payout','Principal + profit credited automatically when the period ends']] as [$icon,$title,$desc])
      <div style="display:flex;gap:10px;margin-bottom:14px">
        <div style="width:32px;height:32px;border-radius:50%;background:var(--accent)18;border:1px solid var(--accent)33;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0">{{ $icon }}</div>
        <div><div style="font-size:13px;font-weight:600">{{ $title }}</div><div style="font-size:12px;color:var(--muted)">{{ $desc }}</div></div>
      </div>
      @endforeach
    </div>
  </div>
</div>

@push('modals')
<div class="modal-bg" id="invest-modal">
  <div class="modal-box">
    <div class="modal-head">
      <h3 id="inv-modal-title">Invest</h3>
      <button class="modal-close" onclick="closeModal('invest-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div style="background:#040f1c;border:1px solid var(--border2);border-radius:10px;padding:12px 14px;margin-bottom:18px">
        <div style="display:flex;justify-content:space-between;margin-bottom:6px">
          <span style="font-size:12.5px;color:var(--dim)">Duration</span><span class="mono" style="font-weight:600" id="inv-days">—</span>
        </div>
        <div style="display:flex;justify-content:space-between;margin-bottom:6px">
          <span style="font-size:12.5px;color:var(--dim)">Return Rate</span><span class="mono" style="font-weight:600;color:var(--green)" id="inv-rate">—</span>
        </div>
        <div style="display:flex;justify-content:space-between">
          <span style="font-size:12.5px;color:var(--dim)">Your USDT Balance</span><span class="mono" style="font-weight:600;color:var(--accent)">${{ number_format((float)$usdBalance,2) }}</span>
        </div>
      </div>
      <form method="POST" action="{{ route('user.invest.store') }}">
        @csrf
        <input type="hidden" name="package_id" id="inv-pkg-id">
        <div class="fg">
          <label class="fl">Amount (USDT)</label>
          <input type="number" name="amount" id="inv-amount" class="fi" placeholder="0.00" step="0.01" min="0.01" oninput="calcReturn()">
          <div style="font-size:12px;color:var(--dim);margin-top:4px" id="inv-min-note"></div>
        </div>
        <div style="background:#040f1c;border:1px solid var(--border2);border-radius:10px;padding:12px 14px;margin-bottom:18px">
          <div style="display:flex;justify-content:space-between;margin-bottom:4px">
            <span style="font-size:12.5px;color:var(--dim)">Estimated Profit</span>
            <span class="mono" style="font-weight:600;color:var(--green)" id="inv-profit-out">$0.00</span>
          </div>
          <div style="display:flex;justify-content:space-between">
            <span style="font-size:12.5px;color:var(--dim)">Total Return</span>
            <span class="mono" style="font-size:15px;font-weight:700;color:var(--accent)" id="inv-return-out">$0.00</span>
          </div>
        </div>
        <div style="background:#ffd6000a;border:1px solid #ffd60022;border-radius:10px;padding:11px 13px;margin-bottom:16px;font-size:12.5px;color:var(--yellow)">
          ⚠ Invested amount will be locked for the full duration. Early withdrawal is not available.
        </div>
        <button type="submit" class="btn bp" style="width:100%">Confirm Investment</button>
      </form>
    </div>
  </div>
</div>
<script>
var selPkg = null;
function selectPackage(el) {
  selPkg = {id:el.dataset.id,name:el.dataset.name,min:parseFloat(el.dataset.min),max:el.dataset.max?parseFloat(el.dataset.max):null,rate:parseFloat(el.dataset.rate),days:parseInt(el.dataset.days)};
  document.querySelectorAll('.pkg-card').forEach(function(c){c.style.borderColor='var(--border2)';c.style.boxShadow='';});
  el.style.borderColor='var(--accent)';el.style.boxShadow='0 0 0 2px #00e5ff22';
  document.getElementById('inv-modal-title').textContent='Invest in '+selPkg.name;
  document.getElementById('inv-days').textContent=selPkg.days+' day'+(selPkg.days>1?'s':'');
  document.getElementById('inv-rate').textContent=selPkg.rate.toFixed(2)+'%';
  document.getElementById('inv-pkg-id').value=selPkg.id;
  document.getElementById('inv-min-note').textContent='Min: $'+selPkg.min.toFixed(2)+(selPkg.max?' · Max: $'+selPkg.max.toFixed(2):'');
  document.getElementById('inv-amount').value='';
  calcReturn();
}
function calcReturn(){
  var amt=parseFloat(document.getElementById('inv-amount').value)||0;
  var profit=selPkg?parseFloat((amt*selPkg.rate/100).toFixed(2)):0;
  var ret=amt+profit;
  document.getElementById('inv-profit-out').textContent='$'+profit.toFixed(2);
  document.getElementById('inv-return-out').textContent='$'+ret.toFixed(2);
}
</script>
@endpush
@endsection
