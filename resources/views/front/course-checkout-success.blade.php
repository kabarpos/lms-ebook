@extends('front.layouts.app')
@section('title', 'Purchase Successful - ' . $course->name)
@section('content')
    <x-navigation-auth />

    <main class="bg-gray-50 min-h-screen py-16">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Success Header -->
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-8 py-12 text-center">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-4">Purchase Successful!</h1>
                    <p class="text-green-100 text-lg">You now have lifetime access to this course</p>
                </div>
                
                <!-- Course Info -->
                <div class="p-8">
                    <div class="flex items-center space-x-6 p-6 bg-gray-50 rounded-xl border border-gray-200">
                        <!-- Course Thumbnail -->
                        <div class="flex-shrink-0">
                            <div class="w-24 h-24 rounded-lg overflow-hidden bg-gray-100">
                                @if($course->thumbnail)
                                    @if(str_starts_with($course->thumbnail, 'http'))
                                        <img src="{{ $course->thumbnail }}" class="w-full h-full object-cover" alt="{{ $course->name }}">
                                    @else
                                        <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-full object-cover" alt="{{ $course->name }}">
                                    @endif
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-lochmara-100">
                                        <span class="text-lochmara-600 font-bold text-lg">{{ substr($course->name, 0, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Course Details -->
                        <div class="flex-1 min-w-0">
                            <div class="space-y-2">
                                @if($course->category)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-lochmara-100 text-lochmara-700">
                                        {{ $course->category->name }}
                                    </span>
                                @endif
                                <h2 class="text-xl font-bold text-gray-900 truncate">{{ $course->name }}</h2>
                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $course->courseSections->sum(function($section) { return $section->sectionContents->count(); }) }} Lessons</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Lifetime Access</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- What's Next -->
                    <div class="mt-8 space-y-6">
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">What's Next?</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="text-center p-4">
                                    <div class="w-12 h-12 bg-lochmara-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-lochmara-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-9 4h10a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-medium text-gray-900">Start Learning</h4>
                                    <p class="text-sm text-gray-600 mt-1">Begin your first lesson</p>
                                </div>
                                <div class="text-center p-4">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-medium text-gray-900">Track Progress</h4>
                                    <p class="text-sm text-gray-600 mt-1">Monitor your learning</p>
                                </div>
                                <div class="text-center p-4">
                                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-medium text-gray-900">Get Certificate</h4>
                                    <p class="text-sm text-gray-600 mt-1">Complete and earn it</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6">
                            <a href="{{ route('dashboard.course.join', $course->slug) }}" 
                               class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-lochmara-600 text-white font-semibold rounded-lg hover:bg-lochmara-700 transition-colors duration-200 shadow-lg hover:shadow-xl cursor-pointer">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-9 4h10a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Start Learning Now
                            </a>
                            
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors duration-200 cursor-pointer">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                View My Courses
                            </a>
                        </div>
                        
                        <!-- Additional Info -->
                        <div class="text-center mt-8 p-4 bg-lochmara-50 rounded-lg border border-lochmara-200">
                            <p class="text-sm text-lochmara-700">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <strong>Lifetime Access:</strong> This course is now permanently available in your account. You can access it anytime from any device.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection