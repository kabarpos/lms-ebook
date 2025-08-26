@extends('front.layouts.app')
@section('title', $course->name . ' - Obito BuildWithAngga')

@section('content')
    <x-nav-guest />
    
    <!-- Clean Hero Section -->
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Course Information -->
                <div class="space-y-8">
                    <!-- Category Badge -->
                    @if($course->category)
                        <div class="inline-flex items-center px-4 py-2 bg-lochmara-50 text-lochmara-700 rounded-full border border-lochmara-200">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-sm font-medium">{{ $course->category->name }}</span>
                        </div>
                    @endif
                    
                    <!-- Course Title -->
                    <div class="space-y-4">
                        <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 leading-tight">
                            {{ $course->name }}
                        </h1>
                        <p class="text-lg text-gray-600 leading-relaxed max-w-2xl">
                            {{ $course->about }}
                        </p>
                    </div>
                    
                    <!-- Stats -->
                    <div class="flex flex-wrap items-center gap-6">
                        <!-- Rating -->
                        <div class="flex items-center space-x-2 bg-gray-50 px-4 py-2 rounded-lg">
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-sm font-medium text-gray-700">5.0</span>
                        </div>
                        
                        <!-- Students Count -->
                        <div class="flex items-center space-x-2 bg-gray-50 px-4 py-2 rounded-lg">
                            <svg class="w-4 h-4 text-lochmara-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">{{ $course->courseStudents->count() }} Students</span>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        @auth
                            <a href="{{ route('dashboard.course.join', $course->slug) }}" 
                               class="inline-flex items-center justify-center px-6 py-3 bg-lochmara-600 text-white font-medium rounded-lg hover:bg-lochmara-700 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Start Learning
                            </a>
                        @else
                            <a href="{{ route('register') }}" 
                               class="inline-flex items-center justify-center px-6 py-3 bg-lochmara-600 text-white font-medium rounded-lg hover:bg-lochmara-700 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Start Learning
                            </a>
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                Sign In
                            </a>
                        @endauth
                    </div>
                </div>
                
                <!-- Course Visual -->
                <div class="flex justify-center lg:justify-end">
                    <div class="relative w-full max-w-lg">
                        <div class="aspect-video rounded-xl overflow-hidden bg-gray-100 shadow-lg">
                            @if($course->thumbnail)
                                @if(str_starts_with($course->thumbnail, 'http'))
                                    <img src="{{ $course->thumbnail }}" class="w-full h-full object-cover" alt="{{ $course->name }}">
                                @else
                                    <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-full object-cover" alt="{{ $course->name }}">
                                @endif
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-lochmara-100">
                                    <div class="text-center">
                                        <div class="text-lochmara-600 font-bold text-3xl mb-2">{{ substr($course->name, 0, 2) }}</div>
                                        <div class="text-lochmara-500 text-sm">Course Preview</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
        
    <!-- Main Content Section -->
    <main class="bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Course Content -->
                <div class="lg:col-span-2 space-y-12">
                    <!-- Course Curriculum Section -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <!-- Section Header -->
                        <div class="px-8 py-6 border-b border-gray-100">
                            <h2 class="text-2xl font-bold text-gray-900">Course Curriculum</h2>
                            <p class="text-gray-600 mt-2">Structured learning path designed by industry experts</p>
                        </div>
                        
                        <!-- Curriculum Content -->
                        <div class="p-8">
                            @if($course->courseSections->count() > 0)
                                <div class="space-y-6">
                                    @foreach($course->courseSections as $index => $section)
                                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                                            <!-- Section Header -->
                                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <!-- Section Number -->
                                                        <div class="w-10 h-10 bg-lochmara-600 text-white rounded-lg flex items-center justify-center mr-4">
                                                            <span class="font-bold text-sm">{{ $index + 1 }}</span>
                                                        </div>
                                                        
                                                        <!-- Section Info -->
                                                        <div>
                                                            <h3 class="text-lg font-semibold text-gray-900">{{ $section->name }}</h3>
                                                            <p class="text-sm text-gray-600">{{ $section->sectionContents->count() }} lessons</p>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Lesson Count Badge -->
                                                    <div class="flex items-center bg-white px-3 py-1 rounded-full border border-gray-200">
                                                        <svg class="w-4 h-4 text-lochmara-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="text-sm font-medium text-gray-700">{{ $section->sectionContents->count() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Lessons List -->
                                            <div class="p-6">
                                                <div class="space-y-3">
                                                    @foreach($section->sectionContents->take(3) as $contentIndex => $content)
                                                        <div class="flex items-center p-4 rounded-lg border border-gray-100 hover:border-lochmara-200 hover:bg-lochmara-50 transition-all duration-200">
                                                            <!-- Lesson Icon -->
                                                            <div class="w-8 h-8 bg-lochmara-100 text-lochmara-600 rounded-lg flex items-center justify-center mr-3">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                                                </svg>
                                                            </div>
                                                            
                                                            <!-- Lesson Info -->
                                                            <div class="flex-1">
                                                                <h4 class="font-medium text-gray-900">{{ $content->name }}</h4>
                                                                <div class="flex items-center text-xs text-gray-500 mt-1">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                    </svg>
                                                                    <span>8-12 min</span>
                                                                    <span class="mx-1">•</span>
                                                                    <span>Video</span>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Status -->
                                                            <div class="w-6 h-6 border-2 border-gray-300 rounded-full"></div>
                                                        </div>
                                                    @endforeach
                                                    
                                                    @if($section->sectionContents->count() > 3)
                                                        <div class="flex items-center justify-center p-4 border-2 border-dashed border-gray-200 rounded-lg text-gray-500">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                            </svg>
                                                            <span class="text-sm">+ {{ $section->sectionContents->count() - 3 }} more lessons</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Course Content Coming Soon</h3>
                                    <p class="text-gray-600">We're preparing amazing curriculum for you. Stay tuned!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                        
                    <!-- What You'll Learn Section -->
                    @if($course->benefits->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-8 py-6 border-b border-gray-100">
                            <h2 class="text-2xl font-bold text-gray-900">What You'll Learn</h2>
                            <p class="text-gray-600 mt-2">Key skills and knowledge you'll gain from this course</p>
                        </div>
                        <div class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($course->benefits as $index => $benefit)
                                    <div class="flex items-start space-x-3 p-4 rounded-lg border border-gray-100 hover:border-lochmara-200 hover:bg-lochmara-50 transition-all duration-200">
                                        <div class="w-6 h-6 bg-lochmara-100 text-lochmara-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $benefit->name }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">Master this essential skill through hands-on practice</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-8 space-y-6">
                        <!-- Course Stats -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Details</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-600">Total Lessons</span>
                                    <span class="font-semibold text-gray-900">{{ $course->courseSections->sum(function($section) { return $section->sectionContents->count(); }) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-600">Students Enrolled</span>
                                    <span class="font-semibold text-gray-900">{{ $course->courseStudents->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-600">Course Level</span>
                                    <span class="font-semibold text-gray-900">Beginner</span>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-gray-600">Certificate</span>
                                    <span class="font-semibold text-lochmara-600">Yes</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                @auth
                                    <a href="{{ route('dashboard.course.join', $course->slug) }}" 
                                       class="w-full flex items-center justify-center px-4 py-3 bg-lochmara-600 text-white font-medium rounded-lg hover:bg-lochmara-700 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        Start Learning
                                    </a>
                                @else
                                    <a href="{{ route('register') }}" 
                                       class="w-full flex items-center justify-center px-4 py-3 bg-lochmara-600 text-white font-medium rounded-lg hover:bg-lochmara-700 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Create Account
                                    </a>
                                @endauth
                                
                                <button class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                    </svg>
                                    Share Course
                                </button>
                                
                                <button class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    Save to Wishlist
                                </button>
                            </div>
                        </div>
                        
                        <!-- Category Info -->
                        @if($course->category)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Category</h3>
                            <div class="flex items-center p-3 bg-lochmara-50 rounded-lg border border-lochmara-200">
                                <div class="w-10 h-10 bg-lochmara-100 text-lochmara-600 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $course->category->name }}</h4>
                                    <p class="text-sm text-gray-600">Explore more courses</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection