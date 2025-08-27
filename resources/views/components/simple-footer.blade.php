<footer class="bg-white border-t border-gray-200 py-8 px-5">
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Logo and Description -->
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('assets/images/logos/logo.svg') }}" alt="Logo" class="h-8">
                    <h3 class="font-bold text-xl text-gray-900" style="font-family: 'Manrope', sans-serif;">LMS EBook</h3>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-4">
                    Platform pembelajaran online terbaik untuk mengembangkan skill dan karir Anda. Akses ribuan kursus berkualitas dari instruktur berpengalaman.
                </p>
                <div class="flex items-center gap-4">
                    <a href="#" class="text-gray-400 hover:text-lochmara-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-lochmara-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-lochmara-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.219-.359-1.219c0-1.142.662-1.995 1.488-1.995.219 0 .688.259.688.978 0 .615-.444 1.219-.662 1.816-.188.797.4 1.219 1.219 1.219 1.854 0 3.097-1.952 3.097-4.766 0-2.49-1.799-4.232-4.37-4.232-2.982 0-4.732 2.237-4.732 4.55 0 .904.347 1.875.779 2.404.085.103.097.194.072.299-.08.33-.256 1.037-.291 1.183-.047.188-.154.228-.355.138-1.279-.595-2.077-2.462-2.077-3.965 0-3.23 2.348-6.197 6.76-6.197 3.548 0 6.307 2.526 6.307 5.901 0 3.522-2.221 6.356-5.307 6.356-1.037 0-2.013-.542-2.343-1.188 0 0-.512 1.952-.637 2.43-.231.896-.854 2.013-1.271 2.696.957.296 1.974.456 3.019.456 6.624 0 11.99-5.367 11.99-11.987C24.007 5.367 18.641.001.017 0z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="font-semibold text-gray-900 mb-4" style="font-family: 'Manrope', sans-serif;">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('front.index') }}" class="text-gray-600 hover:text-lochmara-600 transition-colors">Home</a></li>
                    <li><a href="{{ route('front.pricing') }}" class="text-gray-600 hover:text-lochmara-600 transition-colors">Pricing</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-lochmara-600 transition-colors">Features</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-lochmara-600 transition-colors">About Us</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h4 class="font-semibold text-gray-900 mb-4" style="font-family: 'Manrope', sans-serif;">Support</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="text-gray-600 hover:text-lochmara-600 transition-colors">Help Center</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-lochmara-600 transition-colors">Contact Us</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-lochmara-600 transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-lochmara-600 transition-colors">Terms of Service</a></li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <div class="border-t border-gray-200 pt-6 mt-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-sm">
                    © {{ date('Y') }} LMS EBook. All rights reserved.
                </p>
                <p class="text-gray-500 text-sm">
                    Made with ❤️ for better education
                </p>
            </div>
        </div>
    </div>
</footer>