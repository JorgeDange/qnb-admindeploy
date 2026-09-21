<?php

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
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
            'admin.role' => \App\Http\Middleware\EnsureAdminHasRole::class,
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            return route('admin.login');
        });

        $middleware->redirectUsersTo('/admin');

        $middleware->trustProxies(at: '0.0.0.0/0');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
