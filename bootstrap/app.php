<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',       // TODO [PHASE 1 - Andika]: Route webhook Midtrans
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies (ngrok, reverse proxy) — fixes 419 PAGE EXPIRED via HTTPS tunnel
        $middleware->trustProxies(at: '*');

        // Exempt webhook dari CSRF
        $middleware->validateCsrfTokens(except: ['api/webhook/payment']);

        // Redirect guest (belum login) ke login; user sudah login ke home
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('home'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
