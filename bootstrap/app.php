<?php

use App\Http\Middleware\AutoLoginFrontend;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(
            append: [
                HandleAppearance::class,
                HandleInertiaRequests::class,
                AddLinkHeadersForPreloadedAssets::class,
            ],
            prepend: [
                AutoLoginFrontend::class,
            ],
        );

        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
            AutoLoginFrontend::class,
        ]);

        $middleware->alias([
            'verified' => EnsureEmailIsVerified::class,
        ]);

        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
