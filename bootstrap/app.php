<?php

use App\Http\Controllers\SwitchLangController;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/mobile-api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['api', 'user-lang'])
                ->prefix('api/dashboard')
                ->group(base_path('routes/dashboard-api.php'));

            Route::middleware(['api', 'user-lang'])
                ->prefix('api')
                ->group(base_path('routes/mobile-api.php'));

            Route::middleware(['api', 'user-lang'])
                ->prefix('api')
                ->get('switch-lang', SwitchLangController::class);
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('api', [
            EnsureFrontendRequestsAreStateful::class,
            SubstituteBindings::class,
        ])->alias([
            'user-lang' => \App\Http\Middleware\SetUserLang::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
