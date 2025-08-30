<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Debug - LMS DripCourse</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">🔧 Checkout Debug Information</h1>
                
                <!-- Login Status -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                    <h2 class="font-semibold text-blue-900 mb-2">Authentication Status</h2>
                    @auth
                        <div class="text-green-700">
                            ✅ <strong>Logged in as:</strong> {{ auth()->user()->name }} ({{ auth()->user()->email }})
                        </div>
                        <div class="text-gray-600 text-sm mt-1">
                            User ID: {{ auth()->id() }} | Roles: {{ auth()->user()->roles->pluck('name')->implode(', ') }}
                        </div>
                    @else
                        <div class="text-red-700">
                            ❌ <strong>Not logged in</strong>
                        </div>
                        <a href="{{ route('login') }}" class="inline-block mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Login to Test Checkout
                        </a>
                    @endauth
                </div>

                <!-- Test Links -->
                <div class="mb-6">
                    <h2 class="font-semibold text-gray-900 mb-3">🔗 Test Links</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="/course/react-native-mobile-app-development" 
                           class="block p-4 bg-gray-50 rounded border hover:bg-gray-100 transition-colors">
                            <div class="font-medium text-gray-900">Course Details Page</div>
                            <div class="text-sm text-gray-600">/course/react-native-mobile-app-development</div>
                        </a>
                        <a href="/course/react-native-mobile-app-development/checkout" 
                           class="block p-4 bg-lochmara-50 rounded border hover:bg-lochmara-100 transition-colors">
                            <div class="font-medium text-lochmara-900">Checkout Page (Test This)</div>
                            <div class="text-sm text-lochmara-600">/course/react-native-mobile-app-development/checkout</div>
                        </a>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <h2 class="font-semibold text-yellow-800 mb-2">📋 Testing Instructions</h2>
                    <ol class="list-decimal list-inside text-yellow-700 space-y-1 text-sm">
                        <li>Make sure you're logged in (see status above)</li>
                        <li>Click the "Checkout Page" link above</li>
                        <li>If it shows a blank page, check the browser's developer tools (F12)</li>
                        <li>Look for JavaScript errors or network errors</li>
                        <li>Check the Laravel logs for any errors</li>
                    </ol>
                </div>

                <!-- Expected Results -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                        <h3 class="font-semibold text-green-800 mb-2">✅ Expected (Success)</h3>
                        <ul class="text-green-700 text-sm space-y-1">
                            <li>• Shows checkout form with course details</li>
                            <li>• Displays course price and admin fee</li>
                            <li>• Has a "Pay Now" button</li>
                            <li>• Shows user information</li>
                        </ul>
                    </div>
                    <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                        <h3 class="font-semibold text-red-800 mb-2">❌ Problem (Blank Page)</h3>
                        <ul class="text-red-700 text-sm space-y-1">
                            <li>• White/blank page with no content</li>
                            <li>• Page loads but shows nothing</li>
                            <li>• Browser shows "loading" but never finishes</li>
                            <li>• Network tab shows 500 error or timeout</li>
                        </ul>
                    </div>
                </div>

                <!-- Debug Actions -->
                <div class="mt-6 p-4 bg-gray-100 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-3">🛠️ Debug Actions</h3>
                    <div class="flex flex-wrap gap-3">
                        <a href="/debug-checkout/react-native-mobile-app-development" 
                           class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                            Debug Route Test
                        </a>
                        <button onclick="checkLogs()" 
                                class="px-4 py-2 bg-purple-600 text-white rounded text-sm hover:bg-purple-700">
                            Check Browser Console
                        </button>

                    </div>
                </div>

                <!-- Time Info -->
                <div class="mt-6 text-center text-gray-500 text-sm">
                    Generated at: {{ now()->format('Y-m-d H:i:s') }} | Server: {{ request()->getHost() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function checkLogs() {
            console.log('=== CHECKOUT DEBUG INFO ===');
            console.log('Current URL:', window.location.href);
            console.log('User Agent:', navigator.userAgent);
            console.log('Local Storage:', localStorage);
            console.log('Session Storage:', sessionStorage);
            console.log('Cookies:', document.cookie);
            alert('Check the browser console (F12) for debug information');
        }

        // Auto-check console
        console.log('🔧 Checkout Debug Page Loaded');
        console.log('Time:', new Date().toISOString());
        console.log('Auth Status:', '{{ auth()->check() ? "Logged In" : "Not Logged In" }}');
        @auth
        console.log('User:', '{{ auth()->user()->name }}');
        @endauth
    </script>
</body>
</html>