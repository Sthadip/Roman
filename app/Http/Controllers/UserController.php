<?php
namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $uid  = $user->id;
        Wallet::ensureForUser($uid);

        $coinMeta = Wallet::supportedCoins();
        $wallets  = Wallet::where('user_id', $uid)
            ->orderByRaw("FIELD(coin,'BTC','ETH','USDT')")
            ->get()->keyBy('coin');

        $totalDeposited    = Deposit::where('user_id',$uid)->where('status','confirmed')->sum('amount');
        $totalWithdrawn    = Withdrawal::where('user_id',$uid)->where('status','approved')->sum('amount');
        $pendingDeposits   = Deposit::where('user_id',$uid)->where('status','pending')->count();
        $pendingWithdrawals= Withdrawal::where('user_id',$uid)->where('status','pending')->count();

        $recentDeposits    = Deposit::where('user_id',$uid)->latest()->take(4)->get()->map(fn($d)=>[
            'type'=>'deposit','coin'=>$d->coin,'amount'=>$d->amount,'status'=>$d->status,'at'=>$d->created_at,
        ]);
        $recentWithdrawals = Withdrawal::where('user_id',$uid)->latest()->take(4)->get()->map(fn($w)=>[
            'type'=>'withdrawal','coin'=>$w->coin,'amount'=>$w->amount,'status'=>$w->status,'at'=>$w->created_at,
        ]);
        $recent = $recentDeposits->concat($recentWithdrawals)->sortByDesc('at')->take(6)->values();

        return view('user.dashboard', compact(
            'user','coinMeta','wallets','totalDeposited','totalWithdrawn',
            'pendingDeposits','pendingWithdrawals','recent'
        ));
    }

    public function editProfile()   { return view('user.profile', ['user' => Auth::user()]); }

    public function updateProfile(Request $request)
    {
        $request->validate(['name'=>'required|string|max:255']);
        Auth::user()->update(['name'=>$request->name]);
        return back()->with('success','Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        if ($user->isGoogleUser()) return back()->with('error','Google accounts cannot change password here.');
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required','min:8','confirmed','regex:/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9]).+$/'],
        ], ['password.regex'=>'Password must contain at least one letter, one number, and one symbol.']);
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password'=>'Current password is incorrect.']);
        }
        $user->update(['password'=>Hash::make($request->password)]);
        return back()->with('success','Password updated.');
    }
}
