<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm() { return view('auth.login'); }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Notify admins of user login (not for admin logins)
            if ($user->isUser()) {
                AdminNotification::send(
                    'user_login',
                    'User Logged In',
                    "{$user->name} ({$user->email}) just logged in.",
                    $user->id
                );
            }

            if ($user->isSuperAdmin()) return redirect()->route('superadmin.dashboard');
            if ($user->isAdmin())      return redirect()->route('admin.dashboard');
            return redirect()->route('user.dashboard');
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
