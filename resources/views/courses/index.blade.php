@extends('front.layouts.app')
@section('title', 'My Courses - Obito BuildWithAngga')
@section('content')
    <x-navigation-auth />
    
    <!-- Dashboard Navigation -->
    <nav class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-1 py-4 overflow-x-auto">
                <a href="#" class="flex items-center space-x-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:border-lochmara-300 hover:text-lochmara-600 transition-all duration-200 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span>Overview</span>
                </a>
                <a href="#" class="flex items-center space-x-2 px-4 py-2 rounded-lg bg-lochmara-50 border border-lochmara-300 text-lochmara-700 font-semibold whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Courses</span>
                </a>
                <a href="#" class="flex items-center space-x-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:border-lochmara-300 hover:text-lochmara-600 transition-all duration-200 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Quizzes</span>
                </a>
                <a href="#" class="flex items-center space-x-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:border-lochmara-300 hover:text-lochmara-600 transition-all duration-200 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    <span>Certificates</span>
                </a>
                <a href="#" class="flex items-center space-x-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:border-lochmara-300 hover:text-lochmara-600 transition-all duration-200 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span>Portfolios</span>
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- Popular Roadmap Section -->
            <section class="space-y-6">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900">Popular Roadmap</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Roadmap Card 1 -->
                    <a href="#" class="group bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-lochmara-300 transition-all duration-300">
                        <div class="flex flex-col sm:flex-row items-start space-y-4 sm:space-y-0 sm:space-x-4">
                            <div class="relative w-full sm:w-60 h-40 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                <img src="{{ asset('assets/images/thumbnails/thumbnail-1.png') }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
                                     alt="roadmap thumbnail">
                                <div class="absolute bottom-2 left-2 right-2 bg-white rounded-lg px-2 py-1 shadow-sm">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4 text-lochmara-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="text-xs font-semibold text-gray-700">Featured In AI Industry</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 space-y-3">
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-lochmara-700 transition-colors duration-200 line-clamp-2">
                                    Full-Stack Sr. Website JavaScript Developer 2025
                                </h3>
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-lochmara-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0H8m8 0v2a2 2 0 01-2 2H10a2 2 0 01-2-2V6"/>
                                        </svg>
                                        <span>Rp 125.500.000/year</span>
                                    </div>
                                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-lochmara-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <span>18,498 Courses</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    
                    <!-- Roadmap Card 2 -->
                    <a href="#" class="group bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-lochmara-300 transition-all duration-300">
                        <div class="flex flex-col sm:flex-row items-start space-y-4 sm:space-y-0 sm:space-x-4">
                            <div class="relative w-full sm:w-60 h-40 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                <img src="{{ asset('assets/images/thumbnails/thumbnail-2.png') }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
                                     alt="roadmap thumbnail">
                                <div class="absolute bottom-2 left-2 right-2 bg-white rounded-lg px-2 py-1 shadow-sm">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4 text-lochmara-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="text-xs font-semibold text-gray-700">Featured In AI Industry</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 space-y-3">
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-lochmara-700 transition-colors duration-200 line-clamp-2">
                                    Digital Marketing Enterprise User Acquisitions Level
                                </h3>
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-lochmara-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0H8m8 0v2a2 2 0 01-2 2H10a2 2 0 01-2-2V6"/>
                                        </svg>
                                        <span>Rp 125.500.000/year</span>
                                    </div>
                                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-lochmara-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <span>18,498 Courses</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </section>
            
            <!-- Course Catalog Section -->
            <section class="space-y-6">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900">Course Catalog</h2>
                
                <!-- Category Tabs -->
                <div class="flex flex-wrap gap-3">
                    @foreach ($coursesByCategory as $category => $courses)
                        <button type="button" 
                                class="tab-btn group {{ $loop->first ? 'active' : '' }} px-4 py-2 rounded-lg border transition-all duration-200"
                                data-target="{{ Str::slug($category) }}-content">
                            <span class="text-sm font-medium transition-colors duration-200 group-[.active]:text-white group-[.active]:font-semibold">
                                {{ $category }}
                            </span>
                        </button>
                    @endforeach
                </div>
                
                <!-- Course Content -->
                <div id="tabs-content-container">
                    @foreach ($coursesByCategory as $category => $courses)
                        <div id="{{ Str::slug($category) }}-content" 
                             class="{{ $loop->first ? '' : 'hidden' }} tab-content grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @forelse($courses as $course)
                                <x-course-card :course="$course" />
                            @empty
                                <div class="col-span-full text-center py-12">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Kelas</h3>
                                    <p class="text-gray-600">Belum ada kelas pada kategori ini. Silakan coba kategori lain.</p>
                                </div>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </main>
    
    <style>
    .tab-btn {
        @apply border-gray-300 text-gray-700 hover:border-lochmara-300 hover:text-lochmara-600;
    }
    
    .tab-btn.active {
        @apply bg-lochmara-600 border-lochmara-600 text-white;
    }
    </style>

@endsection

@push('after-scripts')
    <script src="{{ asset('js/tabs.js') }}"></script>
@endpush
