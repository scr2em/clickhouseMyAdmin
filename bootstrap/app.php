<?php

use App\Http\Middleware\BasicAuthMiddleware;
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
        $middleware->web(append: [
            BasicAuthMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'query',
            'database/*/table/*/data',
            'database/*/table/*/update-cell',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
