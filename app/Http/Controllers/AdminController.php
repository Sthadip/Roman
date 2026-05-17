<?php
namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\Investment;
use App\Models\KycVerification;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users'                => User::where('role','user')->count(),
            'confirmed_deposits_count'   => Deposit::where('status','confirmed')->count(),
            'confirmed_deposits_sum'     => Deposit::where('status','confirmed')->sum('amount'),
            'pending_deposits_count'     => Deposit::where('status','pending')->count(),
            'confirmed_withdrawals_count'=> Withdrawal::where('status','approved')->count(),
            'confirmed_withdrawals_sum'  => Withdrawal::where('status','approved')->sum('amount'),
            'pending_withdrawals_count'  => Withdrawal::where('status','pending')->count(),
            'pending_kyc'                => KycVerification::where('status','pending')->count(),
            'active_investments_sum'     => Investment::where('status','active')->sum('amount'),
        ];
        $pendingDeposits    = Deposit::with('user')->where('status','pending')->latest()->take(6)->get();
        $pendingWithdrawals = Withdrawal::with('user')->where('status','pending')->latest()->take(6)->get();
        $recentUsers        = User::where('role','user')->latest()->take(5)->get();
        $notifications      = AdminNotification::latest()->take(10)->get();
        $unreadCount        = AdminNotification::unreadCount();
        $onlineUsers        = UserActivity::with('user')->where('last_seen_at','>=',now()->subMinutes(5))->latest('last_seen_at')->get();

        return view('admin.dashboard', compact(
            'stats','pendingDeposits','pendingWithdrawals','recentUsers',
            'notifications','unreadCount','onlineUsers'
        ));
    }

    public function notifications()
    {
        AdminNotification::where('is_read', false)->update(['is_read' => true]);
        $notifications = AdminNotification::with('refUser')->latest()->paginate(30);
        return view('admin.notifications', compact('notifications'));
    }

    public function markNotificationsRead()
    {
        AdminNotification::where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function users(Request $request)
    {
        $query = User::where('role','user');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q)=>$q->where('name','like',"%$s%")->orWhere('email','like',"%$s%"));
        }
        $users = $query->latest()->paginate(15)->withQueryString();
        return view('admin.users', compact('users'));
    }

    public function userDetail(User $user)
    {
        $activity    = UserActivity::where('user_id', $user->id)->first();
        $deposits    = Deposit::where('user_id', $user->id)->latest()->take(10)->get();
        $withdrawals = Withdrawal::where('user_id', $user->id)->latest()->take(10)->get();
        $wallets     = Wallet::where('user_id', $user->id)->get()->keyBy('coin');
        $coinMeta    = Wallet::supportedCoins();
        $kyc         = KycVerification::where('user_id', $user->id)->latest()->first();
        $txCount     = Transaction::where('user_id', $user->id)->count();
        return view('admin.user-detail', compact('user','activity','deposits','withdrawals','wallets','coinMeta','kyc','txCount'));
    }

    public function deleteUser(User $user)
    {
        if ($user->id === Auth::id()) return back()->with('error','Cannot delete yourself.');
        $user->delete();
        return back()->with('success','User deleted.');
    }

    // ── Deposits ─────────────────────────────────────────────────
    public function deposits(Request $request)
    {
        $query = Deposit::with('user');
        if ($request->filled('status'))  $query->where('status',$request->status);
        if ($request->filled('network')) $query->where('network',$request->network);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q)=>$q->where('transaction_id','like',"%$s%")
                ->orWhereHas('user',fn($u)=>$u->where('name','like',"%$s%")->orWhere('email','like',"%$s%")));
        }
        $deposits = $query->latest()->paginate(20)->withQueryString();
        $counts = [
            'pending'   => Deposit::where('status','pending')->count(),
            'confirmed' => Deposit::where('status','confirmed')->count(),
            'rejected'  => Deposit::where('status','rejected')->count(),
        ];
        $networkMeta = [
            'BTC' => ['icon'=>'₿','color'=>'#F7931A','bg'=>'#F7931A22','name'=>'Bitcoin'],
            'ETH' => ['icon'=>'Ξ','color'=>'#627EEA','bg'=>'#627EEA22','name'=>'Ethereum'],
        ];
        return view('admin.deposits', compact('deposits','counts','networkMeta'));
    }

    public function depositDetail(Deposit $deposit)
    {
        $deposit->load('user','reviewer');
        return view('admin.deposit-detail', compact('deposit'));
    }

    public function approveDeposit(Deposit $deposit)
    {
        if (!$deposit->isPending()) return back()->with('error','Already reviewed.');
        Wallet::ensureForUser($deposit->user_id);
        // Always credit USDT wallet regardless of the network used to deposit
        $wallet = Wallet::where('user_id',$deposit->user_id)->where('coin','USDT')->first();
        $wallet->increment('available',$deposit->amount);
        $wallet->refresh();
        $deposit->update(['status'=>'confirmed','reviewed_by'=>Auth::id(),'reviewed_at'=>now()]);
        Transaction::record($deposit->user_id,'deposit','USDT',$deposit->amount,'credit',
            'Deposit confirmed via '.$deposit->network.' network',(float)$wallet->available,'deposit',$deposit->id);
        return redirect()->route('admin.deposits')->with('success','Deposit approved.');
    }

    public function rejectDeposit(Deposit $deposit)
    {
        if (!$deposit->isPending()) return back()->with('error','Already reviewed.');
        $deposit->update(['status'=>'rejected','reviewed_by'=>Auth::id(),'reviewed_at'=>now()]);
        return redirect()->route('admin.deposits')->with('success','Deposit rejected.');
    }

    // ── Withdrawals ───────────────────────────────────────────────
    public function withdrawals(Request $request)
    {
        $query = Withdrawal::with('user');
        if ($request->filled('status')) $query->where('status',$request->status);
        if ($request->filled('coin'))   $query->where('coin',$request->coin);
        $withdrawals = $query->latest()->paginate(20)->withQueryString();
        $coinMeta    = Wallet::supportedCoins();
        return view('admin.withdrawals', compact('withdrawals','coinMeta'));
    }

    public function approveWithdrawal(Withdrawal $withdrawal)
    {
        if (!$withdrawal->isPending()) return back()->with('error','Already reviewed.');
        // Always operate on USDT wallet (all withdrawals deduct USDT)
        $usdtAmt = (float)($withdrawal->usdt_amount ?: $withdrawal->amount);
        $wallet  = Wallet::where('user_id',$withdrawal->user_id)->where('coin','USDT')->first();
        if ($wallet) { $wallet->decrement('in_order', $usdtAmt); $wallet->refresh(); }
        $withdrawal->update(['status'=>'approved','reviewed_by'=>Auth::id(),'reviewed_at'=>now()]);
        Transaction::record($withdrawal->user_id,'withdrawal','USDT',$usdtAmt,'debit',
            'Withdrawal approved – '.(float)($withdrawal->coin_amount ?: $usdtAmt).' '.$withdrawal->coin.' sent',
            $wallet?(float)$wallet->available:0,'withdrawal',$withdrawal->id);
        return back()->with('success','Withdrawal approved.');
    }

    public function rejectWithdrawal(Withdrawal $withdrawal)
    {
        if (!$withdrawal->isPending()) return back()->with('error','Already reviewed.');
        // Return USDT to available
        $usdtAmt = (float)($withdrawal->usdt_amount ?: $withdrawal->amount);
        $wallet  = Wallet::where('user_id',$withdrawal->user_id)->where('coin','USDT')->first();
        if ($wallet) {
            $wallet->decrement('in_order', $usdtAmt);
            $wallet->increment('available', $usdtAmt);
        }
        $withdrawal->update(['status'=>'rejected','reviewed_by'=>Auth::id(),'reviewed_at'=>now()]);
        return back()->with('success','Withdrawal rejected and USDT returned to user.');
    }

    // ── KYC ───────────────────────────────────────────────────────
    public function kyc(Request $request)
    {
        $query = KycVerification::with('user');
        if ($request->filled('status')) $query->where('status',$request->status);
        $kycs = $query->latest()->paginate(20)->withQueryString();
        return view('admin.kyc', compact('kycs'));
    }

    public function approveKyc(KycVerification $kyc)
    {
        $kyc->update(['status'=>'approved','reviewed_by'=>Auth::id(),'reviewed_at'=>now()]);
        return back()->with('success','KYC approved.');
    }

    public function rejectKyc(KycVerification $kyc)
    {
        $kyc->update(['status'=>'rejected','reviewed_by'=>Auth::id(),'reviewed_at'=>now()]);
        return back()->with('success','KYC rejected.');
    }

    // ── Investments ───────────────────────────────────────────────
    public function investments(Request $request)
    {
        $query = Investment::with(['user','package']);
        if ($request->filled('status')) $query->where('status',$request->status);
        $investments = $query->latest()->paginate(20)->withQueryString();
        return view('admin.investments', compact('investments'));
    }

    // ── Manual Credit ──────────────────────────────────────────────────────────

    public function creditForm(User $user)
    {
        Wallet::ensureForUser($user->id);
        $usdWallet = Wallet::where('user_id',$user->id)->where('coin','USDT')->first();
        $recentCredits = Transaction::where('user_id',$user->id)
            ->where('type','manual_credit')
            ->latest()->limit(5)->get();
        return view('admin.credit-user', compact('user','usdWallet','recentCredits'));
    }

    public function creditSubmit(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'note'   => 'required|string|max:300',
            'reason' => 'required|in:bonus,correction,reward,refund,other',
        ]);

        Wallet::ensureForUser($user->id);
        $wallet = Wallet::where('user_id',$user->id)->where('coin','USDT')->first();
        $wallet->increment('available', $request->amount);
        $wallet->refresh();

        $reasonLabel = match($request->reason) {
            'bonus'      => 'Bonus',
            'correction' => 'Balance Correction',
            'reward'     => 'Reward',
            'refund'     => 'Refund',
            default      => 'Manual Credit',
        };

        Transaction::record(
            $user->id,
            'manual_credit',
            'USDT',
            (float)$request->amount,
            'credit',
            $reasonLabel.': '.$request->note,
            (float)$wallet->available
        );

        $adminName = Auth::user()->name;
        return redirect()
            ->route('admin.users.detail', $user->id)
            ->with('success', '$'.number_format($request->amount,2).' USDT manually credited to '.$user->name.' by '.$adminName.'.');
    }

}
