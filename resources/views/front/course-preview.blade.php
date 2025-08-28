<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $sectionContent->name }} - Learn {{ $course->name }} with comprehensive lessons and practical examples.">
    <meta name="keywords" content="{{ $course->name }}, online learning, course, {{ $sectionContent->name }}">
    <meta name="author" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ $sectionContent->name }} - {{ $course->name }}">
    <meta property="og:description" content="Learn {{ $course->name }} with comprehensive lessons and practical examples.">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $sectionContent->name }} - {{ $course->name }}">
    <meta name="twitter:description" content="Learn {{ $course->name }} with comprehensive lessons and practical examples.">
    <title>{{ $course->name }} - {{ $sectionContent->name }} @if(!$sectionContent->is_free && !auth()->check()) - Premium Locked @elseif(!$sectionContent->is_free && $isAdmin) - Admin Access @elseif(!$sectionContent->is_free) - Learning @else - Preview @endif</title>
    
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
    <div class="min-h-screen bg-gray-50" x-data="courseData()" x-init="initializeCourse()" style="font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif !important;">
        
        <!-- Fixed Sidebar -->
        <aside :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" 
               class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white w-80 lg:w-96 h-screen border-r border-gray-200 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
        
            <!-- Back to Course Details/Dashboard -->
            <div class="px-6 py-4 bg-gradient-to-r from-lochmara-600 to-lochmara-700">
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
                                    @click="toggleSection('{{ $sectionId }}')"
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
                                        
                                        // Determine if user can access this content
                                        $canAccess = $content->is_free || auth()->check();
                                        
                                        // Determine route - UNIFIED ROUTING: everyone uses preview route
                                        $routeName = 'front.course.preview';
                                        $routeParams = ['course' => $course->slug, 'sectionContent' => $content->id];
                                        
                                        // Check if lesson is completed (for authenticated users)
                                        $isCompleted = false;
                                        if (auth()->check() && isset($completedLessons) && is_array($completedLessons)) {
                                            $isCompleted = in_array($content->id, $completedLessons);
                                        }
                                    @endphp
                                    
                                    @if($routeName)
                                        <!-- All Content - Unified Route -->
                                        <a href="{{ route($routeName, $routeParams) }}" 
                                           @click="sidebarOpen = false" 
                                           class="group block cursor-pointer">
                                            <div class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 {{ $isActive ? 'bg-lochmara-50 border border-lochmara-200' : 'hover:bg-gray-50 border border-transparent hover:border-gray-200' }}">
                                                <!-- Lesson Status Icon -->
                                                <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0">
                                                    @if($isActive)
                                                        <div class="w-6 h-6 bg-lochmara-600 text-white rounded-full flex items-center justify-center">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M8 5v14l11-7z"/>
                                                            </svg>
                                                        </div>
                                                    @elseif($isCompleted)
                                                        <div class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                        </div>
                                                    @elseif($content->is_free)
                                                        <div class="w-6 h-6 bg-green-100 text-green-600 group-hover:bg-lochmara-100 group-hover:text-lochmara-600 rounded-full flex items-center justify-center">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 bg-lochmara-100 text-lochmara-600 group-hover:bg-lochmara-200 rounded-full flex items-center justify-center">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M8 5v14l11-7z"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Lesson Info -->
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="font-medium text-sm {{ $isActive ? 'text-lochmara-900' : 'text-gray-900 group-hover:text-lochmara-700' }} line-clamp-2 leading-tight">
                                                        {{ $lessonNumber }}. {{ $content->name }}
                                                    </h4>
                                                    <div class="flex items-center text-xs text-gray-500 mt-1">
                                                        @if($content->is_free)
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                </svg>
                                                                Free
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-lochmara-100 text-lochmara-800">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 616 0z" clip-rule="evenodd"/>
                                                                </svg>
                                                                Premium
                                                            </span>
                                                        @endif
                                                        @if($isCompleted)
                                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                ✓ Completed
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <!-- Active Indicator -->
                                                @if($isActive)
                                                <div class="w-2 h-8 bg-lochmara-600 rounded-full flex-shrink-0"></div>
                                                @endif
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- CTA Section -->
                @if(!auth()->check())
                <div class="p-6 bg-gradient-to-r from-lochmara-600 to-lochmara-700 text-white">
                    <div class="text-center">
                        <h3 class="text-lg font-bold mb-2">Ready to Continue?</h3>
                        <p class="text-sm text-lochmara-100 mb-4">Get full access to all lessons, quizzes, and certificates.</p>
                        <div class="space-y-3">
                            <a href="{{ route('dashboard.course.join', $course->slug) }}" 
                               class="w-full inline-flex items-center justify-center px-4 py-3 bg-white text-lochmara-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Start Learning
                            </a>
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
                        </div>
                    </div>
                </div>
                @endif
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
                            @if(!$sectionContent->is_free && !auth()->check())
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-2">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 616 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Premium Locked
                                </span>
                            @elseif(!$sectionContent->is_free && $isAdmin)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mr-2">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2L3 7v3c0 5.25 3.99 7.68 7 8 3.01-.32 7-2.75 7-8V7l-7-5z" clip-rule="evenodd"/>
                                    </svg>
                                    👑 Admin Access
                                </span>
                            @elseif(!$sectionContent->is_free && auth()->check())
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-lochmara-100 text-lochmara-800 mr-2">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 616 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Premium Learning
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Free Preview
                                </span>
                            @endif
                            <span>{{ $currentSection->name }}</span>
                        </div>
                    </div>
                    
                    @if(!$sectionContent->is_free && auth()->check() && isset($currentProgress))
                    <!-- Progress Info -->                        
                    <div class="hidden sm:flex items-center space-x-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-lochmara-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span x-text="`Lesson ${completedLessons} of ${totalLessons}`"></span>
                    </div>
                    @endif
                </div>
                
                @if(!$sectionContent->is_free && auth()->check() && isset($currentProgress))
                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                    <div class="bg-gradient-to-r from-lochmara-600 to-lochmara-500 h-2 rounded-full transition-all duration-500" 
                         :style="`width: ${currentProgress}%`"></div>
                </div>
                @endif
            </div>
            
            <!-- Lesson Content -->
            <div class="flex-1 bg-white">
                <article class="max-w-4xl mx-auto">
                    <div class="px-6 sm:px-8 lg:px-10 py-8 lg:py-12">
                        @if(!$sectionContent->is_free && !auth()->check())
                            <!-- Premium Locked Content -->
                            <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-red-600 mt-1 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 616 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-red-900 mb-2">🔒 Premium Content Locked</h3>
                                        <p class="text-red-800 text-sm leading-relaxed mb-4">
                                            This lesson contains premium content that requires authentication. Please login or create an account to access this material.
                                        </p>
                                        <div class="flex flex-col sm:flex-row gap-3">
                                            <a href="{{ route('login') }}" 
                                               class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors cursor-pointer">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                                </svg>
                                                Login to Access
                                            </a>
                                            <a href="{{ route('register') }}" 
                                               class="inline-flex items-center px-4 py-2 bg-lochmara-600 text-white font-semibold rounded-lg hover:bg-lochmara-700 transition-colors cursor-pointer">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                Create Account
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Blurred Content Preview - NO YOUTUBE FOR PUBLIC -->
                            <div class="relative">
                                <div class="filament-rich-content prose prose-lg max-w-none content-typography blur-sm opacity-30 pointer-events-none">
                                    {!! \Filament\Forms\Components\RichEditor\RichContentRenderer::make($sectionContent->content ?? '')->toHtml() !!}
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-white to-white pointer-events-none"></div>
                            </div>
                        @elseif(!$sectionContent->is_free)
                            <!-- Premium Content for Authenticated Users -->
                            <!-- YouTube Player (if available) - ONLY FOR AUTHENTICATED USERS -->
                            @if($sectionContent->youtube_url && $sectionContent->getYoutubeVideoId())
                                <x-youtube-player 
                                    :videoId="$sectionContent->getYoutubeVideoId()" 
                                    :title="$sectionContent->name" 
                                />
                            @endif
                            
                            <!-- Premium Content -->
                            <div class="filament-rich-content prose prose-lg max-w-none content-typography tiptap-content">
                                {!! \Filament\Forms\Components\RichEditor\RichContentRenderer::make($sectionContent->content ?? '')->toHtml() !!}
                            </div>
                            
                            <!-- Course Navigation Buttons - Bottom -->
                            <div class="flex flex-col sm:flex-row gap-3 mt-8 pt-6 border-t border-gray-200">
                                <button 
                                    @click="markLessonComplete()" 
                                    :disabled="isLessonCompleted || isLoading"
                                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                                    :class="isLessonCompleted ? 
                                        'bg-green-100 text-green-800 border border-green-300 cursor-not-allowed' : 
                                        isLoading ? 'bg-gray-100 text-gray-500 border border-gray-300 cursor-not-allowed' :
                                        'border border-green-300 text-green-700 bg-green-50 hover:bg-green-100 hover:border-green-400'">
                                    <div x-show="isLoading" class="w-4 h-4 mr-2 animate-spin rounded-full border-2 border-gray-300 border-t-green-600"></div>
                                    <svg x-show="!isLoading" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span x-text="isLoading ? 'Saving...' : (isLessonCompleted ? 'Completed ✅' : 'Mark as Complete')"></span>
                                </button>
                                
                                @php
                                    // Find next content manually if not provided
                                    $nextContentToShow = null;
                                    if (isset($nextContent)) {
                                        $nextContentToShow = $nextContent;
                                    } else {
                                        // Find next content in current section
                                        $currentSectionContents = $currentSection->sectionContents()->orderBy('id')->get();
                                        $currentIndex = $currentSectionContents->search(function($item) use ($sectionContent) {
                                            return $item->id === $sectionContent->id;
                                        });
                                        
                                        if ($currentIndex !== false && $currentIndex + 1 < $currentSectionContents->count()) {
                                            $nextContentToShow = $currentSectionContents[$currentIndex + 1];
                                        } else {
                                            // Find first content in next section
                                            $nextSection = $course->courseSections()->where('id', '>', $currentSection->id)->orderBy('id')->first();
                                            if ($nextSection) {
                                                $nextContentToShow = $nextSection->sectionContents()->orderBy('id')->first();
                                            }
                                        }
                                    }
                                @endphp
                                
                                @if($nextContentToShow)
                                <a href="{{ route('front.course.preview', ['course' => $course->slug, 'sectionContent' => $nextContentToShow->id]) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-lochmara-600 text-white font-semibold rounded-lg hover:bg-lochmara-700 transition-colors cursor-pointer">
                                    <span>Continue Learning</span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>
                                @else
                                <button class="inline-flex items-center px-4 py-2 bg-gray-400 text-white font-semibold rounded-lg cursor-not-allowed" disabled>
                                    <span>Course Complete</span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        @else
                            <!-- YouTube Player (if available) -->
                            @if($sectionContent->youtube_url && $sectionContent->getYoutubeVideoId())
                                <x-youtube-player 
                                    :videoId="$sectionContent->getYoutubeVideoId()" 
                                    :title="$sectionContent->name" 
                                />
                            @endif
                            
                            <!-- Free Content -->
                            <div class="filament-rich-content prose prose-lg max-w-none content-typography">
                                {!! \Filament\Forms\Components\RichEditor\RichContentRenderer::make($sectionContent->content ?? '')->toHtml() !!}
                            </div>
                            
                            <!-- Course Navigation Buttons - Bottom -->
                            <div class="flex flex-col sm:flex-row gap-3 mt-8 pt-6 border-t border-gray-200">
                                <button 
                                    @click="markLessonComplete()" 
                                    :disabled="isLessonCompleted || isLoading"
                                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                                    :class="isLessonCompleted ? 
                                        'bg-green-100 text-green-800 border border-green-300 cursor-not-allowed' : 
                                        isLoading ? 'bg-gray-100 text-gray-500 border border-gray-300 cursor-not-allowed' :
                                        'border border-green-300 text-green-700 bg-green-50 hover:bg-green-100 hover:border-green-400'">
                                    <div x-show="isLoading" class="w-4 h-4 mr-2 animate-spin rounded-full border-2 border-gray-300 border-t-green-600"></div>
                                    <svg x-show="!isLoading" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span x-text="isLoading ? 'Saving...' : (isLessonCompleted ? 'Completed ✅' : 'Mark as Complete')"></span>
                                </button>
                                
                                @php
                                    // Find next content manually for free content
                                    $nextContentToShow = null;
                                    if (isset($nextContent)) {
                                        $nextContentToShow = $nextContent;
                                    } else {
                                        // Find next content in current section
                                        $currentSectionContents = $currentSection->sectionContents()->orderBy('id')->get();
                                        $currentIndex = $currentSectionContents->search(function($item) use ($sectionContent) {
                                            return $item->id === $sectionContent->id;
                                        });
                                        
                                        if ($currentIndex !== false && $currentIndex + 1 < $currentSectionContents->count()) {
                                            $nextContentToShow = $currentSectionContents[$currentIndex + 1];
                                        } else {
                                            // Find first content in next section
                                            $nextSection = $course->courseSections()->where('id', '>', $currentSection->id)->orderBy('id')->first();
                                            if ($nextSection) {
                                                $nextContentToShow = $nextSection->sectionContents()->orderBy('id')->first();
                                            }
                                        }
                                    }
                                @endphp
                                
                                @if($nextContentToShow)
                                <a href="{{ route('front.course.preview', ['course' => $course->slug, 'sectionContent' => $nextContentToShow->id]) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-lochmara-600 text-white font-semibold rounded-lg hover:bg-lochmara-700 transition-colors cursor-pointer">
                                    <span>Continue Learning</span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>
                                @else
                                <button class="inline-flex items-center px-4 py-2 bg-gray-400 text-white font-semibold rounded-lg cursor-not-allowed" disabled>
                                    <span>Course Complete</span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </article>
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
        line-height: 1.4 !important; /* Reduced from 1.75 to 1.4 for tighter spacing */
        display: list-item !important; /* Ensure list item display */
        list-style: inherit !important; /* Inherit parent list style */
        text-align: justify; /* Match paragraph justification */
        color: #374151; /* Match paragraph color */
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
    
    /* Ensure nested list items also have proper line-height */
    .filament-rich-content ul ul li,
    .filament-rich-content ol ol li,
    .filament-rich-content ul ol li,
    .filament-rich-content ol ul li {
        line-height: 1.4 !important;
        margin-bottom: 0.5rem !important;
        text-align: justify;
        color: #374151;
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
        line-height: 1.2rem;
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
    
    /* Enhanced pre code block styling with copy functionality */
    .filament-rich-content pre {
        position: relative;
        background: #1e293b;
        color: #f1f5f9;
        padding: 1.5rem;
        padding-top: 2.5rem; /* Reduced space for smaller copy button */
        border-radius: 0.75rem;
        overflow: hidden; /* Changed from overflow-x: auto to prevent horizontal scroll */
        margin: 2rem 0;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border: 1px solid #334155;
    }
    
    .filament-rich-content pre code {
        background: transparent;
        border: none;
        color: inherit;
        padding: 0;
        display: block;
        white-space: pre-wrap; /* Enable text wrapping */
        word-wrap: break-word; /* Break long words */
        overflow-wrap: break-word; /* Modern alternative */
        line-height: 1.6;
    }
    
    /* Copy button styling */
    .code-copy-btn {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.3);
        color: #93c5fd;
        padding: 0.375rem; /* Equal padding on all sides for perfect symmetry */
        border-radius: 0.25rem;
        font-size: 0.625rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.25rem; /* Consistent gap between icon and text for all states */
        font-family: ui-sans-serif, system-ui, sans-serif;
        z-index: 10;
        line-height: 1;
        min-height: 1.5rem; /* Ensures consistent button height */
        white-space: nowrap; /* Prevents text wrapping */
        justify-content: center; /* Center content horizontally */
    }
    
    .code-copy-btn:hover {
        background: rgba(59, 130, 246, 0.2);
        border-color: rgba(59, 130, 246, 0.5);
        color: #dbeafe;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.2);
    }
    
    .code-copy-btn.copied {
        background: rgba(34, 197, 94, 0.2);
        border-color: rgba(34, 197, 94, 0.5);
        color: #86efac;
    }
    
    .code-copy-btn svg {
        width: 0.75rem;
        height: 0.75rem;
        flex-shrink: 0;
        display: block; /* Ensures consistent icon rendering */
    }
    
    /* Language label styling */
    .code-lang-label {
        position: absolute;
        top: 0.75rem;
        left: 0.75rem;
        background: rgba(148, 163, 184, 0.1);
        border: 1px solid rgba(148, 163, 184, 0.3);
        color: #cbd5e1;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.625rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
    

    </style>
    
    <!-- TipTap Content Processing Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Process TipTap content
        const contentContainers = document.querySelectorAll('.tiptap-content, .filament-rich-content');
        
        contentContainers.forEach(container => {
            // Process YouTube embeds in content BUT IGNORE our YouTube component iframes
            const youtubeIframes = container.querySelectorAll('iframe[src*="youtube.com"]:not([data-youtube-processed="true"]), iframe[src*="youtu.be"]:not([data-youtube-processed="true"])');
            youtubeIframes.forEach(iframe => {
                // Skip if this is a YouTube component iframe
                if (iframe.hasAttribute('data-youtube-processed') || 
                    iframe.classList.contains('youtube-component-iframe') ||
                    iframe.closest('.youtube-player-container[data-youtube-component="true"]')) {
                    return; // Skip processing this iframe
                }
                
                // Create responsive wrapper for non-component YouTube iframes
                const wrapper = document.createElement('div');
                wrapper.className = 'responsive-video-wrapper';
                wrapper.style.cssText = `
                    position: relative;
                    padding-bottom: 56.25%;
                    height: 0;
                    overflow: hidden;
                    max-width: 100%;
                    margin: 2rem 0;
                    border-radius: 0.75rem;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                `;
                
                iframe.style.cssText = `
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    border: none;
                    border-radius: 0.75rem;
                `;
                
                // Wrap iframe
                iframe.parentNode.insertBefore(wrapper, iframe);
                wrapper.appendChild(iframe);
            });
            
            // Enhanced Code Block Processing with Copy Functionality
            const codeBlocks = container.querySelectorAll('pre code');
            codeBlocks.forEach((codeElement, index) => {
                const preElement = codeElement.parentElement;
                
                // Skip if already processed
                if (preElement.querySelector('.code-copy-btn')) return;
                
                // Create copy button
                const copyBtn = document.createElement('button');
                copyBtn.className = 'code-copy-btn cursor-pointer';
                copyBtn.innerHTML = `
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    copy
                `;
                
                // Add copy functionality
                copyBtn.addEventListener('click', async function() {
                    try {
                        const codeText = codeElement.textContent || codeElement.innerText;
                        await navigator.clipboard.writeText(codeText);
                        
                        // Update button appearance
                        copyBtn.classList.add('copied');
                        copyBtn.innerHTML = `
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            copied
                        `;
                        
                        // Reset button after 2 seconds
                        setTimeout(() => {
                            copyBtn.classList.remove('copied');
                            copyBtn.innerHTML = `
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                copy
                            `;
                        }, 2000);
                        
                    } catch (err) {
                        console.error('Failed to copy code:', err);
                        // Fallback: select text for manual copy
                        const range = document.createRange();
                        range.selectNodeContents(codeElement);
                        const selection = window.getSelection();
                        selection.removeAllRanges();
                        selection.addRange(range);
                        
                        // Show fallback message
                        copyBtn.innerHTML = `
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Selected
                        `;
                        
                        setTimeout(() => {
                            copyBtn.innerHTML = `
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                copy
                            `;
                        }, 2000);
                    }
                });
                
                // Add button to pre element
                preElement.appendChild(copyBtn);
                
                // Try to detect language and add label
                const className = codeElement.className || '';
                const langMatch = className.match(/language-(\w+)/) || className.match(/lang-(\w+)/);
                
                if (langMatch && langMatch[1]) {
                    const langLabel = document.createElement('span');
                    langLabel.className = 'code-lang-label';
                    langLabel.textContent = langMatch[1];
                    preElement.appendChild(langLabel);
                    
                    // Adjust copy button position if language label exists
                    copyBtn.style.top = '2.25rem';
                }
                
                // Add unique identifier
                preElement.setAttribute('data-code-block-id', `code-block-${index}`);
            });
            
            // Ensure content visibility
            container.style.visibility = 'visible';
            container.style.opacity = '1';
        });
        
        // Add keyboard shortcut for copying focused code block (Ctrl+C)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'c') {
                const focusedCodeBlock = document.activeElement.closest('pre');
                if (focusedCodeBlock) {
                    const copyBtn = focusedCodeBlock.querySelector('.code-copy-btn');
                    if (copyBtn) {
                        e.preventDefault();
                        copyBtn.click();
                    }
                }
            }
        });
    });
    </script>
