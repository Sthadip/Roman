<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    public function showForm()
    {
        $user = Auth::user();
        if (!$user->hasApprovedKyc()) {
            return redirect()->route('user.kyc')->with('error','Please complete KYC verification before withdrawing.');
        }
        $uid       = $user->id;
        $coinMeta  = Wallet::supportedCoins();
        $wallets   = Wallet::where('user_id',$uid)->get()->keyBy('coin');
        $usdtBal   = (float)($wallets['USDT']->available ?? 0);
        $liveRates = Withdrawal::liveRates();
        return view('user.withdraw-form', compact('coinMeta','wallets','usdtBal','liveRates'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasApprovedKyc()) {
            return redirect()->route('user.kyc')->with('error','KYC verification required.');
        }

        $request->validate([
            'coin'           => 'required|in:BTC,ETH,USDT',
            'usdt_amount'    => 'required|numeric|min:1',
            'wallet_address' => 'required|string|max:255',
            'network'        => 'nullable|string|max:100',
            'note'           => 'nullable|string|max:500',
        ]);

        $uid       = $user->id;
        $coin      = $request->coin;
        $usdtAmt   = (float)$request->usdt_amount;

        // Always deduct from USDT wallet
        $usdtWallet = Wallet::where('user_id',$uid)->where('coin','USDT')->first();
        if (!$usdtWallet || (float)$usdtWallet->available < $usdtAmt) {
            $avail = $usdtWallet ? number_format((float)$usdtWallet->available, 2) : '0.00';
            return back()->with('wd_error',"Insufficient USDT balance. Available: ₮{$avail}");
        }

        // Calculate coin amount
        $conv     = Withdrawal::usdtToCoin($usdtAmt, $coin);
        $coinAmt  = $conv['coin_amount'];
        $rateUsed = $conv['rate'];

        // Deduct USDT
        $usdtWallet->decrement('available', $usdtAmt);
        $usdtWallet->increment('in_order', $usdtAmt);

        $withdrawal = Withdrawal::create([
            'user_id'        => $uid,
            'coin'           => $coin,
            'amount'         => $usdtAmt,      // legacy: keep as usdt amount
            'usdt_amount'    => $usdtAmt,
            'coin_amount'    => $coinAmt,
            'rate_used'      => $coin !== 'USDT' ? $rateUsed : null,
            'wallet_address' => $request->wallet_address,
            'network'        => $request->network,
            'note'           => $request->note,
            'status'         => 'pending',
        ]);

        $usdtWallet->refresh();
        $desc = $coin === 'USDT'
            ? "Withdrawal of ₮{$usdtAmt} USDT submitted"
            : "Withdrawal of ₮{$usdtAmt} USDT → {$coinAmt} {$coin} @ ₮{$rateUsed} submitted";

        Transaction::record($uid,'withdrawal','USDT',$usdtAmt,'debit',
            $desc,(float)$usdtWallet->available,'withdrawal',$withdrawal->id);

        return redirect()->route('user.withdraw.history')
            ->with('success',"Withdrawal submitted. You will receive {$coinAmt} {$coin}. Awaiting admin approval.");
    }

    public function history()
    {
        $uid         = Auth::id();
        $withdrawals = Withdrawal::where('user_id',$uid)->latest()->paginate(15);
        $coinMeta    = Wallet::supportedCoins();
        return view('user.withdraw-history', compact('withdrawals','coinMeta'));
    }
}
