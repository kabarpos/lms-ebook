<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RateLimitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Login rate limiting
        RateLimiter::for('login', function (Request $request) {
            $config = config('rate_limiting.login');
            $email = $request->input('email', '');
            $ip = $request->ip();
            
            return Limit::perMinutes(
                $config['decay_minutes'] ?? 15,
                $config['max_attempts'] ?? 5
            )->by($email . '|' . $ip)->response(function () {
                return response()->json([
                    'message' => 'Terlalu banyak percobaan login. Silakan coba lagi nanti.',
                    'errors' => [
                        'email' => ['Akun Anda sementara diblokir karena terlalu banyak percobaan login yang gagal.']
                    ]
                ], 429);
            });
        });

        // Registration rate limiting
        RateLimiter::for('registration', function (Request $request) {
            $config = config('rate_limiting.registration');
            
            return Limit::perMinutes(
                $config['decay_minutes'] ?? 60,
                $config['max_attempts'] ?? 3
            )->by($request->ip());
        });

        // Password reset rate limiting
        RateLimiter::for('password-reset', function (Request $request) {
            $config = config('rate_limiting.password_reset');
            
            return Limit::perMinutes(
                $config['decay_minutes'] ?? 60,
                $config['max_attempts'] ?? 3
            )->by($request->ip());
        });

        // Payment rate limiting
        RateLimiter::for('payment', function (Request $request) {
            $config = config('rate_limiting.payment');
            
            return Limit::perMinutes(
                $config['decay_minutes'] ?? 60,
                $config['max_attempts'] ?? 10
            )->by($request->user()?->id ?: $request->ip());
        });

        // API rate limiting
        RateLimiter::for('api', function (Request $request) {
            $config = config('rate_limiting.api');
            
            return Limit::perMinutes(
                $config['decay_minutes'] ?? 1,
                $config['max_attempts'] ?? 60
            )->by($request->user()?->id ?: $request->ip());
        });

        // Webhook rate limiting
        RateLimiter::for('webhook', function (Request $request) {
            $config = config('rate_limiting.webhook');
            
            return Limit::perMinutes(
                $config['decay_minutes'] ?? 1,
                $config['max_attempts'] ?? 60
            )->by($request->ip());
        });
    }
}
