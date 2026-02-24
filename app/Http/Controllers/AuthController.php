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
    // -------------------------------------------------------------------------
    // Register
    // -------------------------------------------------------------------------

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email',
            'phone'            => 'required|string|max:30',
            'password'         => 'required|string|min:8|confirmed',
            'telegram_chat_id' => 'nullable|string|max:50',
        ]);

        // Tentukan role berdasarkan domain email
        $role = str_ends_with(strtolower($validated['email']), '@tdr.com')
            ? 'admin'
            : 'customer';

        $user = User::create([
            'name'             => $validated['name'],
            'email'            => $validated['email'],
            'phone'            => $validated['phone'],
            'password_hash'    => Hash::make($validated['password']),
            'role'             => $role,
            'telegram_chat_id' => $validated['telegram_chat_id'] ?? null,
            'is_active'        => true,
        ]);

        Auth::login($user);

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Akun admin berhasil dibuat. Selamat datang!');
        }

        return redirect()->route('home')
            ->with('success', 'Registrasi berhasil! Selamat berbelanja.');
    }

    // -------------------------------------------------------------------------
    // Login
    // -------------------------------------------------------------------------

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'));
        }

        return back()
            ->withErrors(['email' => 'Email atau password tidak sesuai.'])
            ->withInput($request->only('email'));
    }

    // -------------------------------------------------------------------------
    // Logout
    // -------------------------------------------------------------------------

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
