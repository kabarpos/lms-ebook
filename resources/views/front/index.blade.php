@extends('front.layouts.app')
@section('title', 'DripCourse - Learning Platform')
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
    <section class="relative hero-gradient py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Hero Content -->
                <div class="space-y-8">
                    <!-- Trust Badge -->
                    <div class="inline-flex items-center space-x-2 px-4 py-2 bg-lochmara-50 text-lochmara-700 rounded-full text-sm font-semibold">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span>TRUSTED BY 500+ COMPANIES</span>
                    </div>
                    
                    <!-- Main Heading -->
                    <div class="space-y-4">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight">
                            Tingkatkan Skills,<br>
                            <span class="text-lochmara-600">Get Higher Salary</span>
                        </h1>
                        <p class="text-lg text-gray-600 leading-relaxed max-w-lg">
                            Materi terbaru disusun oleh professional dan perusahaan besar agar lebih sesuai kebutuhan dan anda.
                        </p>
                    </div>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center justify-center px-8 py-4 bg-lochmara-600 text-white font-semibold text-lg rounded-lg hover:bg-lochmara-700 transition-colors duration-200 shadow-lg hover:shadow-xl">
                            Get Started
                        </a>
                        <a href="{{ route('front.pricing') }}" 
                           class="inline-flex items-center justify-center px-8 py-4 border-2 border-gray-300 text-gray-700 font-semibold text-lg rounded-lg hover:border-lochmara-300 hover:text-lochmara-600 transition-all duration-200">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M19 10a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            View Pricing
                        </a>
                    </div>
                    
                    {{-- <!-- Social Proof -->
                    <div class="flex items-center space-x-4">
                        <div class="flex -space-x-2">
                            <div class="w-10 h-10 rounded-full bg-lochmara-500 border-2 border-white flex items-center justify-center">
                                <span class="text-white text-sm font-semibold">A</span>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-lochmara-600 border-2 border-white flex items-center justify-center">
                                <span class="text-white text-sm font-semibold">B</span>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-lochmara-700 border-2 border-white flex items-center justify-center">
                                <span class="text-white text-sm font-semibold">C</span>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-gray-100 border-2 border-white flex items-center justify-center">
                                <span class="text-gray-600 text-xs font-semibold">+500</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span class="ml-2 text-sm font-semibold text-gray-900">5.0</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                <span class="font-semibold">{{ number_format($totalStudents ?? 0) }}+ Students</span> • 
                                <span class="font-semibold">{{ $totalCourses ?? 0 }} Courses</span>
                            </p>
                        </div>
                    </div> --}}
                </div>
                
                <!-- Hero Image -->
                <div class="relative">
                    <div class="aspect-square lg:aspect-auto lg:h-[600px] relative">
                        <img src="{{ asset('assets/images/backgrounds/hero-image.png') }}" 
                             alt="Learning Platform Hero" 
                             class="w-full h-full object-cover rounded-2xl shadow-2xl">
                        
                        <!-- Floating Cards -->
                        <div class="absolute top-8 right-8 bg-white rounded-lg shadow-lg p-4 transform rotate-3 hidden lg:block">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-lochmara-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-lochmara-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-900">Top Rated</p>
                                    <p class="text-xs text-gray-500">5.0 Rating</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="absolute bottom-8 left-8 bg-white rounded-lg shadow-lg p-4 transform -rotate-3 hidden lg:block">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-900">Certificate</p>
                                    <p class="text-xs text-gray-500">Verified</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
        
        @if(isset($featuredCourses) && $featuredCourses->count() > 0)
        <!-- Featured Courses Section -->
        <section id="featured-courses" class="bg-gray-50 py-16 lg:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center space-y-4 mb-12">
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900">Featured Courses</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Kursus terpopuler yang diikuti oleh ribuan students
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($featuredCourses as $course)
                    <a href="{{ route('front.course.details', $course->slug) }}" 
                       class="group bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:border-lochmara-300 transition-all duration-300">
                        <!-- Course Image -->
                        <div class="aspect-video bg-gray-100 overflow-hidden">
                            @if($course->thumbnail)
                                @if(str_starts_with($course->thumbnail, 'http'))
                                    <img src="{{ $course->thumbnail }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $course->name }}">
                                @else
                                    <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $course->name }}">
                                @endif
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-lochmara-500 to-lochmara-600">
                                    <span class="text-white font-bold text-2xl">{{ substr($course->name, 0, 2) }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Course Content -->
                        <div class="p-6 space-y-4">
                            <!-- Course Meta -->
                            <div class="flex items-center justify-between">
                                @if($course->category)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-lochmara-100 text-lochmara-700">
                                        {{ $course->category->name }}
                                    </span>
                                @endif
                                <span class="text-sm text-gray-500">
                                    {{ $course->course_students_count ?? 0 }} students
                                </span>
                            </div>
                            
                            <!-- Course Title -->
                            <h3 class="text-xl font-bold text-gray-900 line-clamp-2 group-hover:text-lochmara-700 transition-colors duration-200">
                                {{ $course->name }}
                            </h3>
                            
                            <!-- Course Description -->
                            <p class="text-gray-600 line-clamp-3 text-sm leading-relaxed">
                                {{ $course->about }}
                            </p>
                            
                            <!-- Course Rating & CTA -->
                            <div class="flex items-center justify-between pt-2">
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">5.0</span>
                                </div>
                                
                                <span class="text-sm font-semibold text-lochmara-600 group-hover:text-lochmara-700 transition-colors duration-200">
                                    View Details →
                                </span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                
                <!-- View All Button -->
                <div class="text-center mt-12">
                    <a href="{{ route('front.pricing') }}" 
                       class="inline-flex items-center px-8 py-3 bg-lochmara-600 text-white font-semibold rounded-lg hover:bg-lochmara-700 transition-colors duration-200 shadow-lg hover:shadow-xl">
                        View All Courses
                    </a>
                </div>
            </div>
        </section>
        @endif
@endsection
