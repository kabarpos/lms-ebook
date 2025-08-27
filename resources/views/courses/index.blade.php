@extends('front.layouts.app')
@section('title', 'My Courses - DripCourse')
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
                
                <!-- Course Content -->
                <div class="flex flex-wrap gap-6 justify-start">
                    @foreach ($coursesByCategory as $category => $courses)
                        @foreach($courses as $course)
                            <div class="w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] xl:w-[calc(25%-18px)]">
                                <x-course-card :course="$course" />
                            </div>
                        @endforeach
                    @endforeach
                    
                    @if($coursesByCategory->isEmpty() || $coursesByCategory->flatten()->isEmpty())
                        <div class="w-full text-center py-16">
                            <div class="w-20 h-20 bg-gradient-to-br from-lochmara-100 to-lochmara-200 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-lochmara-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">No Courses Available</h3>
                            <p class="text-gray-600 max-w-md mx-auto">There are no courses available. Please check back later.</p>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </main>
    
    <style>
    /* Force Manrope Font Implementation */
    body, html, * {
        font-family: "Manrope", ui-sans-serif, system-ui, sans-serif !important;
    }
    
    /* Responsive flex layout for course cards */
    @media (max-width: 640px) {
        .course-card-container {
            width: 100% !important;
        }
    }
    </style>

@endsection
