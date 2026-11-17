<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('member.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($credentials['login']);
        $password = $credentials['password'];

        // Determine if login is mobile or email
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'mobile';

        // Also allow stripping +91 or spaces from mobile
        if (!$isEmail) {
            $loginInput = preg_replace('/[^0-9]/', '', $loginInput);
            if (strlen($loginInput) > 10 && str_starts_with($loginInput, '91')) {
                $loginInput = substr($loginInput, 2);
            }
        }

        if (Auth::attempt([$field => $loginInput, 'password' => $password], $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors(['login' => 'Your account is deactivated. Please contact society administration.']);
            }

            AuditLog::log('user_login', "User logged in ({$user->name})");

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } else {
                return redirect()->intended(route('member.dashboard'));
            }
        }

        return back()->withErrors([
            'login' => 'Invalid mobile number/email or password.',
        ])->onlyInput('login');
    }

    public function quickDemoLogin(string $role, ?int $memberIndex = 1)
    {
        if ($role === 'admin') {
            $user = User::where('role', 'admin')->first();
        } else {
            // member 1 or specified member
            $user = User::where('role', 'member')->skip(max(0, $memberIndex - 1))->first();
        }

        if ($user) {
            Auth::login($user);
            AuditLog::log('quick_demo_login', "Quick login as {$user->name} ({$user->role})");
            return $user->isAdmin()
                ? redirect()->route('admin.dashboard')->with('success', 'Logged in as Society Administrator')
                : redirect()->route('member.dashboard')->with('success', 'Logged in as Member: ' . $user->name);
        }

        return redirect()->route('login')->with('error', 'User account not found.');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::log('user_logout', 'User logged out');
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been safely logged out.');
    }
}
