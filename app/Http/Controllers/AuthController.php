<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    // GET /register 
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    // POST /register 
    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|min:8|confirmed',
            'telegram_chat_id' => 'nullable|string|max:100',
        ]);

        $role = str_ends_with($data['email'], '@tdr-hpz.com') ? 'admin' : 'customer';

        $user = User::create([
            'name'             => $data['name'],
            'email'            => $data['email'],
            'password'         => Hash::make($data['password']),
            'role'             => $role,
            'telegram_chat_id' => $data['telegram_chat_id'] ?? null,
        ]);

        Auth::login($user);

        if (empty($user->telegram_chat_id)) {
            return redirect()->route('telegram.setup')
                ->with('success', 'Akun berhasil dibuat. Selamat datang!');
        }

        return redirect()->intended(route('home'))
            ->with('success', 'Akun berhasil dibuat. Selamat datang!');
    }

    // GET /login
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    // POST /login
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Email @tdr-hpz.com selalu jadi admin (handle akun lama)
            if (str_ends_with($user->email, '@tdr-hpz.com') && $user->role !== 'admin') {
                $user->update(['role' => 'admin']);
                $user->refresh();
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role === 'affiliate') {
                return redirect()->route('affiliate.dashboard');
            }

            if (empty($user->telegram_chat_id)) {
                return redirect()->route('telegram.setup');
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // POST /logout 
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
