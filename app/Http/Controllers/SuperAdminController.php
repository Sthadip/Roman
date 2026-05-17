<?php
namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users'              => User::where('role','user')->count(),
            'total_admins'             => User::where('role','admin')->count(),
            'confirmed_deposits'       => Deposit::where('status','confirmed')->sum('amount'),
            'pending_deposits'         => Deposit::where('status','pending')->count(),
            'confirmed_withdrawals'    => Withdrawal::where('status','approved')->sum('amount'),
            'pending_withdrawals'      => Withdrawal::where('status','pending')->count(),
        ];
        $admins        = User::whereIn('role',['admin','super_admin'])->latest()->get();
        $notifications = AdminNotification::latest()->take(8)->get();
        $unreadCount   = AdminNotification::unreadCount();
        return view('superadmin.dashboard', compact('stats','admins','notifications','unreadCount'));
    }

    public function admins(Request $request)
    {
        $query = User::whereIn('role',['admin','super_admin']);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q)=>$q->where('name','like',"%$s%")->orWhere('email','like',"%$s%"));
        }
        $admins = $query->latest()->paginate(15)->withQueryString();
        return view('superadmin.admins', compact('admins'));
    }

    public function promoteToAdmin(User $user)
    {
        if ($user->isSuperAdmin()) return back()->with('error','Cannot change Super Admin role.');
        $user->update(['role' => User::ROLE_ADMIN]);
        return back()->with('success', "{$user->name} promoted to Admin.");
    }

    public function demoteToUser(User $user)
    {
        if ($user->isSuperAdmin()) return back()->with('error','Cannot demote Super Admin.');
        if ($user->id === Auth::id()) return back()->with('error','Cannot demote yourself.');
        $user->update(['role' => User::ROLE_USER]);
        return back()->with('success', "{$user->name} demoted to User.");
    }
}
