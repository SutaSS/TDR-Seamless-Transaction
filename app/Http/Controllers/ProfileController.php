<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'nullable|string|max:30',
            'email'            => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telegram_chat_id' => 'nullable|string|max:50',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil diperbarui.');
    }

    /**
     * POST /profile/telegram  — quick-save Telegram Chat ID (from popup modal).
     */
    public function saveTelegramId(Request $request): RedirectResponse
    {
        $request->validate([
            'telegram_chat_id' => 'required|string|regex:/^-?[0-9]+$/|max:20',
        ], [
            'telegram_chat_id.regex' => 'Chat ID hanya berisi angka.',
        ]);

        Auth::user()->update(['telegram_chat_id' => $request->telegram_chat_id]);

        return back()->with('success', 'Telegram berhasil dihubungkan! ✅ Kamu akan menerima notifikasi pesanan.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'Password lama tidak cocok.'])->withInput();
        }

        $user->update(['password_hash' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
