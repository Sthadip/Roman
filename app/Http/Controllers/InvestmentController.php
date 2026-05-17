<?php
namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentPackage;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestmentController extends Controller
{
    public function index()
    {
        $packages    = InvestmentPackage::where('is_active', true)->orderBy('duration_days')->get();
        $investments = Investment::where('user_id', Auth::id())->with('package')->latest()->paginate(10);
        $wallet      = Wallet::where('user_id', Auth::id())->where('coin', 'USDT')->first();
        $usdBalance  = $wallet ? (float)$wallet->available : 0;

        // Stats
        $totalInvested   = Investment::where('user_id', Auth::id())->sum('amount');
        $totalReturned   = Investment::where('user_id', Auth::id())->where('status', 'completed')->sum('expected_return');
        $activeCount     = Investment::where('user_id', Auth::id())->where('status', 'active')->count();

        return view('user.invest', compact('packages','investments','usdBalance','totalInvested','totalReturned','activeCount'));
    }

    public function store(Request $request)
    {
        // KYC check
        if (!Auth::user()->hasApprovedKyc()) {
            return back()->with('error', 'You must complete KYC verification before investing.');
        }

        $request->validate([
            'package_id' => 'required|exists:investment_packages,id',
            'amount'     => 'required|numeric|min:0.01',
        ]);

        $package = InvestmentPackage::findOrFail($request->package_id);
        $amount  = (float)$request->amount;
        $uid     = Auth::id();

        if (!$package->is_active) {
            return back()->with('error', 'This investment package is no longer available.');
        }
        if ($amount < (float)$package->min_amount) {
            return back()->with('error', "Minimum investment is \${$package->min_amount}.");
        }
        if ($package->max_amount && $amount > (float)$package->max_amount) {
            return back()->with('error', "Maximum investment is \${$package->max_amount}.");
        }

        $wallet = Wallet::where('user_id', $uid)->where('coin', 'USDT')->first();
        if (!$wallet || (float)$wallet->available < $amount) {
            return back()->with('error', 'Insufficient USDT balance.');
        }

        // Deduct from wallet
        $wallet->decrement('available', $amount);
        $wallet->increment('in_order', $amount);
        $wallet->refresh();

        $profit   = $package->calcProfit($amount);
        $expected = $package->calcReturn($amount);
        $startsAt = now();
        $endsAt   = now()->addDays($package->duration_days);

        $investment = Investment::create([
            'user_id'         => $uid,
            'package_id'      => $package->id,
            'amount'          => $amount,
            'profit'          => $profit,
            'expected_return' => $expected,
            'coin'            => 'USDT',
            'status'          => 'active',
            'starts_at'       => $startsAt,
            'ends_at'         => $endsAt,
        ]);

        Transaction::record($uid,'investment','USDT',$amount,'debit',
            "Investment in {$package->name} — locked for {$package->duration_days} day(s)",
            (float)$wallet->available,'investment',$investment->id);

        return back()->with('success', "Investment of \${$amount} in {$package->name} activated! Matures in {$package->duration_days} day(s).");
    }

    // Command/cron: complete matured investments
    public static function processMatured(): void
    {
        $matured = Investment::where('status','active')->where('ends_at','<=',now())->with('user')->get();
        foreach ($matured as $inv) {
            $wallet = Wallet::where('user_id',$inv->user_id)->where('coin','USDT')->first();
            if (!$wallet) continue;

            // Release locked amount + add profit
            $wallet->decrement('in_order', $inv->amount);
            $wallet->increment('available', $inv->expected_return);
            $wallet->refresh();

            $inv->update(['status'=>'completed','completed_at'=>now()]);

            Transaction::record($inv->user_id,'profit','USDT',$inv->expected_return,'credit',
                "Investment matured — {$inv->package->name} returned \${$inv->expected_return}",
                (float)$wallet->available,'investment',$inv->id);
        }
    }
}
