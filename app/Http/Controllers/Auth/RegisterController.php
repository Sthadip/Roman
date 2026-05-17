<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm() { return view('auth.register'); }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => ['required','min:8','confirmed','regex:/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9]).+$/'],
        ], ['password.regex' => 'Password must contain at least one letter, one number, and one special character.']);

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role'              => User::ROLE_USER,
            'email_verified_at' => now(),
        ]);

        // Notify admins
        AdminNotification::send(
            'new_user',
            'New User Registered',
            "{$user->name} ({$user->email}) just created an account.",
            $user->id
        );

        Auth::login($user);
        return redirect()->route('user.dashboard')->with('success', 'Welcome to NEXUS Exchange!');
    }
}
