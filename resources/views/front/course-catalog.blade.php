@extends('front.layouts.app')
@section('title', 'Course Catalog - DripCourse')

@section('content')
    <style>
    /* Force Manrope Font Implementation */
    body, html, * {
        font-family: "Manrope", ui-sans-serif, system-ui, sans-serif !important;
    }
    
    .hero-gradient {
        background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%);
    }
    </style>
    
    <x-nav-guest />
    
    <!-- Hero Section -->
    <section class="hero-gradient py-16 lg:py-24 font-manrope">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Trust Badge -->
            <div class="inline-flex items-center space-x-2 px-4 py-2 bg-lochmara-50 text-lochmara-700 rounded-full text-sm font-semibold mb-6">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span>BUY INDIVIDUAL COURSES</span>
            </div>
            
            <!-- Main Heading -->
            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 mb-4">
                Choose Your Perfect Course
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                We've moved from subscriptions to individual course purchases. Buy only what you need and own it forever!
            </p>
        </div>
    </section>
    
    <!-- Course Stats Section -->
    <section class="bg-white py-12 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="space-y-2">
                    <div class="text-3xl font-bold text-lochmara-600">{{ $totalCourses }}+</div>
                    <div class="text-gray-600">Quality Courses</div>
                </div>
                <div class="space-y-2">
                    <div class="text-3xl font-bold text-lochmara-600">{{ $totalStudents }}+</div>
                    <div class="text-gray-600">Happy Students</div>
                </div>
                <div class="space-y-2">
                    <div class="text-3xl font-bold text-lochmara-600">Lifetime</div>
                    <div class="text-gray-600">Access Guaranteed</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Courses Section -->
    @if($featuredCourses->isNotEmpty())
    <section class="bg-gray-50 py-16 font-manrope">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">Featured Courses</h2>
                <p class="text-lg text-gray-600">Hand-picked courses from our experts</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($featuredCourses as $course)
                    <x-course-card :course="$course" />
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- All Courses Section -->
    <section class="bg-white py-16 font-manrope">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">All Courses</h2>
                <p class="text-lg text-gray-600">Explore our complete course catalog</p>
            </div>
            
            @if($allCourses->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($allCourses as $course)
                        <x-course-card :course="$course" />
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-20 h-20 bg-gradient-to-br from-lochmara-100 to-lochmara-200 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-lochmara-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">No Courses Available</h3>
                    <p class="text-gray-600 max-w-md mx-auto">Check back later for new courses.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="bg-lochmara-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">Why Choose Individual Courses?</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-lochmara-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-lochmara-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Pay Only for What You Need</h3>
                    <p class="text-gray-600">No recurring subscriptions. Buy specific courses that match your goals.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-lochmara-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-lochmara-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Lifetime Access</h3>
                    <p class="text-gray-600">Once purchased, the course is yours forever. Learn at your own pace.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-lochmara-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-lochmara-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Certificate of Completion</h3>
                    <p class="text-gray-600">Get recognized for your achievements with course completion certificates.</p>
                </div>
            </div>
        </div>
    </section>

@endsection