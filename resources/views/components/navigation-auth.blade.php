<nav id="nav-auth" class="bg-white border-b border-gray-100 shadow-sm" x-data="{ open: false, profileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('front.index') }}" class="flex-shrink-0">
                    <img src="{{ asset('assets/images/logos/logo.svg') }}" class="h-8 w-auto" alt="logo">
                </a>
            </div>

            <!-- Desktop Search -->
            <div class="hidden lg:flex flex-1 max-w-lg mx-8">
                <form method="GET" action="{{ route('dashboard.search.courses') }}" class="w-full">
                    <div class="relative">
                        <input type="text" name="search" 
                               class="w-full pl-4 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lochmara-500 focus:border-transparent transition-all duration-200" 
                               placeholder="Search courses...">
                        <button type="submit" 
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 p-2 text-lochmara-600 hover:text-lochmara-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Desktop Actions & Profile -->
            <div class="hidden lg:flex items-center space-x-4">
                <!-- Action Icons -->
                <a href="#" class="p-2 text-gray-500 hover:text-lochmara-600 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </a>
                <a href="#" class="p-2 text-gray-500 hover:text-lochmara-600 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2l1.5 3h5L16.5 4h2a2 2 0 012 2v2M4 6v10a2 2 0 002 2h12a2 2 0 002-2V6M4 6h16M10 11V9m4 2V9"/>
                    </svg>
                </a>
                <a href="#" class="p-2 text-gray-500 hover:text-lochmara-600 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h1l10.5-10.5a1.5 1.5 0 00-2.12-2.12L3 17v2z"/>
                    </svg>
                </a>

                <div class="h-6 w-px bg-gray-300"></div>

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ profileOpen: false }">
                    <button @click="profileOpen = !profileOpen" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-200 flex-shrink-0">
                            @if($user && $user->photo)
                                <img src="{{ Storage::url($user->photo) }}" class="w-full h-full object-cover" alt="profile">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-lochmara-500 to-lochmara-600">
                                    <span class="text-white font-semibold text-sm">{{ $user ? substr($user->name, 0, 2) : 'U' }}</span>
                                </div>
                            @endif
                        </div>
                        @if($user)
                        <div class="hidden lg:block text-left">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->occupation ?? 'Student' }}</p>
                        </div>
                        @endif
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="profileOpen" @click.away="profileOpen = false" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-lochmara-50 hover:text-lochmara-600 transition-colors duration-200">
                            My Courses
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-lochmara-50 hover:text-lochmara-600 transition-colors duration-200">
                            Certificates
                        </a>
                        <a href="{{ route('dashboard.subscriptions') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-lochmara-50 hover:text-lochmara-600 transition-colors duration-200">
                            Subscriptions
                        </a>
                        @if($user && ($user->hasRole('admin') || $user->hasRole('super-admin')))
                        <a href="/admin" target="_blank" class="block px-4 py-2 text-sm text-gray-700 hover:bg-lochmara-50 hover:text-lochmara-600 transition-colors duration-200">
                            Admin Panel
                        </a>
                        @endif
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-lochmara-50 hover:text-lochmara-600 transition-colors duration-200">
                            Settings
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-lochmara-50 hover:text-lochmara-600 transition-colors duration-200">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="lg:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-lochmara-500">
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
            <!-- Mobile Search -->
            <div class="px-3 py-2">
                <form method="GET" action="{{ route('dashboard.search.courses') }}">
                    <div class="relative">
                        <input type="text" name="search" 
                               class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lochmara-500 focus:border-transparent" 
                               placeholder="Search courses...">
                        <button type="submit" 
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 p-1 text-lochmara-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Mobile Profile Section -->
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="flex items-center px-3">
                    <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 flex-shrink-0">
                        @if($user && $user->photo)
                            <img src="{{ Storage::url($user->photo) }}" class="w-full h-full object-cover" alt="profile">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-lochmara-500 to-lochmara-600">
                                <span class="text-white font-semibold">{{ $user ? substr($user->name, 0, 2) : 'U' }}</span>
                            </div>
                        @endif
                    </div>
                    @if($user)
                    <div class="ml-3">
                        <div class="text-base font-medium text-gray-800">{{ $user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $user->occupation ?? 'Student' }}</div>
                    </div>
                    @endif
                </div>
                <div class="mt-3 space-y-1">
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-base text-gray-700 hover:bg-lochmara-50 hover:text-lochmara-600 transition-colors duration-200">
                        My Courses
                    </a>
                    <a href="#" class="block px-3 py-2 text-base text-gray-700 hover:bg-lochmara-50 hover:text-lochmara-600 transition-colors duration-200">
                        Certificates
                    </a>
                    <a href="{{ route('dashboard.subscriptions') }}" class="block px-3 py-2 text-base text-gray-700 hover:bg-lochmara-50 hover:text-lochmara-600 transition-colors duration-200">
                        Subscriptions
                    </a>
                    @if($user && ($user->hasRole('admin') || $user->hasRole('super-admin')))
                    <a href="/admin" target="_blank" class="block px-3 py-2 text-base text-gray-700 hover:bg-lochmara-50 hover:text-lochmara-600 transition-colors duration-200">
                        Admin Panel
                    </a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-base text-gray-700 hover:bg-lochmara-50 hover:text-lochmara-600 transition-colors duration-200">
                        Settings
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left block px-3 py-2 text-base text-gray-700 hover:bg-lochmara-50 hover:text-lochmara-600 transition-colors duration-200">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
