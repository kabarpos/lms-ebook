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
                                        <div class="text-right">
                                            @if($course->original_price && $course->original_price > $course->price)
                                                <!-- Original Price (Strikethrough) -->
                                                <div class="text-sm text-gray-500 line-through">
                                                    Rp {{ number_format($course->original_price, 0, '', '.') }}
                                                </div>
                                                <!-- Current Price with Discount Badge -->
                                                <div class="flex items-center justify-end space-x-2">
                                                    <span class="font-bold text-lg">Rp {{ number_format($course->price, 0, '', '.') }}</span>
                                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded">
                                                        {{ round((($course->original_price - $course->price) / $course->original_price) * 100) }}% OFF
                                                    </span>
                                                </div>
                                            @else
                                                <!-- Regular Price -->
                                                <span class="font-bold text-lg">Rp {{ number_format($course->price, 0, '', '.') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if(isset($admin_fee_amount) && $admin_fee_amount > 0)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Biaya Admin</span>
                                        </div>
                                        <span class="font-semibold">Rp {{ number_format($admin_fee_amount, 0, '', '.') }}</span>
                                    </div>
                                    @endif
                                    
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
                                
                                <!-- Discount Code Section -->
                                <div class="space-y-4">
                                    <div class="border-t border-gray-200 pt-4">
                                        <h3 class="text-sm font-medium text-gray-900 mb-3">Kode Diskon</h3>
                                        <div class="flex space-x-2 {{ isset($appliedDiscount) ? 'hidden' : '' }}" id="discount-input-section">
                                            <div class="flex-1">
                                                <input type="text" 
                                                       id="discount-code" 
                                                       name="discount_code"
                                                       placeholder="Masukkan kode diskon" 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lochmara-500 focus:border-lochmara-500 text-sm cursor-pointer"
                                                       autocomplete="off">
                                            </div>
                                            <button type="button" 
                                                    id="apply-discount" 
                                                    class="px-4 py-2 bg-lochmara-600 text-white text-sm font-medium rounded-lg hover:bg-lochmara-700 transition-colors duration-200 cursor-pointer">
                                                Terapkan
                                            </button>
                                        </div>
                                        
                                        <!-- Discount Message -->
                                        <div id="discount-message" class="mt-2 text-sm hidden"></div>
                                        
                                        <!-- Applied Discount Display -->
                                        <div id="applied-discount" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg {{ isset($appliedDiscount) ? '' : 'hidden' }}">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-green-700 font-medium" id="discount-name">
                                                        @if(isset($appliedDiscount))
                                                            {{ $appliedDiscount['name'] }} ({{ $appliedDiscount['code'] }})
                                                        @endif
                                                    </span>
                                                </div>
                                                <button type="button" 
                                                        id="remove-discount" 
                                                        class="text-green-600 hover:text-green-800 cursor-pointer">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="mt-1 text-sm text-green-600" id="discount-details">
                                                @if(isset($appliedDiscount) && isset($discount_amount))
                                                    Hemat Rp {{ number_format($discount_amount, 0, ',', '.') }}
                                                    @if($appliedDiscount['type'] === 'percentage')
                                                        ({{ $appliedDiscount['value'] }}% off)
                                                    @else
                                                        (diskon tetap)
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr class="border-gray-200">
                                
                                <!-- Price Breakdown -->
                                <div class="space-y-3">
                                    <!-- Subtotal -->
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Subtotal</span>
                                        <span id="subtotal-amount">Rp {{ number_format($sub_total_amount, 0, '', '.') }}</span>
                                    </div>
                                    
                                    <!-- Discount Amount (show if discount exists) -->
                                    <div id="discount-amount-row" class="flex items-center justify-between text-sm text-green-600 {{ isset($discount_amount) && $discount_amount > 0 ? '' : 'hidden' }}">
                                        <span>Diskon</span>
                                        <span id="discount-amount">
                                            @if(isset($discount_amount) && $discount_amount > 0)
                                                -Rp {{ number_format($discount_amount, 0, ',', '.') }}
                                            @else
                                                -Rp 0
                                            @endif
                                        </span>
                                    </div>
                                    
                                    @if(isset($admin_fee_amount) && $admin_fee_amount > 0)
                                    <!-- Admin Fee -->
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Biaya Admin</span>
                                        <span>Rp {{ number_format($admin_fee_amount, 0, '', '.') }}</span>
                                    </div>
                                    @endif
                                </div>
                                
                                <hr class="border-gray-200">
                                
                                <div class="flex items-center justify-between p-4 bg-lochmara-50 rounded-lg border border-lochmara-200">
                                    <span class="text-lg font-bold text-lochmara-800">Total Payment</span>
                                    <span class="text-2xl font-bold text-lochmara-800" id="total-payment">Rp {{ number_format($grand_total_amount, 0, '', '.') }}</span>
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
                                    <x-lazy-image 
                                        src="{{ $course->thumbnail }}" 
                                        alt="{{ $course->name }}" 
                                        class="w-full h-full object-cover"
                                        loading="lazy" />
                                @else
                                    <x-lazy-image 
                                        src="{{ Storage::disk('public')->url($course->thumbnail) }}" 
                                        alt="{{ $course->name }}" 
                                        class="w-full h-full object-cover"
                                        loading="lazy" />
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
        // Global variables for discount management
        let appliedDiscount = null;
        let originalPricing = {
            subtotal: {{ $sub_total_amount }},
            adminFee: {{ $admin_fee_amount ?? 0 }},
            grandTotal: {{ $grand_total_amount }}
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            const payButton = document.getElementById('pay-button');
            const applyDiscountBtn = document.getElementById('apply-discount');
            const discountCodeInput = document.getElementById('discount-code');
            const removeDiscountBtn = document.getElementById('remove-discount');
            
            if (payButton) {
                payButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    handlePayment();
                });
            }
            
            // Discount code validation
            if (applyDiscountBtn && discountCodeInput) {
                applyDiscountBtn.addEventListener('click', function() {
                    validateDiscountCode();
                });
                
                discountCodeInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        validateDiscountCode();
                    }
                });
            }
            
            // Remove discount
            if (removeDiscountBtn) {
                removeDiscountBtn.addEventListener('click', function() {
                    removeDiscount();
                });
            }
        });
        
        function validateDiscountCode() {
            const discountCodeInput = document.getElementById('discount-code');
            const applyBtn = document.getElementById('apply-discount');
            const messageDiv = document.getElementById('discount-message');
            
            const discountCode = discountCodeInput.value.trim();
            
            if (!discountCode) {
                showDiscountMessage('Silakan masukkan kode diskon.', 'error');
                return;
            }
            
            // Show loading state
            applyBtn.disabled = true;
            applyBtn.textContent = 'Memvalidasi...';
            hideDiscountMessage();
            
            // Get CSRF token
            const tokenInput = document.querySelector('input[name="_token"]');
            if (!tokenInput) {
                showDiscountMessage('Session expired. Please refresh the page.', 'error');
                resetApplyButton();
                return;
            }
            
            // Make validation request
            fetch('{{ route('front.course.validate-discount', $course->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': tokenInput.value
                },
                body: JSON.stringify({
                    discount_code: discountCode
                })
            })
            .then(response => response.json())
            .then(data => {
                resetApplyButton();
                
                if (data.success) {
                    // Apply discount
                    appliedDiscount = data.discount;
                    updatePricingDisplay(data.pricing, data.formatted);
                    showAppliedDiscount(data.discount, data.formatted.savings);
                    showDiscountMessage(data.message, 'success');
                    
                    // Clear input and hide apply section
                    discountCodeInput.value = '';
                    const discountInputSection = document.getElementById('discount-input-section');
                    if (discountInputSection) {
                        discountInputSection.classList.add('hidden');
                    }
                } else {
                    showDiscountMessage(data.message, 'error');
                }
            })
            .catch(error => {
                resetApplyButton();
                showDiscountMessage('Terjadi kesalahan. Silakan coba lagi.', 'error');
            });
        }
        
        function removeDiscount() {
            // Get CSRF token
            const tokenInput = document.querySelector('input[name="_token"]');
            if (!tokenInput) {
                showDiscountMessage('Session expired. Please refresh the page.', 'error');
                return;
            }
            
            // Make remove discount request
            fetch('{{ route('front.course.remove-discount', $course->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': tokenInput.value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    appliedDiscount = null;
                    
                    // Update pricing display with server response
                    updatePricingDisplay(data.pricing, data.formatted);
                    
                    // Hide applied discount and show input again
                    hideAppliedDiscount();
                    showDiscountInput();
                    hideDiscountMessage();
                } else {
                    showDiscountMessage(data.message, 'error');
                }
            })
            .catch(error => {
                showDiscountMessage('Terjadi kesalahan. Silakan coba lagi.', 'error');
            });
        }
        
        function updatePricingDisplay(pricing, formatted) {
            // Update subtotal
            const subtotalElement = document.getElementById('subtotal-amount');
            if (subtotalElement) {
                subtotalElement.textContent = formatted.subtotal;
            }
            
            // Show/hide discount amount
            const discountRow = document.getElementById('discount-amount-row');
            const discountAmount = document.getElementById('discount-amount');
            
            if (pricing.discount_amount > 0) {
                discountRow.classList.remove('hidden');
                discountAmount.textContent = '-' + formatted.discount_amount;
            } else {
                discountRow.classList.add('hidden');
            }
            
            // Update total payment
            const totalPayment = document.getElementById('total-payment');
            if (totalPayment) {
                totalPayment.textContent = formatted.grand_total;
            }
        }
        
        function showAppliedDiscount(discount, savings) {
            const appliedDiscountDiv = document.getElementById('applied-discount');
            const discountName = document.getElementById('discount-name');
            const discountDetails = document.getElementById('discount-details');
            
            if (appliedDiscountDiv && discountName && discountDetails) {
                discountName.textContent = discount.name + ' (' + discount.code + ')';
                
                let detailText = 'Hemat ' + savings;
                if (discount.type === 'percentage') {
                    detailText += ' (' + discount.value + '% off)';
                } else {
                    detailText += ' (diskon tetap)';
                }
                discountDetails.textContent = detailText;
                
                appliedDiscountDiv.classList.remove('hidden');
            }
        }
        
        function hideAppliedDiscount() {
            const appliedDiscountDiv = document.getElementById('applied-discount');
            if (appliedDiscountDiv) {
                appliedDiscountDiv.classList.add('hidden');
            }
        }
        
        function showDiscountInput() {
            const discountInputSection = document.getElementById('discount-input-section');
            
            if (discountInputSection) {
                discountInputSection.classList.remove('hidden');
            }
        }
        
        function showDiscountMessage(message, type) {
            const messageDiv = document.getElementById('discount-message');
            if (messageDiv) {
                messageDiv.textContent = message;
                messageDiv.className = 'mt-2 text-sm ' + (type === 'error' ? 'text-red-600' : 'text-green-600');
                messageDiv.classList.remove('hidden');
            }
        }
        
        function hideDiscountMessage() {
            const messageDiv = document.getElementById('discount-message');
            if (messageDiv) {
                messageDiv.classList.add('hidden');
            }
        }
        
        function resetApplyButton() {
            const applyBtn = document.getElementById('apply-discount');
            if (applyBtn) {
                applyBtn.disabled = false;
                applyBtn.textContent = 'Terapkan';
            }
        }
        
        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }
        
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