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
    /** GET /register */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /** POST /register */
    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|min:8|confirmed',
            'telegram_chat_id' => 'nullable|string|max:100',
        ]);

        $role = str_ends_with($data['email'], '@tdr.com') ? 'admin' : 'customer';

        $user = User::create([
            'name'             => $data['name'],
            'email'            => $data['email'],
            'password'         => Hash::make($data['password']),
            'role'             => $role,
            'telegram_chat_id' => $data['telegram_chat_id'] ?? null,
        ]);

        Auth::login($user);

        // Tampilkan modal jika telegram belum dihubungkan
        if (empty($user->telegram_chat_id)) {
            session()->flash('show_telegram_modal', true);
        }

        return redirect()->intended(route('home'))
            ->with('success', 'Akun berhasil dibuat. Selamat datang!');
    }

    /** GET /login */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /** POST /login */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Tampilkan modal jika telegram belum dihubungkan
            if (empty($user->telegram_chat_id)) {
                session()->flash('show_telegram_modal', true);
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role === 'affiliate') {
                return redirect()->route('affiliate.dashboard');
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /** POST /logout */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
