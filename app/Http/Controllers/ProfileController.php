<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'telegram_chat_id' => 'nullable|string|max:50',
            'name'             => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->update($validated);

        return back()->with('success', 'Profil diperbarui.');
    }
}