</body>
<script>
// Alpine.js Data Function
function courseData() {
    return {
        sidebarOpen: false,
        isLoading: false,
        @if(isset($currentProgress))
        currentProgress: {{ $currentProgress ?? 0 }},
        totalLessons: {{ $totalLessons ?? 1 }},
        completedLessons: {{ count($completedLessons ?? []) }},
        isLessonCompleted: {{ isset($isCompleted) && $isCompleted ? 'true' : 'false' }},
        @endif
        openSections: {
            @if($course->courseSections->count() > 0)
                @foreach($course->courseSections as $loop_index => $section)
                'section_{{ $section->id }}': {{ $currentSection && $currentSection->id == $section->id ? 'true' : 'false' }}{{ !$loop->last ? ',' : '' }}
                @endforeach
            @endif
        },
        
        // Toggle section function
        toggleSection(sectionId) {
            this.openSections[sectionId] = !this.openSections[sectionId];
        },
        
        // Mark lesson as complete function
        async markLessonComplete() {
            if (this.isLessonCompleted || this.isLoading) return;
            
            this.isLoading = true;
            
            try {
                const response = await fetch('{{ route('api.lesson-progress.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        section_content_id: {{ $sectionContent->id }}
                    })
                });
                
                if (response.ok) {
                    this.isLessonCompleted = true;
                    this.completedLessons++;
                    this.currentProgress = Math.round((this.completedLessons / this.totalLessons) * 100);
                    
                    // Success notification
                    this.showNotification('Lesson marked as completed!', 'success');
                } else {
                    throw new Error('Failed to mark lesson as complete');
                }
            } catch (error) {
                console.error('Error marking lesson complete:', error);
                this.showNotification('Failed to mark lesson as complete. Please try again.', 'error');
            } finally {
                this.isLoading = false;
            }
        },
        
        // Simple notification system
        showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-lochmara-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
    }
}

// Initialize function
function initializeCourse() {
    // Alpine.js component initialized
}
</script>
</html>