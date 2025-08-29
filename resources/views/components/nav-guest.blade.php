<nav id="nav-guest" class="bg-white border-b border-gray-100 shadow-sm" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('front.index') }}" class="flex-shrink-0 cursor-pointer">
                    <img src="{{ asset('assets/images/logos/logo.png') }}" class="h-10 w-auto" alt="logo" />
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-8">
                <a href="{{ route('front.index') }}" class="{{ request()->routeIs('front.index') ? 'text-lochmara-600 font-semibold' : 'text-gray-700 hover:text-lochmara-600' }} transition-colors duration-200 cursor-pointer">
                    Home
                </a>
                <a href="{{ route('front.courses') }}" class="{{ request()->routeIs('front.courses') ? 'text-lochmara-600 font-semibold' : 'text-gray-700 hover:text-lochmara-600' }} transition-colors duration-200 cursor-pointer">
                    Courses
                </a>
                <a href="{{ route('front.terms-of-service') }}" class="{{ request()->routeIs('front.terms-of-service') ? 'text-lochmara-600 font-semibold' : 'text-gray-700 hover:text-lochmara-600' }} transition-colors duration-200 cursor-pointer">
                    Peraturan
                </a>

            </div>

            <!-- Desktop Action Buttons -->
            <div class="hidden lg:flex items-center space-x-4">
 
                <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-white bg-lochmara-600 rounded-lg hover:bg-lochmara-700 transition-colors duration-200 cursor-pointer">
                    Login
                </a>
            </div>

            <!-- Mobile menu button -->
            <div class="lg:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-lochmara-500 cursor-pointer">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden lg:hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-100">
            <a href="{{ route('front.index') }}" class="{{ request()->routeIs('front.index') ? 'bg-lochmara-50 text-lochmara-600 font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-lochmara-600' }} block px-3 py-2 rounded-md text-base transition-colors duration-200 cursor-pointer">
                Home
            </a>
            <a href="{{ route('front.courses') }}" class="{{ request()->routeIs('front.courses') ? 'bg-lochmara-50 text-lochmara-600 font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-lochmara-600' }} block px-3 py-2 rounded-md text-base transition-colors duration-200 cursor-pointer">
                Courses
            </a>
            <a href="{{ route('front.terms-of-service') }}" class="{{ request()->routeIs('front.terms-of-service') ? 'bg-lochmara-50 text-lochmara-600 font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-lochmara-600' }} block px-3 py-2 rounded-md text-base transition-colors duration-200 cursor-pointer">
                Peraturan
            </a>

            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="px-3 space-y-3">
                    <a href="{{ route('register') }}" class="block text-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-lochmara-600 transition-colors duration-200 cursor-pointer">
                        Belum punya akun? Daftar di sini
                    </a>
                    <a href="{{ route('login') }}" class="block text-center px-4 py-2 text-sm font-medium text-white bg-lochmara-600 rounded-lg hover:bg-lochmara-700 transition-colors duration-200 cursor-pointer">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
