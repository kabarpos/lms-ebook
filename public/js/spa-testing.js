/**
 * SPA Testing Script untuk FilamentPHP Dashboard
 * Jalankan script ini di browser console untuk monitoring SPA performance
 */

class SPATestingMonitor {
    constructor() {
        this.navigationTimes = [];
        this.errors = [];
        this.startTime = null;
        this.isMonitoring = false;
        
        this.init();
    }
    
    init() {
        console.log('🚀 SPA Testing Monitor initialized');
        this.setupErrorTracking();
        this.setupNavigationTracking();
        this.setupPerformanceTracking();
    }
    
    setupErrorTracking() {
        // Track JavaScript errors
        window.addEventListener('error', (event) => {
            this.logError('JavaScript Error', {
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno,
                error: event.error
            });
        });
        
        // Track unhandled promise rejections
        window.addEventListener('unhandledrejection', (event) => {
            this.logError('Unhandled Promise Rejection', {
                reason: event.reason
            });
        });
        
        // Track Livewire errors
        document.addEventListener('livewire:error', (event) => {
            this.logError('Livewire Error', event.detail);
        });
    }
    
    setupNavigationTracking() {
        // Track SPA navigation start
        document.addEventListener('livewire:navigate', (event) => {
            this.startTime = performance.now();
            console.log('🔄 SPA Navigation started to:', event.detail.url);
        });
        
        // Track SPA navigation end
        document.addEventListener('livewire:navigated', (event) => {
            if (this.startTime) {
                const endTime = performance.now();
                const duration = endTime - this.startTime;
                
                this.navigationTimes.push({
                    url: event.detail.url || window.location.href,
                    duration: duration,
                    timestamp: new Date().toISOString()
                });
                
                console.log(`✅ SPA Navigation completed in ${duration.toFixed(2)}ms`);
                this.startTime = null;
            }
        });
    }
    
    setupPerformanceTracking() {
        // Track page load performance
        window.addEventListener('load', () => {
            setTimeout(() => {
                const perfData = performance.getEntriesByType('navigation')[0];
                if (perfData) {
                    console.log('📊 Page Load Performance:', {
                        'DNS Lookup': `${(perfData.domainLookupEnd - perfData.domainLookupStart).toFixed(2)}ms`,
                        'TCP Connection': `${(perfData.connectEnd - perfData.connectStart).toFixed(2)}ms`,
                        'Request': `${(perfData.responseStart - perfData.requestStart).toFixed(2)}ms`,
                        'Response': `${(perfData.responseEnd - perfData.responseStart).toFixed(2)}ms`,
                        'DOM Processing': `${(perfData.domComplete - perfData.domLoading).toFixed(2)}ms`,
                        'Total Load Time': `${(perfData.loadEventEnd - perfData.navigationStart).toFixed(2)}ms`
                    });
                }
            }, 1000);
        });
    }
    
    logError(type, details) {
        const error = {
            type: type,
            details: details,
            timestamp: new Date().toISOString(),
            url: window.location.href
        };
        
        this.errors.push(error);
        console.error(`❌ ${type}:`, details);
    }
    
    startMonitoring() {
        this.isMonitoring = true;
        this.navigationTimes = [];
        this.errors = [];
        console.log('🎯 Started SPA monitoring...');
    }
    
    stopMonitoring() {
        this.isMonitoring = false;
        console.log('⏹️ Stopped SPA monitoring');
        return this.getReport();
    }
    
    getReport() {
        const avgNavigationTime = this.navigationTimes.length > 0 
            ? this.navigationTimes.reduce((sum, nav) => sum + nav.duration, 0) / this.navigationTimes.length
            : 0;
            
        const report = {
            summary: {
                totalNavigations: this.navigationTimes.length,
                averageNavigationTime: `${avgNavigationTime.toFixed(2)}ms`,
                totalErrors: this.errors.length,
                testDuration: this.isMonitoring ? 'Still running' : 'Completed'
            },
            navigationTimes: this.navigationTimes,
            errors: this.errors,
            recommendations: this.getRecommendations()
        };
        
        console.log('📋 SPA Testing Report:', report);
        return report;
    }
    
    getRecommendations() {
        const recommendations = [];
        
        if (this.errors.length > 0) {
            recommendations.push('❗ Fix JavaScript errors found during testing');
        }
        
        const avgTime = this.navigationTimes.length > 0 
            ? this.navigationTimes.reduce((sum, nav) => sum + nav.duration, 0) / this.navigationTimes.length
            : 0;
            
        if (avgTime > 1000) {
            recommendations.push('⚠️ SPA navigation is slower than expected (>1000ms)');
        } else if (avgTime < 500) {
            recommendations.push('✅ SPA navigation performance is excellent (<500ms)');
        } else {
            recommendations.push('👍 SPA navigation performance is good (500-1000ms)');
        }
        
        if (this.navigationTimes.length === 0) {
            recommendations.push('ℹ️ No SPA navigations detected - ensure SPA mode is enabled');
        }
        
        return recommendations;
    }
    
    testExternalLinks() {
        console.log('🔗 Testing external links behavior...');
        
        // Create test external links
        const testLinks = [
            'https://docs.filamentphp.com',
            'https://github.com',
            'https://laravel.com'
        ];
        
        testLinks.forEach(url => {
            console.log(`Testing external link: ${url}`);
            // Note: Actual clicking would need to be done manually
            console.log(`Manual test required: Click link to ${url} and verify full page reload`);
        });
    }
    
    measurePageLoadTime() {
        const perfData = performance.getEntriesByType('navigation')[0];
        if (perfData) {
            return {
                totalLoadTime: perfData.loadEventEnd - perfData.navigationStart,
                domContentLoaded: perfData.domContentLoadedEventEnd - perfData.navigationStart,
                firstPaint: performance.getEntriesByType('paint').find(entry => entry.name === 'first-paint')?.startTime || 0,
                firstContentfulPaint: performance.getEntriesByType('paint').find(entry => entry.name === 'first-contentful-paint')?.startTime || 0
            };
        }
        return null;
    }
}

// Initialize the monitor
const spaMonitor = new SPATestingMonitor();

// Expose to global scope for manual testing
window.spaMonitor = spaMonitor;

// Helper functions for manual testing
window.startSPATest = () => spaMonitor.startMonitoring();
window.stopSPATest = () => spaMonitor.stopMonitoring();
window.getSPAReport = () => spaMonitor.getReport();
window.testExternalLinks = () => spaMonitor.testExternalLinks();
window.measurePageLoad = () => spaMonitor.measurePageLoadTime();

console.log(`
🧪 SPA Testing Commands Available:
- startSPATest() - Start monitoring
- stopSPATest() - Stop monitoring and get report
- getSPAReport() - Get current report
- testExternalLinks() - Test external links
- measurePageLoad() - Measure current page load metrics
`);