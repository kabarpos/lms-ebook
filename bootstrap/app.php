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
        //
        $middleware->trustProxies(at: '*');
        
        // Add security headers globally
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        
        // Add rate limit logging globally
        $middleware->append(\App\Http\Middleware\RateLimitLogger::class);
        
        $middleware->validateCsrfTokens(except: [
            '/booking/payment/midtrans/notification', // Exclude Midtrans notification route
        ]);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'check.subscription' => \App\Http\Middleware\CheckSubscription::class,
            'check.subscription.or.admin' => \App\Http\Middleware\CheckSubscriptionOrAdmin::class,
            'check.course.access' => \App\Http\Middleware\CheckCourseAccess::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'login.rate.limit' => \App\Http\Middleware\LoginRateLimit::class,
            'rate.limit.logger' => \App\Http\Middleware\RateLimitLogger::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
