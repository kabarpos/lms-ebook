<x-filament-panels::page>
    <div class="space-y-6">
        <!-- SPA Testing Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">🧪 SPA Testing Instructions</h3>
            <div class="text-blue-800 space-y-2">
                <p><strong>1. Console Monitoring:</strong> Buka Developer Tools (F12) → Console tab</p>
                <p><strong>2. Load Testing Script:</strong> Jalankan script berikut di console:</p>
                <code class="block bg-blue-100 p-2 rounded text-sm mt-2">
                    const script = document.createElement('script');
                    script.src = '/js/spa-testing.js';
                    document.head.appendChild(script);
                </code>
                <p><strong>3. Start Monitoring:</strong> Ketik <code class="bg-blue-100 px-1 rounded">startSPATest()</code> di console</p>
                <p><strong>4. Test Navigation:</strong> Klik berbagai menu dan halaman</p>
                <p><strong>5. Get Report:</strong> Ketik <code class="bg-blue-100 px-1 rounded">stopSPATest()</code> di console</p>
            </div>
        </div>

        <!-- Navigation Test Links -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-green-900 mb-3">🔄 Navigation Test Links</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <a href="{{ route('filament.admin.resources.categories.index') }}" 
                   class="block p-3 bg-white border border-green-300 rounded hover:bg-green-50 transition-colors cursor-pointer">
                    <div class="font-medium text-green-900">Categories</div>
                    <div class="text-sm text-green-600">Test SPA navigation</div>
                </a>
                
                <a href="{{ route('filament.admin.resources.courses.index') }}" 
                   class="block p-3 bg-white border border-green-300 rounded hover:bg-green-50 transition-colors cursor-pointer">
                    <div class="font-medium text-green-900">Courses</div>
                    <div class="text-sm text-green-600">Test SPA navigation</div>
                </a>
                
                <a href="{{ route('filament.admin.resources.transactions.index') }}" 
                   class="block p-3 bg-white border border-green-300 rounded hover:bg-green-50 transition-colors cursor-pointer">
                    <div class="font-medium text-green-900">Transactions</div>
                    <div class="text-sm text-green-600">Test SPA navigation</div>
                </a>
                
                <a href="{{ route('filament.admin.resources.users.index') }}" 
                   class="block p-3 bg-white border border-green-300 rounded hover:bg-green-50 transition-colors cursor-pointer">
                    <div class="font-medium text-green-900">Users</div>
                    <div class="text-sm text-green-600">Test SPA navigation</div>
                </a>
                
                <a href="{{ route('filament.admin.pages.statistik') }}" 
                   class="block p-3 bg-white border border-green-300 rounded hover:bg-green-50 transition-colors cursor-pointer">
                    <div class="font-medium text-green-900">Statistik</div>
                    <div class="text-sm text-green-600">Test SPA navigation</div>
                </a>
                
                <a href="{{ route('filament.admin.pages.data') }}" 
                   class="block p-3 bg-white border border-green-300 rounded hover:bg-green-50 transition-colors cursor-pointer">
                    <div class="font-medium text-green-900">Data</div>
                    <div class="text-sm text-green-600">Test SPA navigation</div>
                </a>
            </div>
        </div>

        <!-- External Links Test -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-yellow-900 mb-3">🔗 External Links Test (spaUrlExceptions)</h3>
            <p class="text-yellow-800 mb-4">Links berikut harus membuka dengan <strong>full page reload</strong> karena ada di spaUrlExceptions:</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="https://docs.filamentphp.com" 
                   target="_blank"
                   class="block p-3 bg-white border border-yellow-300 rounded hover:bg-yellow-50 transition-colors cursor-pointer">
                    <div class="font-medium text-yellow-900">Filament Docs</div>
                    <div class="text-sm text-yellow-600">Should open in new tab</div>
                </a>
                
                <a href="https://github.com/filamentphp/filament" 
                   target="_blank"
                   class="block p-3 bg-white border border-yellow-300 rounded hover:bg-yellow-50 transition-colors cursor-pointer">
                    <div class="font-medium text-yellow-900">GitHub</div>
                    <div class="text-sm text-yellow-600">Should open in new tab</div>
                </a>
                
                <a href="https://laravel.com/docs" 
                   target="_blank"
                   class="block p-3 bg-white border border-yellow-300 rounded hover:bg-yellow-50 transition-colors cursor-pointer">
                    <div class="font-medium text-yellow-900">Laravel Docs</div>
                    <div class="text-sm text-yellow-600">Should open in new tab</div>
                </a>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-purple-900 mb-3">📊 Performance Metrics</h3>
            <div class="text-purple-800 space-y-2">
                <p><strong>Expected SPA Benefits:</strong></p>
                <ul class="list-disc list-inside space-y-1 ml-4">
                    <li>Navigation time: 200-500ms (vs 800-1500ms full reload)</li>
                    <li>Smooth transitions without white flash</li>
                    <li>Reduced server requests for assets</li>
                    <li>Loading indicators during navigation</li>
                    <li>Browser back/forward button support</li>
                </ul>
                
                <p class="mt-4"><strong>Console Commands:</strong></p>
                <div class="bg-purple-100 p-2 rounded text-sm space-y-1">
                    <div><code>measurePageLoad()</code> - Current page load metrics</div>
                    <div><code>getSPAReport()</code> - Get navigation report</div>
                    <div><code>testExternalLinks()</code> - Test external links behavior</div>
                </div>
            </div>
        </div>

        <!-- Testing Checklist -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">✅ Testing Checklist</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Navigation Tests:</h4>
                    <ul class="space-y-1 text-sm text-gray-700">
                        <li>□ All menu items load without full refresh</li>
                        <li>□ URL changes correctly</li>
                        <li>□ Loading indicators appear</li>
                        <li>□ No white flash between pages</li>
                        <li>□ Browser back/forward works</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Console Tests:</h4>
                    <ul class="space-y-1 text-sm text-gray-700">
                        <li>□ No JavaScript errors</li>
                        <li>□ No Livewire errors</li>
                        <li>□ No 404 errors for assets</li>
                        <li>□ No CORS errors</li>
                        <li>□ Minimal warnings only</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">External Links:</h4>
                    <ul class="space-y-1 text-sm text-gray-700">
                        <li>□ External links open in new tab</li>
                        <li>□ Download actions work properly</li>
                        <li>□ PDF generation not affected</li>
                        <li>□ spaUrlExceptions working</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Performance:</h4>
                    <ul class="space-y-1 text-sm text-gray-700">
                        <li>□ SPA navigation < 500ms</li>
                        <li>□ Reduced asset requests</li>
                        <li>□ Better perceived performance</li>
                        <li>□ Smooth user experience</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-load testing script
        document.addEventListener('DOMContentLoaded', function() {
            if (!window.spaMonitor) {
                const script = document.createElement('script');
                script.src = '/js/spa-testing.js';
                script.onload = function() {
                    console.log('🧪 SPA Testing script loaded automatically');
                    console.log('Use startSPATest() to begin monitoring');
                };
                document.head.appendChild(script);
            }
        });
    </script>
</x-filament-panels::page>