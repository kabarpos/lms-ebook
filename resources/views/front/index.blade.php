@extends('front.layouts.app')
@section('title', 'Aksellera - Learning Platform')
@section('content')
    <style nonce="{{ request()->attributes->get('csp_nonce') }}">
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
                    <div class="inline-flex items-center space-x-2 px-4 py-2 bg-mountain-meadow-50 text-mountain-meadow-700 rounded-full text-sm font-semibold">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span>TRUSTED BY 500+ COMPANIES</span>
                    </div>
                    
                    <!-- Main Heading -->
                    <div class="space-y-4">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight">
                            Belajar Bisnis Online,
                            <span class="text-mountain-meadow-600">Mulai Dari 0</span>
                        </h1>
                        <p class="text-lg text-gray-600 leading-relaxed max-w-lg">
                            Materi terbaru disusun oleh professional dan perusahaan besar agar lebih sesuai kebutuhan dan anda.
                        </p>
                    </div>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center justify-center px-8 py-4 bg-mountain-meadow-600 text-white font-semibold text-lg rounded-lg hover:bg-mountain-meadow-700 transition-colors duration-200 shadow-lg hover:shadow-xl cursor-pointer">
                            Get Started
                        </a>
                        <a href="{{ route('front.courses') }}" 
                           class="inline-flex items-center justify-center px-8 py-4 border-2 border-gray-300 text-gray-700 font-semibold text-lg rounded-lg hover:border-mountain-meadow-300 hover:text-mountain-meadow-600 transition-all duration-200 cursor-pointer">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Browse Courses
                        </a>
                    </div>
                    
                    {{-- <!-- Social Proof -->
                    <div class="flex items-center space-x-4">
                        <div class="flex -space-x-2">
                            <div class="w-10 h-10 rounded-full bg-mountain-meadow-500 border-2 border-white flex items-center justify-center">
                                <span class="text-white text-sm font-semibold">A</span>
                            </div>
                             <div class="w-10 h-10 rounded-full bg-mountain-meadow-600 border-2 border-white flex items-center justify-center">
                                <span class="text-white text-sm font-semibold">B</span>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-mountain-meadow-700 border-2 border-white flex items-center justify-center">
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
                    <div class="lg:aspect-auto lg:h-full relative items-center">
                        <x-lazy-image 
                            src="{{ asset('assets/images/backgrounds/dashboard.webp') }}" 
                            alt="Learning Platform Hero" 
                            class="w-full h-[300px] lg:h-[600px] object-cover rounded-2xl shadow-2xl"
                            loading="eager" />
                        
                        <!-- Floating Cards -->
                        <div class="absolute top-8 right-8 bg-white rounded-lg shadow-lg p-4 transform rotate-3 hidden lg:block">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-mountain-meadow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-mountain-meadow-600" fill="currentColor" viewBox="0 0 20 20">
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
        
 <!-- Featured Courses Section -->
    @if($featuredCourses->isNotEmpty())
    <section class="bg-gray-50 py-16 font-manrope">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">Featured Courses</h2>
                <p class="text-lg text-gray-600">Hand-picked courses from our experts</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredCourses as $course)
                    <x-course-card :course="$course" />
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection
