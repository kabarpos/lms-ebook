<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class SecurityScanner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip security scanning in testing environment
        if (app()->environment('testing')) {
            return $next($request);
        }
        
        // Skip security scanning for localhost in development
        if (app()->environment('local') && in_array($request->ip(), ['127.0.0.1', '::1', 'localhost'])) {
            return $next($request);
        }
        
        // Skip scanning for static assets and service worker to avoid false positives
        if ($this->isStaticAsset($request)) {
            return $next($request);
        }
        
        // Check for various security threats
        $this->scanForSqlInjection($request);
        $this->scanForXssAttempts($request);
        $this->scanForPathTraversal($request);
        $this->scanForCommandInjection($request);
        $this->scanForFileInclusion($request);
        $this->scanForSuspiciousUserAgents($request);
        $this->scanForBotActivity($request);
        $this->scanForBruteForceAttempts($request);
        
        // Check if IP should be blocked
        if ($this->shouldBlockRequest($request)) {
            return $this->blockRequest($request);
        }
        
        return $next($request);
    }
    
    /**
     * Scan for SQL injection attempts
     */
    private function scanForSqlInjection(Request $request): void
    {
        if (!config('security.database.detect_sql_injection', true)) {
            return;
        }
        
        $sqlPatterns = [
            '/(\bunion\b.*\bselect\b)/i',
            '/(\bselect\b.*\bfrom\b.*\bwhere\b)/i',
            '/(\bdrop\b.*\btable\b)/i',
            '/(\binsert\b.*\binto\b)/i',
            '/(\bupdate\b.*\bset\b)/i',
            '/(\bdelete\b.*\bfrom\b)/i',
            '/(\bor\b.*1\s*=\s*1)/i',
            '/(\band\b.*1\s*=\s*1)/i',
            '/(\bor\b.*\btrue\b)/i',
            '/(\bunion\b.*\ball\b.*\bselect\b)/i',
            '/(\'.*\bor\b.*\'.*=.*\')/i',
            '/(\bhaving\b.*\bcount\b.*\*)/i',
            '/(\bexec\b.*\bxp_)/i',
            '/(\bsp_executesql\b)/i',
        ];
        
        $allInput = array_merge(
            $request->query->all(),
            $request->request->all(),
            $request->headers->all()
        );
        
        foreach ($allInput as $key => $value) {
            if (is_string($value)) {
                foreach ($sqlPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $this->logSecurityThreat('sql_injection', $request, [
                            'parameter' => $key,
                            'value' => $value,
                            'pattern' => $pattern,
                        ]);
                        
                        $this->incrementThreatScore($request, 'sql_injection', 10);
                        break;
                    }
                }
            }
        }
    }
    
    /**
     * Scan for XSS attempts
     */
    private function scanForXssAttempts(Request $request): void
    {
        if (!config('security.monitoring.log_xss_attempts', true)) {
            return;
        }
        
        $xssPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe[^>]*>/i',
            '/<object[^>]*>/i',
            '/<embed[^>]*>/i',
            '/<applet[^>]*>/i',
            '/<meta[^>]*>/i',
            '/<link[^>]*>/i',
            '/expression\s*\(/i',
            '/vbscript:/i',
            '/data:text\/html/i',
            '/<svg[^>]*onload/i',
            '/<img[^>]*onerror/i',
        ];
        
        $allInput = array_merge(
            $request->query->all(),
            $request->request->all()
        );
        
        foreach ($allInput as $key => $value) {
            if (is_string($value)) {
                foreach ($xssPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $this->logSecurityThreat('xss_attempt', $request, [
                            'parameter' => $key,
                            'value' => $value,
                            'pattern' => $pattern,
                        ]);
                        
                        $this->incrementThreatScore($request, 'xss', 8);
                        break;
                    }
                }
            }
        }
    }
    
    /**
     * Scan for path traversal attempts
     */
    private function scanForPathTraversal(Request $request): void
    {
        $pathTraversalPatterns = [
            '/\.\.\//',
            '/\.\.\\\\/',
            '/%2e%2e%2f/',
            '/%2e%2e\\\\/',
            '/\.\.\%2f/',
            '/\.\.\%5c/',
            '/\.\.%252f/',
            '/\.\.%255c/',
        ];
        
        $allInput = array_merge(
            $request->query->all(),
            $request->request->all(),
            [$request->getPathInfo()]
        );
        
        foreach ($allInput as $key => $value) {
            if (is_string($value)) {
                foreach ($pathTraversalPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $this->logSecurityThreat('path_traversal', $request, [
                            'parameter' => $key,
                            'value' => $value,
                            'pattern' => $pattern,
                        ]);
                        
                        $this->incrementThreatScore($request, 'path_traversal', 7);
                        break;
                    }
                }
            }
        }
    }
    
    /**
     * Scan for command injection attempts
     */
    private function scanForCommandInjection(Request $request): void
    {
        $commandPatterns = [
            '/;\s*(cat|ls|pwd|whoami|id|uname)/i',
            '/\|\s*(cat|ls|pwd|whoami|id|uname)/i',
            '/&&\s*(cat|ls|pwd|whoami|id|uname)/i',
            '/`[^`]*`/',
            '/\$\([^)]*\)/',
            '/;\s*rm\s+-rf/i',
            '/;\s*wget\s+/i',
            '/;\s*curl\s+/i',
            '/;\s*nc\s+/i',
            '/;\s*netcat\s+/i',
        ];
        
        $allInput = array_merge(
            $request->query->all(),
            $request->request->all()
        );
        
        foreach ($allInput as $key => $value) {
            if (is_string($value)) {
                foreach ($commandPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $this->logSecurityThreat('command_injection', $request, [
                            'parameter' => $key,
                            'value' => $value,
                            'pattern' => $pattern,
                        ]);
                        
                        $this->incrementThreatScore($request, 'command_injection', 9);
                        break;
                    }
                }
            }
        }
    }
    
    /**
     * Scan for file inclusion attempts
     */
    private function scanForFileInclusion(Request $request): void
    {
        $fileInclusionPatterns = [
            '/\/etc\/passwd/i',
            '/\/etc\/shadow/i',
            '/\/proc\/self\/environ/i',
            '/\/proc\/version/i',
            '/\/windows\/system32/i',
            '/php:\/\/filter/i',
            '/php:\/\/input/i',
            '/data:\/\/text/i',
            '/file:\/\/\//i',
            '/expect:\/\//i',
        ];
        
        $allInput = array_merge(
            $request->query->all(),
            $request->request->all()
        );
        
        foreach ($allInput as $key => $value) {
            if (is_string($value)) {
                foreach ($fileInclusionPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $this->logSecurityThreat('file_inclusion', $request, [
                            'parameter' => $key,
                            'value' => $value,
                            'pattern' => $pattern,
                        ]);
                        
                        $this->incrementThreatScore($request, 'file_inclusion', 8);
                        break;
                    }
                }
            }
        }
    }
    
    /**
     * Scan for suspicious user agents
     */
    private function scanForSuspiciousUserAgents(Request $request): void
    {
        $userAgent = $request->userAgent();
        
        if (!$userAgent) {
            $this->logSecurityThreat('missing_user_agent', $request);
            $this->incrementThreatScore($request, 'suspicious_ua', 3);
            return;
        }
        
        $suspiciousPatterns = [
            '/sqlmap/i',
            '/nikto/i',
            '/nmap/i',
            '/masscan/i',
            '/zap/i',
            '/burp/i',
            '/acunetix/i',
            '/nessus/i',
            '/openvas/i',
            '/w3af/i',
            '/havij/i',
            '/pangolin/i',
            '/python-requests/i',
            '/curl/i',
            '/wget/i',
            '/libwww/i',
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                $this->logSecurityThreat('suspicious_user_agent', $request, [
                    'user_agent' => $userAgent,
                    'pattern' => $pattern,
                ]);
                
                $this->incrementThreatScore($request, 'suspicious_ua', 6);
                break;
            }
        }
    }
    
    /**
     * Scan for bot activity
     */
    private function scanForBotActivity(Request $request): void
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        
        // More lenient request frequency for production
        $requestThreshold = app()->environment('production') ? 120 : 60;
        
        // Check request frequency
        $requestKey = "requests_per_minute_{$ip}";
        $requestCount = Cache::get($requestKey, 0);
        
        if ($requestCount > $requestThreshold) { // More lenient for production
            $this->logSecurityThreat('high_frequency_requests', $request, [
                'requests_per_minute' => $requestCount,
            ]);
            
            $this->incrementThreatScore($request, 'bot_activity', 5);
        }
        
        Cache::put($requestKey, $requestCount + 1, 60);
        
        // Check for bot-like behavior patterns
        $botPatterns = [
            '/bot/i',
            '/crawler/i',
            '/spider/i',
            '/scraper/i',
            '/harvester/i',
        ];
        
        foreach ($botPatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                // This might be legitimate, so lower score
                $this->incrementThreatScore($request, 'bot_activity', 2);
                break;
            }
        }
    }
    
    /**
     * Scan for brute force attempts
     */
    private function scanForBruteForceAttempts(Request $request): void
    {
        $ip = $request->ip();
        $path = $request->getPathInfo();
        
        // More lenient thresholds for production
        $loginThreshold = app()->environment('production') ? 20 : 10;
        $adminThreshold = app()->environment('production') ? 10 : 5;
        
        // Check for login attempts (POST only)
        if ($request->isMethod('POST') && (str_contains($path, '/login') || str_contains($path, '/auth'))) {
            $loginKey = "login_attempts_{$ip}";
            $attempts = Cache::get($loginKey, 0);
            
            if ($attempts > $loginThreshold) { // More lenient for production
                $this->logSecurityThreat('brute_force_login', $request, [
                    'attempts' => $attempts,
                ]);
                
                $this->incrementThreatScore($request, 'brute_force', 8);
            }
            
            Cache::put($loginKey, $attempts + 1, 3600); // 1 hour
        }
        
        // Check for admin panel access attempts (only when unauthenticated)
        if (!auth()->check() && (str_contains($path, '/admin') || str_contains($path, '/wp-admin'))) {
            $adminKey = "admin_attempts_{$ip}";
            $attempts = Cache::get($adminKey, 0);
            
            if ($attempts > $adminThreshold) {
                $this->logSecurityThreat('admin_brute_force', $request, [
                    'attempts' => $attempts,
                ]);
                
                $this->incrementThreatScore($request, 'brute_force', 7);
            }
            
            Cache::put($adminKey, $attempts + 1, 3600);
        }
    }
    
    /**
     * Check if request should be blocked
     */
    private function shouldBlockRequest(Request $request): bool
    {
        $ip = $request->ip();
        $threatScore = Cache::get("threat_score_{$ip}", 0);
        
        // Increase threshold for production to reduce false positives
        $blockingThreshold = app()->environment('production') ? 100 : 50;
        
        // Block if threat score is too high
        if ($threatScore >= $blockingThreshold) {
            return true;
        }
        
        // Check if IP is in blocklist
        $blockedIps = Cache::get('blocked_ips', []);
        if (in_array($ip, $blockedIps)) {
            return true;
        }
        
        // Do not globally rate limit normal traffic here; only block on high threat score or explicit blocklist
        return false;
    }

    /**
     * Determine if the request is for static assets or service worker
     */
    private function isStaticAsset(Request $request): bool
    {
        // Skip common static paths and service worker
        return $request->is('sw.js')
            || $request->is('favicon.ico')
            || $request->is('robots.txt')
            || $request->is('assets/*')
            || $request->is('css/*')
            || $request->is('js/*')
            || $request->is('images/*')
            || $request->is('fonts/*')
            || $request->is('vendor/*')
            || $request->is('livewire/*');
    }
    
    /**
     * Block the request
     */
    private function blockRequest(Request $request): Response
    {
        $this->logSecurityThreat('request_blocked', $request, [
            'reason' => 'High threat score or blocked IP',
        ]);
        
        return response('Access Denied', 403);
    }
    
    /**
     * Log security threat
     */
    private function logSecurityThreat(string $type, Request $request, array $additional = []): void
    {
        Log::channel('security')->warning("Security threat detected: {$type}", array_merge([
            'type' => $type,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'timestamp' => now(),
        ], $additional));
    }
    
    /**
     * Increment threat score for IP
     */
    private function incrementThreatScore(Request $request, string $type, int $score): void
    {
        $ip = $request->ip();
        $key = "threat_score_{$ip}";
        $currentScore = Cache::get($key, 0);
        $newScore = $currentScore + $score;
        
        // Store threat score for 1 hour
        Cache::put($key, $newScore, 3600);
        
        // Track threat type
        $typeKey = "threat_type_{$type}_{$ip}";
        Cache::increment($typeKey, 1);
        Cache::put($typeKey, Cache::get($typeKey, 0), 3600);
        
        // If score is very high, add to blocklist temporarily
        if ($newScore >= 100) {
            $blockedIps = Cache::get('blocked_ips', []);
            $blockedIps[] = $ip;
            Cache::put('blocked_ips', array_unique($blockedIps), 7200); // 2 hours
            
            Log::channel('security')->critical('IP blocked due to high threat score', [
                'ip' => $ip,
                'threat_score' => $newScore,
                'threat_type' => $type,
            ]);
        }
    }
}