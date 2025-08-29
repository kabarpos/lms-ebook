@extends('layouts.app')
@section('title', 'My Purchases')
@section('content')
    <x-navigation-auth />
    
    <!-- Breadcrumb -->
    <div id="path" class="flex w-full bg-white border-b border-gray-200 py-4">
        <div class="flex items-center w-full max-w-7xl px-4 sm:px-6 lg:px-8 mx-auto gap-3">
            <a href="{{ route('front.index') }}" class="text-gray-600 hover:text-lochmara-600 cursor-pointer">Home</a>
            <div class="h-4 w-px bg-gray-300"></div>
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-lochmara-600 cursor-pointer">Dashboard</a>
            <span class="text-gray-400">/</span>
            <span class="font-semibold text-gray-900">My Purchases</span>
        </div>
    </div>

    <main class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">My Course Purchases</h1>
                <p class="text-gray-600 mt-2">View your purchased courses and transaction history</p>
            </div>

            @if($purchases->count() > 0)
                <!-- Purchase History -->
                <div class="space-y-6">
                    @foreach($purchases as $purchase)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4">
                                            <!-- Course Thumbnail -->
                                            <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                                @if($purchase->course->thumbnail)
                                                    @if(str_starts_with($purchase->course->thumbnail, 'http'))
                                                        <img src="{{ $purchase->course->thumbnail }}" class="w-full h-full object-cover" alt="{{ $purchase->course->name }}">
                                                    @else
                                                        <img src="{{ Storage::url($purchase->course->thumbnail) }}" class="w-full h-full object-cover" alt="{{ $purchase->course->name }}">
                                                    @endif
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-lochmara-100">
                                                        <span class="text-lochmara-600 font-bold text-lg">{{ substr($purchase->course->name, 0, 2) }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Course Info -->
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                                    {{ $purchase->course->name }}
                                                </h3>
                                                @if($purchase->course->category)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-lochmara-100 text-lochmara-700 mb-2">
                                                        {{ $purchase->course->category->name }}
                                                    </span>
                                                @endif
                                                <p class="text-sm text-gray-600 mb-3">{{ Str::limit($purchase->course->about, 120) }}</p>
                                                
                                                <!-- Transaction Details -->
                                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                    <span>Transaction ID: {{ $purchase->booking_trx_id }}</span>
                                                    <span>•</span>
                                                    <span>Purchased: {{ $purchase->created_at->format('M d, Y') }}</span>
                                                    @if($purchase->payment_type)
                                                        <span>•</span>
                                                        <span>Payment: {{ $purchase->payment_type }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price and Actions -->
                                    <div class="text-right ml-6">
                                        <div class="text-2xl font-bold text-lochmara-600 mb-2">
                                            Rp {{ number_format($purchase->grand_total_amount, 0, '', '.') }}
                                        </div>
                                        
                                        <div class="space-y-2">
                                            <!-- Access Course Button -->
                                            <a href="{{ route('front.course.details', $purchase->course->slug) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-lochmara-600 text-white text-sm font-medium rounded-lg hover:bg-lochmara-700 transition-colors duration-200 cursor-pointer">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                                Access Course
                                            </a>
                                            
                                            <!-- Status Badge -->
                                            @if($purchase->is_paid)
                                                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Paid
                                                </div>
                                            @else
                                                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Pending
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Summary Stats -->
                <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Purchase Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-lochmara-600">{{ $purchases->count() }}</div>
                            <div class="text-sm text-gray-600">Total Courses</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $purchases->where('is_paid', true)->count() }}</div>
                            <div class="text-sm text-gray-600">Completed Purchases</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-lochmara-600">
                                Rp {{ number_format($purchases->where('is_paid', true)->sum('grand_total_amount'), 0, '', '.') }}
                            </div>
                            <div class="text-sm text-gray-600">Total Spent</div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Purchases Yet</h3>
                    <p class="text-gray-600 mb-6">You haven't purchased any courses yet. Browse our course catalog to get started!</p>
                    <a href="{{ route('front.pricing') }}" 
                       class="inline-flex items-center px-6 py-3 bg-lochmara-600 text-white font-medium rounded-lg hover:bg-lochmara-700 transition-colors duration-200 cursor-pointer">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Browse Courses
                    </a>
                </div>
            @endif
        </div>
    </main>
@endsection