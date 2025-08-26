@extends('front.layouts.app')
@section('title', $course->name . ' - Obito BuildWithAngga')

@section('content')
    <x-nav-guest />
    
    <!-- Hero Section with Dramatic Visual Impact -->
    <section class="relative min-h-screen bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900 overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 via-purple-600/20 to-pink-600/20"></div>
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
        </div>
        
        <!-- Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-32">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center min-h-[80vh]">
                <!-- Course Information -->
                <div class="space-y-10 text-white">
                    <!-- Category Badge -->
                    @if($course->category)
                        <div class="inline-flex items-center px-6 py-3 bg-white/10 backdrop-blur-sm rounded-full border border-white/20">
                            <svg class="w-5 h-5 mr-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-lg font-semibold">{{ $course->category->name }}</span>
                        </div>
                    @endif
                    
                    <!-- Course Title -->
                    <div class="space-y-6">
                        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black leading-tight">
                            <span class="bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">
                                {{ $course->name }}
                            </span>
                        </h1>
                        <div class="flex items-center space-x-4">
                            <div class="h-1 w-20 bg-gradient-to-r from-pink-500 to-yellow-500 rounded-full"></div>
                            <div class="h-1 w-12 bg-gradient-to-r from-blue-500 to-green-500 rounded-full"></div>
                            <div class="h-1 w-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full"></div>
                        </div>
                    </div>
                    
                    <!-- Course Description -->
                    <p class="text-xl lg:text-2xl text-gray-200 leading-relaxed font-light max-w-2xl">
                        {{ $course->about }}
                    </p>
                    
                    <!-- Stats & Rating -->
                    <div class="flex flex-wrap items-center gap-8">
                        <!-- Rating -->
                        <div class="flex items-center space-x-3 bg-white/10 backdrop-blur-sm px-6 py-4 rounded-2xl border border-white/20">
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-2xl font-bold">5.0</span>
                        </div>
                        
                        <!-- Students Count -->
                        <div class="flex items-center space-x-3 bg-white/10 backdrop-blur-sm px-6 py-4 rounded-2xl border border-white/20">
                            <svg class="w-6 h-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                            <div>
                                <div class="text-2xl font-bold">{{ $course->courseStudents->count() }}</div>
                                <div class="text-sm text-gray-300">Students</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-6 pt-8">
                        @auth
                            <a href="{{ route('dashboard.course.details', $course->slug) }}" 
                               class="group relative overflow-hidden px-12 py-5 bg-gradient-to-r from-emerald-500 to-blue-600 text-white font-bold text-lg rounded-2xl transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                                <div class="relative flex items-center justify-center">
                                    <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Continue Learning
                                </div>
                            </a>
                        @else
                            <a href="{{ route('register') }}" 
                               class="group relative overflow-hidden px-12 py-5 bg-gradient-to-r from-emerald-500 to-blue-600 text-white font-bold text-lg rounded-2xl transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                                <div class="relative flex items-center justify-center">
                                    <svg class="w-6 h-6 mr-3 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Start Learning Now
                                </div>
                            </a>
                            <a href="{{ route('login') }}" 
                               class="px-12 py-5 bg-white/10 backdrop-blur-sm text-white font-bold text-lg rounded-2xl border-2 border-white/30 hover:bg-white/20 transition-all duration-300">
                                Already have account? Sign In
                            </a>
                        @endauth
                    </div>
                </div>
                
                <!-- Course Visual -->
                <div class="flex justify-center lg:justify-end">
                    <div class="relative group">
                        <!-- Glow Effect -->
                        <div class="absolute -inset-8 bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 rounded-3xl opacity-30 group-hover:opacity-50 blur-xl transition-opacity duration-500"></div>
                        
                        <!-- Main Image Container -->
                        <div class="relative w-full max-w-2xl aspect-video rounded-3xl overflow-hidden border-4 border-white/20 backdrop-blur-sm shadow-2xl">
                            @if($course->thumbnail)
                                @if(str_starts_with($course->thumbnail, 'http'))
                                    <img src="{{ $course->thumbnail }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="{{ $course->name }}">
                                @else
                                    <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="{{ $course->name }}">
                                @endif
                                
                                <!-- Play Button Overlay -->
                                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-all duration-300 flex items-center justify-center">
                                    <div class="w-20 h-20 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300 shadow-xl">
                                        <svg class="w-8 h-8 text-gray-800 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                        </svg>
                                    </div>
                                </div>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-purple-600 via-blue-600 to-green-600">
                                    <div class="text-center">
                                        <div class="text-white font-black text-8xl mb-4">{{ substr($course->name, 0, 2) }}</div>
                                        <div class="text-white/80 text-xl font-medium">Course Preview</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Down Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <div class="w-6 h-10 border-2 border-white/50 rounded-full flex justify-center">
                <div class="w-1 h-3 bg-white/70 rounded-full mt-2 animate-pulse"></div>
            </div>
        </div>
    </section>
        
    <!-- Main Content Section -->
    <main class="bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">
                <!-- Course Content -->
                <div class="lg:col-span-2 space-y-16">
                    <!-- Course Curriculum Section -->
                    <div class="relative">
                        <!-- Section Header -->
                        <div class="text-center mb-16">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mb-6">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <h2 class="text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                                Course <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Curriculum</span>
                            </h2>
                            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                                Master every concept with our carefully structured learning path designed by industry experts.
                            </p>
                        </div>
                        
                        <!-- Curriculum Content -->
                        @if($course->courseSections->count() > 0)
                            <div class="space-y-8">
                                @foreach($course->courseSections as $index => $section)
                                    <div class="group relative">
                                        <!-- Section Card -->
                                        <div class="relative bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                                            <!-- Section Header -->
                                            <div class="relative bg-gradient-to-r from-gray-50 to-gray-100 group-hover:from-blue-50 group-hover:to-purple-50 px-8 py-8 transition-all duration-300">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <!-- Section Number -->
                                                        <div class="relative">
                                                            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mr-6 transform group-hover:scale-110 transition-transform duration-300">
                                                                <span class="text-2xl font-black text-white">{{ $index + 1 }}</span>
                                                            </div>
                                                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center">
                                                                <svg class="w-3 h-3 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Section Info -->
                                                        <div>
                                                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $section->name }}</h3>
                                                            <p class="text-gray-600">Master the fundamentals and advanced concepts</p>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Lesson Count Badge -->
                                                    <div class="flex items-center bg-white px-6 py-3 rounded-full shadow-md border border-gray-200">
                                                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="font-bold text-gray-900">{{ $section->sectionContents->count() }}</span>
                                                        <span class="text-gray-600 ml-1">lessons</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Lessons List -->
                                            <div class="px-8 py-8">
                                                <div class="grid gap-4">
                                                    @foreach($section->sectionContents->take(3) as $contentIndex => $content)
                                                        <div class="flex items-center p-5 rounded-2xl bg-gray-50 hover:bg-blue-50 transition-colors duration-300 group/lesson">
                                                            <!-- Lesson Icon -->
                                                            <div class="w-12 h-12 bg-gradient-to-r from-emerald-400 to-blue-500 rounded-full flex items-center justify-center mr-4 group-hover/lesson:scale-110 transition-transform duration-300">
                                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                                                </svg>
                                                            </div>
                                                            
                                                            <!-- Lesson Info -->
                                                            <div class="flex-1">
                                                                <h4 class="text-lg font-semibold text-gray-900 mb-1">{{ $content->name }}</h4>
                                                                <div class="flex items-center text-sm text-gray-500">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                    </svg>
                                                                    <span>8-12 minutes</span>
                                                                    <span class="mx-2">•</span>
                                                                    <span>Video Lesson</span>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Completion Status -->
                                                            <div class="w-8 h-8 border-3 border-gray-300 rounded-full flex items-center justify-center group-hover/lesson:border-blue-400 transition-colors duration-300">
                                                                <div class="w-3 h-3 bg-blue-400 rounded-full opacity-0 group-hover/lesson:opacity-100 transition-opacity duration-300"></div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    
                                                    @if($section->sectionContents->count() > 3)
                                                        <div class="flex items-center justify-center p-5 mt-4 border-2 border-dashed border-gray-300 rounded-2xl text-gray-500 hover:border-blue-400 hover:text-blue-600 transition-colors duration-300">
                                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                            </svg>
                                                            <span class="font-medium">+ {{ $section->sectionContents->count() - 3 }} more lessons to explore</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-20">
                                <div class="w-32 h-32 bg-gradient-to-r from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-8">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <h3 class="text-3xl font-bold text-gray-900 mb-4">Course Content Coming Soon</h3>
                                <p class="text-lg text-gray-600 max-w-md mx-auto">We're putting the finishing touches on this amazing curriculum. Stay tuned for updates!</p>
                            </div>
                        @endif
                    </div>
                        
                    <!-- What You'll Learn Section -->
                    @if($course->benefits->count() > 0)
                    <div class="text-center mb-16">
                        <h2 class="text-4xl font-black text-gray-900 mb-4">
                            What You'll <span class="bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">Master</span>
                        </h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach($course->benefits as $index => $benefit)
                            <div class="bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                                <div class="flex items-start space-x-6">
                                    <div class="w-16 h-16 bg-gradient-to-r from-green-100 to-emerald-100 rounded-2xl flex items-center justify-center">
                                        <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $benefit->name }}</h3>
                                        <p class="text-gray-600">Master this essential skill through hands-on practice.</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                
                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-8 space-y-8">
                        <!-- Course Stats -->
                        <div class="bg-white rounded-3xl p-8 shadow-xl">
                            <h3 class="text-2xl font-black text-gray-900 mb-6 text-center">Course Details</h3>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="text-center p-4 bg-blue-50 rounded-2xl">
                                    <div class="text-2xl font-black text-blue-600">{{ $course->courseSections->sum(function($section) { return $section->sectionContents->count(); }) }}</div>
                                    <div class="text-sm text-gray-600">Lessons</div>
                                </div>
                                <div class="text-center p-4 bg-green-50 rounded-2xl">
                                    <div class="text-2xl font-black text-green-600">{{ $course->courseStudents->count() }}</div>
                                    <div class="text-sm text-gray-600">Students</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- CTA Card -->
                        <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-3xl p-8 text-white text-center">
                            @guest
                                <h3 class="text-2xl font-black mb-4">Start Learning Today!</h3>
                                <p class="mb-6">Join thousands of successful students.</p>
                                <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-8 py-4 bg-white text-purple-700 font-bold rounded-2xl hover:bg-gray-100 transition-all duration-300">
                                    Start Free Today
                                </a>
                            @else
                                <h3 class="text-2xl font-black mb-4">Continue Learning</h3>
                                <a href="{{ route('dashboard.course.details', $course->slug) }}" class="w-full inline-flex items-center justify-center px-8 py-4 bg-white text-purple-700 font-bold rounded-2xl hover:bg-gray-100 transition-all duration-300">
                                    Access Course
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection