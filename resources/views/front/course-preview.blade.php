<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $course->name }} - {{ $sectionContent->name }} Preview</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Manrope', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif !important;
        }
    </style>
</head>
<body class="antialiased">

    <!-- Main Learning Interface -->
    <div class="min-h-screen bg-gray-50" x-data="{ 
        sidebarOpen: false,
        openSections: {
            @foreach($course->courseSections as $section)
            'section_{{ $section->id }}': {{ $section->sectionContents->where('is_free', true)->count() > 0 ? 'true' : 'false' }},
            @endforeach
        }
    }" style="font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif !important;">
        
        <!-- Fixed Sidebar -->
        <aside :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" 
               class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white w-80 lg:w-96 h-screen border-r border-gray-200 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
        
            <!-- Back to Course Details/Dashboard -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-lochmara-600 to-lochmara-700">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-white hover:text-lochmara-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Dashboard
                    </a>
                @else
                    <a href="{{ route('front.course.details', $course->slug) }}" class="inline-flex items-center text-white hover:text-lochmara-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Course Details
                    </a>
                @endauth
            </div>
        
            <!-- Mobile Close Button -->
            <div class="lg:hidden flex items-center justify-end px-4 py-3 border-b border-gray-100">
                <button @click="sidebarOpen = false" class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Course Info Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-lochmara-600 to-lochmara-700 text-white">
                <div class="mb-3">
                    <h2 class="text-lg font-bold line-clamp-1">{{ $course->name }}</h2>
                    <p class="text-lochmara-100 text-sm mt-1">Free Preview Mode</p>
                </div>
                
                <!-- Preview Notice -->
                <div class="bg-lochmara-800 rounded-lg p-3">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-300 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-white">Limited Preview</p>
                            <p class="text-xs text-lochmara-100 mt-1">This is a free preview lesson. Sign up to access the full course.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Course Sections Navigation -->
            <div class="flex-1 overflow-y-auto sidebar-scroll">
                <div class="px-6 py-4">
                    @foreach($course->courseSections as $sectionIndex => $section)
                        @php
                            $sectionId = 'section_' . $section->id;
                            $freeContentCount = $section->sectionContents->where('is_free', true)->count();
                        @endphp
                        <div class="mb-6 last:mb-0">
                            <!-- Section Header (Clickable) -->
                            <button type="button" 
                                    @click="openSections['{{ $sectionId }}'] = !openSections['{{ $sectionId }}']"
                                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors group cursor-pointer">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-lochmara-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-lochmara-700 font-semibold text-sm">{{ $sectionIndex + 1 }}</span>
                                    </div>
                                    <div class="text-left">
                                        <h3 class="font-semibold text-gray-900 text-sm group-hover:text-lochmara-700 transition-colors">{{ $section->name }}</h3>
                                        <div class="flex items-center space-x-2 text-xs text-gray-500 mt-0.5">
                                            <span>{{ $section->sectionContents->count() }} lessons</span>
                                            @if($freeContentCount > 0)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    {{ $freeContentCount }} Free
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Dropdown Arrow -->
                                <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" 
                                     :class="openSections['{{ $sectionId }}'] ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <!-- Section Contents (Collapsible) -->
                            <div x-show="openSections['{{ $sectionId }}']" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="mt-3 ml-11 space-y-2">
                                @foreach($section->sectionContents as $contentIndex => $content)
                                    @php
                                        $isActive = $currentSection && $section->id == $currentSection->id && $sectionContent->id == $content->id;
                                        $lessonNumber = $contentIndex + 1;
                                    @endphp
                                    @if($content->is_free)
                                        <!-- Free Content - Clickable -->
                                        <a href="{{ route('front.course.preview', ['course' => $course->slug, 'sectionContent' => $content->id]) }}" 
                                           @click="sidebarOpen = false" 
                                           class="group block cursor-pointer">
                                            <div class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 {{ $isActive ? 'bg-lochmara-50 border border-lochmara-200' : 'hover:bg-gray-50 border border-transparent hover:border-gray-200' }}">
                                                <!-- Lesson Status Icon -->
                                                <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 
                                                    @if($isActive) 
                                                        bg-lochmara-600 text-white
                                                    @else 
                                                        bg-green-100 text-green-600 group-hover:bg-lochmara-100 group-hover:text-lochmara-600
                                                    @endif">
                                                    @if($isActive)
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M8 5v14l11-7z"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                                
                                                <!-- Lesson Info -->
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="font-medium text-sm {{ $isActive ? 'text-lochmara-900' : 'text-gray-900 group-hover:text-lochmara-700' }} line-clamp-2 leading-tight">
                                                        {{ $lessonNumber }}. {{ $content->name }}
                                                    </h4>
                                                    <div class="flex items-center text-xs {{ $isActive ? 'text-lochmara-600' : 'text-gray-500' }} mt-1">
                                                        <span>Free Preview</span>
                                                        <span class="mx-1">•</span>
                                                        <span>8-12 min</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <!-- Locked Content -->
                                        <div class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50 opacity-60">
                                            <!-- Lesson Icon -->
                                            <div class="w-6 h-6 bg-gray-200 text-gray-400 rounded-full flex items-center justify-center flex-shrink-0">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            
                                            <!-- Lesson Info -->
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-sm text-gray-500 line-clamp-2 leading-tight">
                                                    {{ $lessonNumber }}. {{ $content->name }}
                                                </h4>
                                                <div class="flex items-center text-xs text-gray-400 mt-1">
                                                    <span>Premium Only</span>
                                                    <span class="mx-1">•</span>
                                                    <span>8-12 min</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Lock Icon -->
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Premium
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- CTA Section -->
                <div class="p-6 bg-gradient-to-r from-lochmara-600 to-lochmara-700 text-white">
                    <div class="text-center">
                        <h3 class="text-lg font-bold mb-2">Ready to Continue?</h3>
                        <p class="text-sm text-lochmara-100 mb-4">Get full access to all lessons, quizzes, and certificates.</p>
                        <div class="space-y-3">
                            @auth
                                <a href="{{ route('dashboard.course.join', $course->slug) }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-white text-lochmara-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Start Learning
                                </a>
                            @else
                                <a href="{{ route('register') }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-white text-lochmara-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Sign Up Now
                                </a>
                                <a href="{{ route('login') }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-2 border border-white text-white font-medium rounded-lg hover:bg-white hover:text-lochmara-700 transition-colors cursor-pointer">
                                    Already have an account? Login
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <div class="lg:ml-96 min-h-screen flex flex-col">
            <!-- Mobile Header -->
            <div class="lg:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between sticky top-0 z-30">
                <button @click="sidebarOpen = true" class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-900 truncate">{{ $sectionContent->name }}</h1>
                <div class="w-10"></div> <!-- Spacer for center alignment -->
            </div>
            
            <!-- Desktop Header -->
            <div class="hidden lg:block bg-white border-b border-gray-200 px-6 lg:px-8 py-4 sticky top-0 z-30">
                <!-- Breadcrumb Navigation -->
                <nav class="flex items-center space-x-2 text-sm mb-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-lochmara-600 transition-colors cursor-pointer">Dashboard</a>
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-lochmara-600 transition-colors truncate max-w-xs cursor-pointer">{{ $course->name }}</a>
                    @else
                        <a href="{{ route('front.course.details', $course->slug) }}" class="text-gray-500 hover:text-lochmara-600 transition-colors cursor-pointer">Course Details</a>
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <a href="{{ route('front.course.details', $course->slug) }}" class="text-gray-500 hover:text-lochmara-600 transition-colors truncate max-w-xs cursor-pointer">{{ $course->name }}</a>
                    @endauth
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-gray-900 font-medium truncate max-w-xs">{{ $sectionContent->name }}</span>
                </nav>
                
                <!-- Lesson Title and Info -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $sectionContent->name }}</h1>
                        <div class="flex items-center text-sm text-gray-600 mt-1">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Free Preview
                            </span>
                            <span>{{ $currentSection->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lesson Content -->
            <div class="flex-1 bg-white">
                <article class="max-w-4xl mx-auto">
                    <div class="px-6 sm:px-8 lg:px-10 py-8 lg:py-12">
                        <!-- Free Preview Notice -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-green-600 mt-1 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-green-900 mb-2">🎉 Free Preview Lesson</h3>
                                    <p class="text-green-800 text-sm leading-relaxed mb-4">
                                        You're viewing a free preview of this lesson. This gives you a taste of the high-quality content available in the full course.
                                    </p>
                                    <div class="flex flex-col sm:flex-row gap-3">
                                        @auth
                                            <a href="{{ route('dashboard.course.join', $course->slug) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors cursor-pointer">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                </svg>
                                                Join Full Course
                                            </a>
                                        @else
                                            <a href="{{ route('register') }}" 
                                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors cursor-pointer">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                Create Free Account
                                            </a>
                                        @endauth
                                        <a href="{{ route('front.pricing') }}" 
                                           class="inline-flex items-center px-4 py-2 border border-green-600 text-green-600 font-medium rounded-lg hover:bg-green-50 transition-colors cursor-pointer">
                                            View Pricing Plans
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Lesson Content -->
                        <div class="filament-rich-content prose prose-lg max-w-none content-typography">
                            {!! \Filament\Forms\Components\RichEditor\RichContentRenderer::make($sectionContent->content)->toHtml() !!}
                        </div>
                    </div>
                </article>
            </div>
            
            <!-- CTA Footer -->
            <div class="bg-gradient-to-r from-lochmara-600 to-lochmara-700 text-white p-6">
                <div class="max-w-4xl mx-auto text-center">
                    <h2 class="text-xl font-bold mb-2">Enjoyed this preview?</h2>
                    <p class="text-lochmara-100 mb-6">Get unlimited access to all {{ $course->courseSections->sum(fn($section) => $section->sectionContents->count()) }} lessons, downloadable resources, and certificates.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center max-w-md mx-auto">
                        @auth
                            <a href="{{ route('dashboard.course.join', $course->slug) }}" 
                               class="inline-flex items-center justify-center px-6 py-3 bg-white text-lochmara-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Access Full Course
                            </a>
                        @else
                            <a href="{{ route('register') }}" 
                               class="inline-flex items-center justify-center px-6 py-3 bg-white text-lochmara-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Sign Up Free
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" 
             class="mobile-overlay fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>
    </div>
    
    <style>
    /* Force Manrope Font Implementation */
    body, html, * {
        font-family: "Manrope", ui-sans-serif, system-ui, sans-serif !important;
    }
    
    /* Sidebar Scrollbar Styling */
    .sidebar-scroll {
        scrollbar-width: thin;
        scrollbar-color: #e5e7eb #f9fafb;
    }
    
    .sidebar-scroll::-webkit-scrollbar {
        width: 6px;
    }
    
    .sidebar-scroll::-webkit-scrollbar-track {
        background: #f9fafb;
    }
    
    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: #e5e7eb;
        border-radius: 3px;
    }
    
    .sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background: #d1d5db;
    }
    
    
    /* Filament Rich Content Specific Styling */
    .filament-rich-content {
        font-family: "Manrope", ui-sans-serif, system-ui, sans-serif !important;
    }
    
    /* Enhanced blockquote styling for TipTap output */
    .filament-rich-content blockquote {
        border-left: 4px solid #0f4c7a;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        padding: 1.5rem;
        margin: 2rem 0;
        border-radius: 0.75rem;
        font-style: italic;
        position: relative;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        color: #374151;
        font-size: 1.125rem;
        line-height: 1.75;
    }
    
    .filament-rich-content blockquote::before {
        content: '"';
        position: absolute;
        top: -0.5rem;
        left: 1rem;
        font-size: 4rem;
        color: #0f4c7a;
        opacity: 0.3;
        font-family: Georgia, serif;
    }
    
    .filament-rich-content blockquote p {
        margin: 0;
        padding: 0;
    }
    
    /* Enhanced paragraph styling */
    .filament-rich-content p {
        margin-bottom: 1.25rem;
        color: #374151;
        line-height: 1.75;
        text-align: justify;
    }
    
    /* Enhanced list styling - Override global reset */
    .filament-rich-content ul, 
    .filament-rich-content ol {
        margin: 1.5rem 0 !important;
        padding-left: 2rem !important;
        list-style: revert !important; /* Force list styles to show */
    }
    
    .filament-rich-content ul {
        list-style-type: disc !important;
    }
    
    .filament-rich-content ol {
        list-style-type: decimal !important;
    }
    
    .filament-rich-content li {
        margin-bottom: 0.75rem !important;
        line-height: 1.75 !important;
        display: list-item !important; /* Ensure list item display */
        list-style: inherit !important; /* Inherit parent list style */
    }
    
    .filament-rich-content li::marker {
        color: #0f4c7a !important;
        font-weight: 600 !important;
    }
    
    /* Nested lists with stronger specificity */
    .filament-rich-content ul ul {
        list-style-type: circle !important;
        margin: 0.5rem 0 !important;
    }
    
    .filament-rich-content ul ul ul {
        list-style-type: square !important;
    }
    
    .filament-rich-content ol ol {
        list-style-type: lower-alpha !important;
        margin: 0.5rem 0 !important;
    }
    
    .filament-rich-content ol ol ol {
        list-style-type: lower-roman !important;
    }
    
    /* Task list styling */
    .filament-rich-content ul[data-type="taskList"] {
        list-style: none !important;
        padding-left: 0 !important;
    }
    
    .filament-rich-content ul[data-type="taskList"] li {
        display: flex !important;
        align-items: flex-start !important;
        gap: 0.5rem !important;
    }
    
    .filament-rich-content ul[data-type="taskList"] li input[type="checkbox"] {
        margin-top: 0.125rem !important;
        flex-shrink: 0 !important;
    }
    
    /* Enhanced heading styling */
    .filament-rich-content h1,
    .filament-rich-content h2,
    .filament-rich-content h3 {
        font-family: "Manrope", ui-sans-serif, system-ui, sans-serif !important;
        font-weight: 700;
        color: #1f2937;
        letter-spacing: -0.025em;
    }
    
    .filament-rich-content h1 {
        font-size: 2.25rem;
        line-height: 1.2;
        margin: 2rem 0 1rem;
        background: linear-gradient(135deg, #0f4c7a 0%, #1d4ed8 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .filament-rich-content h2 {
        font-size: 1.875rem;
        line-height: 1.3;
        margin: 1.75rem 0 1rem;
        color: #0f4c7a;
    }
    
    .filament-rich-content h3 {
        font-size: 1.5rem;
        line-height: 1.4;
        margin: 1.5rem 0 0.75rem;
        color: #1e40af;
    }
    
    /* Enhanced link styling */
    .filament-rich-content a {
        color: #0f4c7a;
        text-decoration: none;
        font-weight: 500;
        border-bottom: 1px solid transparent;
        transition: all 0.2s ease;
    }
    
    .filament-rich-content a:hover {
        color: #0c3d61;
        border-bottom-color: #0f4c7a;
    }
    
    /* Enhanced code styling */
    .filament-rich-content code {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        color: #dc2626;
        font-weight: 500;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
    
    .filament-rich-content pre {
        background: #1e293b;
        color: #f1f5f9;
        padding: 1.5rem;
        border-radius: 0.75rem;
        overflow-x: auto;
        margin: 2rem 0;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border: 1px solid #334155;
    }
    
    .filament-rich-content pre code {
        background: transparent;
        border: none;
        color: inherit;
        padding: 0;
    }
    
    /* Content Typography */
    .content-typography {
        line-height: 1.75;
    }
    
    .content-typography h1,
    .content-typography h2,
    .content-typography h3 {
        font-family: "Manrope", ui-sans-serif, system-ui, sans-serif !important;
        font-weight: 700;
        color: #1f2937;
    }
    
    .content-typography p {
        margin-bottom: 1.25rem;
        color: #374151;
    }
    
    .content-typography ul,
    .content-typography ol {
        margin: 1.25rem 0;
        padding-left: 1.5rem;
    }
    
    .content-typography code {
        background-color: #f3f4f6;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }
    
    .content-typography pre {
        background-color: #1f2937;
        color: #f9fafb;
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin: 1.5rem 0;
    }
    </style>
</body>
</html>