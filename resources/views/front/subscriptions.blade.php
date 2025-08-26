@extends('front.layouts.app')
@section('title', 'My Subscriptions - Obito BuildWithAngga')
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
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:border-lochmara-300 hover:text-lochmara-600 transition-all duration-200 whitespace-nowrap">
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
    <main class="min-h-screen bg-gray-50 relative">
        <!-- Background Pattern (Hidden on mobile) -->
        <div class="absolute inset-y-0 right-0 w-1/2 bg-gradient-to-l from-lochmara-50 to-transparent hidden lg:block">
            <div class="absolute inset-0 opacity-10">
                <img src="{{ asset('assets/images/backgrounds/banner-subscription.png') }}" 
                     class="w-full h-full object-cover" 
                     alt="background pattern">
            </div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">My Subscriptions</h1>
                <p class="text-lg text-gray-600 mt-2">Manage your active and expired subscriptions</p>
            </div>
            
            <!-- Subscriptions List -->
            <div class="max-w-4xl space-y-6">
                @forelse($transactions as $transaction)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-200">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0 lg:space-x-6">
                            <!-- Subscription Info -->
                            <div class="flex items-center space-x-4 flex-1">
                                <div class="w-12 h-12 bg-lochmara-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-lochmara-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $transaction->pricing->name }}</h3>
                                    <p class="text-gray-600">{{ $transaction->pricing->duration }} months duration</p>
                                </div>
                            </div>
                            
                            <!-- Subscription Details -->
                            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                                <!-- Price -->
                                <div class="text-center lg:text-left">
                                    <div class="flex items-center justify-center lg:justify-start space-x-1 text-gray-500 mb-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                        <span class="text-sm">Price</span>
                                    </div>
                                    <p class="font-semibold text-gray-900">
                                        Rp {{ number_format($transaction->sub_total_amount, 0, '', '.') }}
                                    </p>
                                </div>
                                
                                <!-- Start Date -->
                                <div class="text-center lg:text-left">
                                    <div class="flex items-center justify-center lg:justify-start space-x-1 text-gray-500 mb-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-sm">Started</span>
                                    </div>
                                    <p class="font-semibold text-gray-900">{{ $transaction->started_at->format('d M, Y') }}</p>
                                </div>
                                
                                <!-- Status -->
                                <div class="col-span-2 lg:col-span-1 text-center lg:text-left">
                                    <div class="flex items-center justify-center lg:justify-start space-x-1 text-gray-500 mb-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-sm">Status</span>
                                    </div>
                                    @if($transaction->isActive())
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            ACTIVE
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            EXPIRED
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Action Button -->
                            <div class="flex justify-center lg:justify-end">
                                <a href="{{ route('dashboard.subscription.details', $transaction) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:border-lochmara-300 hover:text-lochmara-600 transition-all duration-200">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Subscriptions Found</h3>
                        <p class="text-gray-600 mb-6">You haven't purchased any subscription packages yet.</p>
                        <a href="{{ route('front.pricing') }}" 
                           class="inline-flex items-center px-6 py-3 bg-lochmara-600 text-white font-semibold rounded-lg hover:bg-lochmara-700 transition-colors duration-200">
                            View Pricing Plans
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </main>
@endsection
