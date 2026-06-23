<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
 ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'owner.active' => \App\Http\Middleware\CheckOwnerStatus::class,
            'admin'        => \App\Http\Middleware\EnsureAdmin::class,
            'student'      => \App\Http\Middleware\EnsureStudent::class,
            'maintenance'  => \App\Http\Middleware\EnsureMaintenance::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'test-data',
            'login',
        ]);
    }) 
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
