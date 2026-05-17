<?php
namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $uid = Auth::id();
        Wallet::ensureForUser($uid);
        $coinMeta = Wallet::supportedCoins();
        $wallets  = Wallet::where('user_id', $uid)
            ->orderByRaw("FIELD(coin,'BTC','ETH','USDT')")
            ->get();
        return view('user.wallet', compact('wallets', 'coinMeta'));
    }

    public function depositForm()
    {
        $coinMeta = Wallet::supportedCoins();
        return view('user.deposit-form', compact('coinMeta'));
    }

    public function depositSubmit(Request $request)
    {
        $request->validate([
            'network'        => 'required|in:BTC,ETH',
            'amount'         => 'required|numeric|min:1',
            'transaction_id' => 'required|string|max:255',
            'screenshot'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'note'           => 'nullable|string|max:500',
        ]);

        $screenshotPath = $request->file('screenshot')->store('deposits/screenshots', 'public');

        // Coin is always USDT — network just indicates which chain the user sent on
        Deposit::create([
            'user_id'         => Auth::id(),
            'network'         => $request->network,
            'amount'          => $request->amount,
            'transaction_id'  => $request->transaction_id,
            'screenshot_path' => $screenshotPath,
            'note'            => $request->note,
            'status'          => 'pending',
        ]);

        return redirect()->route('user.deposit.history')
            ->with('success', 'Deposit submitted! Awaiting admin confirmation.');
    }

    public function depositHistory()
    {
        $deposits = Deposit::where('user_id', Auth::id())->latest()->paginate(15);
        return view('user.deposit-history', compact('deposits'));
    }

    public function depositDetail(Deposit $deposit)
    {
        // Ensure the deposit belongs to the authenticated user
        if ($deposit->user_id !== Auth::id()) {
            abort(403);
        }
        $coinMeta = Wallet::supportedCoins();
        return view('user.deposit-detail', compact('deposit', 'coinMeta'));
    }
}
