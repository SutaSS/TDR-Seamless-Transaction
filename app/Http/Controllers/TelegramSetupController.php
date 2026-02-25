<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TelegramSetupController extends Controller
{
    /** GET /telegram/setup */
    public function show(): View
    {
        return view('telegram.setup', [
            'botUsername' => config('services.telegram.bot_username', 'TDRHPZBot'),
        ]);
    }

    /** POST /telegram/setup/skip — simpan pilihan lanjut tanpa setup */
    public function skip(Request $request): RedirectResponse
    {
        return redirect()->intended(route('home'));
    }
}
