@extends('front.layouts.app')
@section('title', 'My Courses - BuildWithAngga')
@section('content')
    <x-navigation-auth />
    
    <!-- Main Content -->
    <main class="bg-gray-50 min-h-screen py-8" style="font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif !important;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">            
            <!-- Course Catalog Section -->
            <section class="space-y-6">
                <div class="text-center space-y-4">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">My Learning Dashboard</h1>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">Continue your learning journey with your enrolled courses</p>
                </div>
                
                <!-- Category Tabs -->
                <div class="flex flex-wrap gap-3 justify-center">
                    @foreach ($coursesByCategory as $category => $courses)
                        <button type="button" 
                                class="tab-btn group {{ $loop->first ? 'active' : '' }} px-6 py-3 rounded-xl border transition-all duration-200"
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
                             class="{{ $loop->first ? '' : 'hidden' }} tab-content course-grid">
                            @forelse($courses as $course)
                                <x-course-card :course="$course" />
                            @empty
                                <div class="col-span-full text-center py-16">
                                    <div class="w-20 h-20 bg-gradient-to-br from-lochmara-100 to-lochmara-200 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <svg class="w-10 h-10 text-lochmara-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-3">No Courses Available</h3>
                                    <p class="text-gray-600 max-w-md mx-auto">There are no courses in this category yet. Please check other categories or come back later.</p>
                                </div>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </main>
    
    <style>
    /* Force Manrope Font Implementation */
    body, html, * {
        font-family: "Manrope", ui-sans-serif, system-ui, sans-serif !important;
    }
    
    .tab-btn {
        @apply border-gray-300 text-gray-700 hover:border-lochmara-300 hover:text-lochmara-600;
        font-family: "Manrope", ui-sans-serif, system-ui, sans-serif !important;
    }
    
    .tab-btn.active {
        @apply bg-gradient-to-r from-lochmara-600 to-lochmara-700 border-lochmara-600 text-white shadow-lg;
    }
    
    /* Enhanced course cards */
    .course-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    
    @media (max-width: 640px) {
        .course-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>

@endsection

@push('after-scripts')
    <script src="{{ asset('js/tabs.js') }}"></script>
@endpush
