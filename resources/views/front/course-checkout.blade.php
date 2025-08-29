@extends('front.layouts.app')
@section('title', 'Course Checkout - ' . $course->name)
@section('content')
    <x-navigation-auth />
    
    <!-- Breadcrumb -->
    <div id="path" class="flex w-full bg-white border-b border-gray-200 py-4">
        <div class="flex items-center w-full max-w-7xl px-4 sm:px-6 lg:px-8 mx-auto gap-3">
            <a href="{{ route('front.index') }}" class="text-gray-600 hover:text-lochmara-600 cursor-pointer">Home</a>
            <div class="h-4 w-px bg-gray-300"></div>
            <a href="{{ route('front.course.details', $course->slug) }}" class="text-gray-600 hover:text-lochmara-600 cursor-pointer">{{ $course->name }}</a>
            <span class="text-gray-400">/</span>
            <span class="font-semibold text-gray-900">Checkout</span>
        </div>
    </div>

    <main class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Checkout Form -->
                <div class="space-y-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                        <form id="checkout-details" class="space-y-6">
                            @csrf
                            <input type="hidden" name="payment_method" value="Midtrans">
                            
                            <div class="border-b border-gray-200 pb-6">
                                <h1 class="text-2xl font-bold text-gray-900">Course Purchase</h1>
                                <p class="text-gray-600 mt-2">Complete your purchase to get lifetime access to this course</p>
                            </div>
                            

                            
                            <!-- Order Summary -->
                            <section class="space-y-4">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-5 h-5 text-lochmara-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="font-medium">Course Purchase</span>
                                        </div>
                                        <span class="font-bold text-lg">Rp {{ number_format($course->price, 0, '', '.') }}</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>PPN 11%</span>
                                        </div>
                                        <span class="font-semibold">Rp {{ number_format($total_tax_amount, 0, '', '.') }}</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 2L3 7v11c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V7l-7-5zM8 18v-6h4v6H8z"/>
                                            </svg>
                                            <span>Access Duration</span>
                                        </div>
                                        <span class="font-semibold text-green-600">Lifetime</span>
                                    </div>
                                </div>
                                
                                <hr class="border-gray-200">
                                
                                <div class="flex items-center justify-between p-4 bg-lochmara-50 rounded-lg border border-lochmara-200">
                                    <span class="text-lg font-bold text-lochmara-800">Total Payment</span>
                                    <span class="text-2xl font-bold text-lochmara-800">Rp {{ number_format($grand_total_amount, 0, '', '.') }}</span>
                                </div>
                            </section>
                            
                            <!-- Payment Button -->
                            <button type="button" id="pay-button" 
                                    class="w-full py-4 bg-lochmara-600 text-white font-bold rounded-lg hover:bg-lochmara-700 transition-colors duration-200 shadow-lg hover:shadow-xl cursor-pointer">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    <span>Pay Now</span>
                                </div>
                            </button>
                            
                            <hr class="border-gray-200">
                            
                            <p class="text-sm text-gray-500 text-center">
                                By purchasing this course, you agree to our 
                                <a href="{{ route('front.terms-of-service') }}" class="text-lochmara-600 hover:underline cursor-pointer">Terms & Conditions</a>
                            </p>
                        </form>
                    </div>
                </div>
                
                <!-- Course Preview -->
                <div class="space-y-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Course Image -->
                        <div class="aspect-video bg-gray-100">
                            @if($course->thumbnail)
                                @if(str_starts_with($course->thumbnail, 'http'))
                                    <img src="{{ $course->thumbnail }}" class="w-full h-full object-cover" alt="{{ $course->name }}">
                                @else
                                    <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-full object-cover" alt="{{ $course->name }}">
                                @endif
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-lochmara-100">
                                    <div class="text-center">
                                        <div class="text-lochmara-600 font-bold text-3xl mb-2">{{ substr($course->name, 0, 2) }}</div>
                                        <div class="text-lochmara-500 text-sm">Course Preview</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Course Info -->
                        <div class="p-6 space-y-6">
                            <!-- Course Title and Category -->
                            <div class="space-y-3">
                                @if($course->category)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-lochmara-100 text-lochmara-700">
                                        {{ $course->category->name }}
                                    </span>
                                @endif
                                <h2 class="text-xl font-bold text-gray-900">{{ $course->name }}</h2>
                                <p class="text-gray-600 leading-relaxed">{{ $course->about }}</p>
                            </div>
                            
                            <!-- Course Stats -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <div class="text-2xl font-bold text-lochmara-600">{{ $course->courseSections->sum(function($section) { return $section->sectionContents->count(); }) }}</div>
                                    <div class="text-sm text-gray-600">Total Lessons</div>
                                </div>
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <div class="text-2xl font-bold text-lochmara-600">{{ $course->courseStudents->count() }}</div>
                                    <div class="text-sm text-gray-600">Students Enrolled</div>
                                </div>
                            </div>
                            
                            <!-- What You Get -->
                            <div class="space-y-4">
                                <h3 class="font-semibold text-gray-900">What You'll Get:</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700">Lifetime access to all course content</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700">Certificate of completion</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700">Access from any device</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700">Learn at your own pace</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <!-- Midtrans Snap JS -->
    <script type="text/javascript" 
            src="https://app.sandbox.midtrans.com/snap/snap.js" 
            data-client-key="{{ $midtrans_client_key ?? config('midtrans.clientKey') }}"></script>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const payButton = document.getElementById('pay-button');
            
            if (payButton) {
                payButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    handlePayment();
                });
            }
        });
        
        function handlePayment() {
            const payButton = document.getElementById('pay-button');
            
            // Show loading state
            payButton.disabled = true;
            payButton.innerHTML = '<div class="flex items-center justify-center space-x-2"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div><span>Processing...</span></div>';
            
            // Get CSRF token
            const tokenInput = document.querySelector('input[name="_token"]');
            if (!tokenInput) {
                alert('Session expired. Please refresh the page.');
                resetButton();
                return;
            }
            
            // Make payment request
            fetch('{{ route('front.payment_store_courses_midtrans') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': tokenInput.value
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                resetButton();
                
                if (data.snap_token) {
                    if (typeof snap === 'undefined') {
                        alert('Payment system not ready. Please refresh the page.');
                        return;
                    }
                    
                    // Open Midtrans payment popup
                    snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            window.location.href = "{{ route('front.checkout.success') }}";
                        },
                        onPending: function(result) {
                            alert('Payment is pending. Please complete your payment.');
                            window.location.href = "{{ route('front.course.details', $course->slug) }}";
                        },
                        onError: function(result) {
                            alert('Payment failed. Please try again.');
                            window.location.href = "{{ route('front.course.details', $course->slug) }}";
                        },
                        onClose: function() {
                            // User closed popup without completing payment
                        }
                    });
                } else {
                    alert('Error: ' + (data.error || 'Unable to process payment'));
                }
            })
            .catch(error => {
                resetButton();
                alert('Network error. Please try again.');
            });
        }
        
        function resetButton() {
            const payButton = document.getElementById('pay-button');
            if (payButton) {
                payButton.disabled = false;
                payButton.innerHTML = '<div class="flex items-center justify-center space-x-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 003 3v8a3 3 0 003 3z"/></svg><span>Pay Now</span></div>';
            }
        }
    </script>
@endpush