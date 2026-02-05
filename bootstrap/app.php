<?php

use App\Http\Middleware\CallbackThrottle;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\HandleRedirects;
use App\Http\Middleware\RedirectTrailingSlash;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(prepend: [
            RedirectTrailingSlash::class,
            HandleRedirects::class,
        ]);
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
        $middleware->alias([
            'callback.throttle' => CallbackThrottle::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
