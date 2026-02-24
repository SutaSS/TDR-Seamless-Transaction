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
        // TODO [PHASE 1 - Andika]: Exempt webhook route dari CSRF (jika diperlukan)
        // $middleware->validateCsrfTokens(except: ['api/webhook/payment']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
