<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Basic Security Headers
        $this->setBasicSecurityHeaders($response);
        
        // Advanced Security Headers
        $this->setAdvancedSecurityHeaders($response, $request);
        
        // Content Security Policy
        $this->setContentSecurityPolicy($response, $request);
        
        // HTTPS-specific headers
        if ($request->isSecure()) {
            $this->setHttpsSecurityHeaders($response);
        }
        
        // Log security violations if any
        $this->logSecurityViolations($request);
        
        return $response;
    }
    
    /**
     * Set basic security headers
     */
    private function setBasicSecurityHeaders(Response $response): void
    {
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        
        // Relax COEP to avoid blocking cross-origin assets like ui-avatars
        $response->headers->set('Cross-Origin-Embedder-Policy', 'unsafe-none');
        
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
    }
    
    /**
     * Set advanced security headers
     */
    private function setAdvancedSecurityHeaders(Response $response, Request $request): void
    {
        // Enhanced Permissions Policy
        $permissionsPolicy = [
            'camera' => '()',
            'microphone' => '()',
            'geolocation' => '()',
            'payment' => '(self)',
            'usb' => '()',
            'magnetometer' => '()',
            'gyroscope' => '()',
            'accelerometer' => '()',
            'autoplay' => '(self)',
            'encrypted-media' => '(self)',
            'fullscreen' => '(self)',
            'picture-in-picture' => '()',
            'sync-xhr' => '()',
            'web-share' => '(self)',
        ];
        
        $permissionsPolicyString = collect($permissionsPolicy)
            ->map(fn($value, $key) => "$key=$value")
            ->implode(', ');
            
        $response->headers->set('Permissions-Policy', $permissionsPolicyString);
        
        // Server information hiding
        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');
        $response->headers->set('Server', 'LMS-Server');
        
        // Cache control for sensitive pages
        if ($this->isSensitivePage($request)) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }
    }
    
    /**
     * Set HTTPS-specific security headers
     */
    private function setHttpsSecurityHeaders(Response $response): void
    {
        // HSTS with preload
        $response->headers->set(
            'Strict-Transport-Security', 
            'max-age=31536000; includeSubDomains; preload'
        );
        
        // Expect-CT for certificate transparency
        $response->headers->set(
            'Expect-CT', 
            'max-age=86400, enforce, report-uri="' . url('/security/ct-report') . '"'
        );
        
        // Public Key Pinning (HPKP) - Only in production with valid certificates
        if (config('app.env') === 'production' && config('security.enable_hpkp', false)) {
            $pins = config('security.hpkp_pins', []);
            if (!empty($pins)) {
                $hpkpValue = collect($pins)
                    ->map(fn($pin) => "pin-sha256=\"$pin\"")
                    ->implode('; ');
                $hpkpValue .= '; max-age=5184000; includeSubDomains; report-uri="' . url('/security/hpkp-report') . '"';
                $response->headers->set('Public-Key-Pins', $hpkpValue);
            }
        }
    }
    
    /**
     * Set Content Security Policy
     */
    private function setContentSecurityPolicy(Response $response, Request $request): void
    {
        $nonce = $this->generateNonce();
        
        // Store nonce in request for use in views
        $request->attributes->set('csp_nonce', $nonce);
        
        // Get Vite dev server URLs for development (IPv4 and IPv6 loopback)
        $isLocal = config('app.env') === 'local' || config('app.env') === 'development';
        $viteDevServerV4 = $isLocal ? 'http://localhost:5173' : '';
        $viteDevServerV6 = $isLocal ? 'http://[::1]:5173' : '';
        
        $isProd = config('app.env') === 'production';

        $scriptCdn = "https://app.sandbox.midtrans.com https://app.midtrans.com https://code.jquery.com https://cdnjs.cloudflare.com";
        $styleCdn = "https://fonts.googleapis.com https://fonts.bunny.net https://cdnjs.cloudflare.com";

        $scriptSrc = "'self' " . ($isProd ? "'nonce-{$nonce}' " : "'unsafe-inline' ") . $scriptCdn
            . ($viteDevServerV4 ? " $viteDevServerV4" : "")
            . ($viteDevServerV6 ? " $viteDevServerV6" : "");
        $scriptSrcElem = $scriptSrc;

        $styleSrc = "'self' " . ($isProd ? "'nonce-{$nonce}' " : "'unsafe-inline' ") . $styleCdn
            . ($viteDevServerV4 ? " $viteDevServerV4" : "")
            . ($viteDevServerV6 ? " $viteDevServerV6" : "");
        $styleSrcElem = $styleSrc;

        $csp = [
            "default-src" => "'self'",
            "script-src" => $scriptSrc,
            "script-src-elem" => $scriptSrcElem,
            "style-src" => $styleSrc,
            "style-src-elem" => $styleSrcElem,
            "font-src" => "'self' data: https://fonts.gstatic.com https://fonts.bunny.net",
            "img-src" => "'self' data: https: https://ui-avatars.com",
            "connect-src" => "'self' https://api.sandbox.midtrans.com https://api.midtrans.com https://ui-avatars.com ws: wss:"
                . ($viteDevServerV4 ? " $viteDevServerV4 ws://localhost:5173" : "")
                . ($viteDevServerV6 ? " $viteDevServerV6 ws://[::1]:5173" : ""),
            "object-src" => "'none'",
            "media-src" => "'self' https://www.youtube.com https://www.youtube-nocookie.com",
            "frame-src" => "'self' https://www.youtube.com https://www.youtube-nocookie.com https://app.sandbox.midtrans.com https://app.midtrans.com",
            "base-uri" => "'self'",
            "form-action" => "'self'",
            "frame-ancestors" => "'none'",
        ];

        // In production, tighten rules: remove unsafe-eval and unsafe-inline (handled above)
        
        // Only add upgrade-insecure-requests and block-all-mixed-content in production
        if (config('app.env') === 'production') {
            $csp["upgrade-insecure-requests"] = "";
            $csp["block-all-mixed-content"] = "";
            $csp["report-uri"] = url('/security/csp-report');
            $csp["report-to"] = 'csp-endpoint';
        }
        
        $cspString = collect($csp)
            ->map(fn($value, $key) => $value ? "$key $value" : $key)
            ->implode('; ');
        
        // Apply CSP based on environment
        if (config('app.env') === 'production') {
            $response->headers->set('Content-Security-Policy', $cspString);
        } else {
            // Use report-only in development to avoid blocking resources
            $response->headers->set('Content-Security-Policy-Report-Only', $cspString);
        }
        
        // Set Reporting API endpoint
        if (config('app.env') === 'production') {
            $reportTo = json_encode([
                'group' => 'csp-endpoint',
                'max_age' => 10886400,
                'endpoints' => [
                    ['url' => url('/security/csp-report')]
                ]
            ]);
            $response->headers->set('Report-To', $reportTo);
        }
    }
    
    /**
     * Generate CSP nonce
     */
    private function generateNonce(): string
    {
        return base64_encode(random_bytes(16));
    }
    
    /**
     * Check if current page is sensitive
     */
    private function isSensitivePage(Request $request): bool
    {
        $sensitivePaths = [
            '/login',
            '/register',
            '/password',
            '/admin',
            '/profile',
            '/payment',
            '/checkout',
        ];
        
        $currentPath = $request->getPathInfo();
        
        return collect($sensitivePaths)->some(function ($path) use ($currentPath) {
            return str_starts_with($currentPath, $path);
        });
    }
    
    /**
     * Log security violations
     */
    private function logSecurityViolations(Request $request): void
    {
        // Check for suspicious headers or patterns
        $suspiciousHeaders = [
            'X-Forwarded-Host',
            'X-Original-URL',
            'X-Rewrite-URL',
        ];
        
        foreach ($suspiciousHeaders as $header) {
            if ($request->hasHeader($header)) {
                Log::channel('security')->warning('Suspicious header detected', [
                    'header' => $header,
                    'value' => $request->header($header),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                ]);
            }
        }
        
        // Check for potential XSS attempts in query parameters
        $queryString = $request->getQueryString();
        if ($queryString && $this->containsXssPatterns($queryString)) {
            Log::channel('security')->warning('Potential XSS attempt detected', [
                'query_string' => $queryString,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);
        }
    }
    
    /**
     * Check for XSS patterns
     */
    private function containsXssPatterns(string $input): bool
    {
        $xssPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe[^>]*>/i',
            '/<object[^>]*>/i',
            '/<embed[^>]*>/i',
        ];
        
        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
}
