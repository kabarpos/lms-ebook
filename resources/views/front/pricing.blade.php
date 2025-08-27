@extends('front.layouts.app')
@section('title', 'Pricing - LMS DripCourse')

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
    <section class="hero-gradient py-16 lg:py-24" style="font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif !important;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Trust Badge -->
            <div class="inline-flex items-center space-x-2 px-4 py-2 bg-lochmara-50 text-lochmara-700 rounded-full text-sm font-semibold mb-6">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span>UNLOCK PRO JOURNEY</span>
            </div>
            
            <!-- Main Heading -->
            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 mb-4">
                Pricing For Everyone
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Harga yang kami tetapkan tergolong murah namun mentor tetap memberikan kualitas standard internasional
            </p>
        </div>
    </section>
    
    <!-- Pricing Cards Section -->
    <section class="bg-gray-50 py-16" style="font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif !important;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-6">
                
                <!-- Beasiswa Plan -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 relative">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Beasiswa</h3>
                    </div>
                    
                    <div class="mb-6">
                        <div class="text-3xl font-bold text-gray-900 mb-2">Rp 0</div>
                        <p class="text-gray-500">3 months duration</p>
                    </div>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Access {{ min($totalCourses ?? 10, 100) }}+ Online Courses</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Get Premium Certifications</span>
                        </div>
                    </div>
                    
                    <button class="w-full py-3 px-4 bg-gray-100 text-gray-500 font-semibold rounded-lg cursor-not-allowed">
                        Sold Out
                    </button>
                </div>
                
                <!-- Dynamic Pricing Packages -->
                @foreach($pricing_packages as $package)
                <div class="bg-white rounded-xl shadow-lg border-2 border-lochmara-500 p-8 relative transform hover:scale-105 transition-transform duration-200">
                    <!-- Popular Badge -->
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-lochmara-600 text-white px-6 py-2 rounded-full text-sm font-semibold shadow-lg">
                            Most Popular Package
                        </span>
                    </div>
                    
                    <div class="flex items-center space-x-3 mb-6 mt-4">
                        <div class="w-12 h-12 bg-lochmara-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-lochmara-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $package->name }}</h3>
                    </div>
                    
                    <div class="mb-6">
                        <div class="text-3xl font-bold text-gray-900 mb-2">
                            Rp {{ number_format($package->price, 0, '', '.') }}
                        </div>
                        <p class="text-gray-500">{{ $package->duration }} months duration</p>
                    </div>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Access {{ $totalCourses ?? 100 }}+ Online Courses</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Get Premium Certifications</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">High Quality Work Portfolio</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Career Consultation 2025</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Support learning 24/7</span>
                        </div>
                    </div>
                    
                    @if ($user && $package->isSubscribedByUser($user->id))
                        <button class="w-full py-3 px-4 bg-lochmara-600 text-white font-semibold rounded-lg shadow-lg">
                            You've Subscribed
                        </button>
                    @else
                        <a href="{{ route('front.checkout', $package) }}" 
                           class="block w-full py-3 px-4 bg-lochmara-600 text-white font-semibold rounded-lg text-center hover:bg-lochmara-700 transition-colors duration-200 shadow-lg hover:shadow-xl cursor-pointer">
                            Get Pro
                        </a>
                    @endif
                </div>
                @endforeach
                
                <!-- Business Plan -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 relative">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-6a1 1 0 00-1-1H9a1 1 0 00-1 1v6a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Business</h3>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">
                            Customizing easily without paying too much money
                        </h4>
                    </div>
                    
                    <div class="mb-8">
                        <p class="text-gray-600 leading-relaxed">
                            Kami bantu siapkan materi ajar sesuai kebutuhan pertumbuhan perusahaan anda saat ini.
                        </p>
                    </div>
                    
                    <a href="#" 
                       class="block w-full py-3 px-4 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg text-center hover:border-lochmara-300 hover:text-lochmara-600 transition-all duration-200 cursor-pointer">
                        Contact Sales
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials Section -->
    <section class="bg-white py-16" style="font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif !important;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 mb-4">
                    What Our Students Say
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Testimonials from students who have transformed their careers
                </p>
            </div>
            
            <!-- Testimonials Slider -->
            <div class="relative overflow-hidden">
                <div class="flex space-x-6 animate-scroll">
                    <!-- Testimonial Cards -->
                    @for($i = 0; $i < 6; $i++)
                    <div class="flex-shrink-0 w-80 bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                        <!-- Stars -->
                        <div class="flex items-center space-x-1">
                            @for($star = 1; $star <= 5; $star++)
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        
                        <!-- Testimonial Text -->
                        <p class="text-gray-600 leading-relaxed">
                            "Asik banget belajar di sini dapat contoh kasus sesuai kebutuhan perusahaan saat ini proses adaptasi jadi lebih cepat dan produktif."
                        </p>
                        
                        <!-- Profile -->
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gradient-to-br from-lochmara-500 to-lochmara-600">
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="text-white font-semibold">AR</span>
                                </div>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Angga Risky</p>
                                <p class="text-sm text-gray-500">Full Stack Developer</p>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </section>
    
    <style>
    @keyframes scroll {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-50%);
        }
    }
    
    .animate-scroll {
        animation: scroll 30s linear infinite;
    }
    
    .animate-scroll:hover {
        animation-play-state: paused;
    }
    </style>
@endsection
