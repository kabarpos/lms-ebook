<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class LoginRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);
        $config = config('rate_limiting.login');
        $maxAttempts = $config['max_attempts'] ?? 5;
        $decayMinutes = $config['decay_minutes'] ?? 15;
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            // Log suspicious activity
            \Log::warning('Login rate limit exceeded', [
                'ip' => $request->ip(),
                'email' => $request->input('email'),
                'user_agent' => $request->userAgent(),
                'available_in' => $seconds
            ]);
            
            throw new ThrottleRequestsException(
                'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
                null,
                [],
                $seconds
            );
        }
        
        $response = $next($request);
        
        // If login failed (redirect back with errors), increment the rate limiter
        if ($response->isRedirect() && $request->session()->has('errors')) {
            RateLimiter::hit($key, $decayMinutes * 60);
            
            // Log failed login attempt if configured
            if (config('rate_limiting.log_events', true)) {
                \Log::info('Failed login attempt', [
                    'ip' => $request->ip(),
                    'email' => $request->input('email'),
                    'attempts_remaining' => $maxAttempts - RateLimiter::attempts($key)
                ]);
            }
        }
        
        // If login successful, clear the rate limiter
        if ($response->isRedirect() && !$request->session()->has('errors')) {
            RateLimiter::clear($key);
            
            if (config('rate_limiting.log_events', true)) {
                \Log::info('Successful login', [
                    'ip' => $request->ip(),
                    'email' => $request->input('email')
                ]);
            }
        }
        
        return $response;
    }
    
    /**
     * Resolve request signature for rate limiting.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $email = $request->input('email', '');
        $ip = $request->ip();
        
        // Combine email and IP for more specific rate limiting
        return 'login_attempts:' . sha1($email . '|' . $ip);
    }
}