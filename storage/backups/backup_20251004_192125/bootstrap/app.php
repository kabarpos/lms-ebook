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
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware
        $middleware->append([
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\RateLimitLogger::class,
            \App\Http\Middleware\RequestLogger::class,
            \App\Http\Middleware\PerformanceMonitor::class,
            \App\Http\Middleware\SecurityScanner::class,
        ]);

        // Middleware aliases
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
            'course.access' => \App\Http\Middleware\CheckCourseAccess::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'login.rate.limit' => \App\Http\Middleware\LoginRateLimit::class,
            'rate.limit.logger' => \App\Http\Middleware\RateLimitLogger::class,
            'request.logger' => \App\Http\Middleware\RequestLogger::class,
            'performance.monitor' => \App\Http\Middleware\PerformanceMonitor::class,
            'security.scanner' => \App\Http\Middleware\SecurityScanner::class,
        ]);

        // CSRF exceptions
        $middleware->validateCsrfTokens(except: [
            'midtrans/notification',
            'security/*', // Exclude security report endpoints from CSRF
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
