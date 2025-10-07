@extends('front.layouts.app')
@section('title', 'Login - LMS EBook')

@section('content')
    <x-nav-guest/>
    <main class="min-h-screen flex items-center justify-center py-12 px-5">
        <section class="w-full max-w-lg">
            @if(session('success') || session('warning') || session('error'))
                <div class="mb-4">
                    @if(session('success'))
                        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('warning'))
                        <div class="rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-yellow-800">
                            {{ session('warning') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            @endif

            <!-- Rate Limit Modal -->
            <div id="rateLimitModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
                <div class="w-full max-w-md rounded-2xl bg-white shadow-xl">
                    <div class="px-6 pt-6">
                        <h3 class="text-lg font-bold text-gray-900">Terlalu Banyak Percobaan Login</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            @php($blockedSeconds = session('blocked_seconds'))
                            {{ session('error') ?? 'Akun Anda sementara diblokir karena terlalu banyak percobaan login yang gagal.' }}
                            @if($blockedSeconds)
                                <br>Silakan coba lagi dalam sekitar {{ ceil($blockedSeconds/60) }} menit.
                            @endif
                        </p>
                    </div>
                    <div class="px-6 pb-6 mt-4 flex gap-3">
                        <button type="button" id="rateLimitModalClose" class="flex-1 rounded-full bg-LMS-green px-5 py-3 text-white font-semibold">Mengerti</button>
                        <a href="{{ route('password.reset.options') }}" class="flex-1 rounded-full border border-LMS-green px-5 py-3 text-LMS-green font-semibold text-center">Lupa Password?</a>
                    </div>
                </div>
            </div>
            <form  method="POST" action="{{ route('login') }}" class="flex flex-col w-full rounded-[20px] border border-LMS-grey p-8 gap-5 bg-white shadow-lg">
                @csrf
                <h1 class="font-bold text-[22px] leading-[33px] mb-5 text-center form-title">Welcome Back, <br>Let's Upgrade Skills</h1>
                <div class="flex flex-col gap-2">
                    <p class="form-label">Email Address</p>
                    <label class="relative group">
                        <input name="email" type="email" required
                            class="appearance-none outline-none w-full rounded-full border border-LMS-grey py-[14px] px-5 pl-12 font-semibold placeholder:font-normal placeholder:text-LMS-text-secondary group-focus-within:border-LMS-green transition-all duration-300"
                            placeholder="Type your valid email address">
                        <x-lazy-image 
                            src="{{ asset('assets/images/icons/sms.svg') }}"
                            alt="email icon"
                            class="absolute size-5 flex shrink-0 transform -translate-y-1/2 top-1/2 left-5"
                            loading="eager" />
                    </label>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div class="flex flex-col gap-3">
                    <p class="form-label">Password</p>
                    <label class="relative group">
                        <input name="password" type="password" required
                            class="appearance-none outline-none w-full rounded-full border border-LMS-grey py-[14px] px-5 pl-12 font-semibold placeholder:font-normal placeholder:text-LMS-text-secondary group-focus-within:border-LMS-green transition-all duration-300"
                            placeholder="Type your password">
                        <x-lazy-image 
                            src="{{ asset('assets/images/icons/shield-security.svg') }}"
                            alt="password icon"
                            class="absolute size-5 flex shrink-0 transform -translate-y-1/2 top-1/2 left-5"
                            loading="eager" />
                    </label>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    <a href="{{ route('password.reset.options') }}" class="text-sm text-LMS-green hover:underline cursor-pointer">Forgot My Password</a>
                </div>
                <button type="submit"
                    class="flex items-center justify-center gap-[10px] rounded-full py-[14px] px-5 bg-LMS-green hover:drop-shadow-effect transition-all duration-300 cursor-pointer">
                    <span class="font-semibold text-white">Sign In to My Account</span>
                </button>
            </form>
            
            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 form-text">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="text-LMS-green font-semibold hover:underline cursor-pointer">Daftar Sekarang</a>
                </p>
            </div>
            
            <!-- Resend Verification Section -->
            <div class="mt-6 p-6 bg-yellow-50 border border-yellow-200 rounded-[20px] resend-section-hidden" id="resend-section">
                <h3 class="font-bold text-lg mb-4 text-yellow-800">Kirim Ulang Verifikasi</h3>
                <p class="text-sm text-yellow-700 mb-4">Jika Anda belum menerima link verifikasi di WhatsApp, masukkan email Anda di bawah ini untuk mengirim ulang.</p>
                
                <form method="POST" action="{{ route('whatsapp.verification.resend') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="relative group">
                            <input name="email" type="email" required
                                class="appearance-none outline-none w-full rounded-full border border-yellow-300 py-[12px] px-5 pl-12 font-semibold placeholder:font-normal placeholder:text-gray-500 group-focus-within:border-yellow-500 transition-all duration-300"
                                placeholder="Masukkan email Anda">
                            <x-lazy-image src="{{ asset('assets/images/icons/sms.svg') }}"
                                class="absolute size-5 flex shrink-0 transform -translate-y-1/2 top-1/2 left-5"
                                alt="icon" loading="eager" />
                        </label>
                    </div>
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-[10px] rounded-full py-[12px] px-5 bg-yellow-600 hover:bg-yellow-700 transition-all duration-300 cursor-pointer">
                        <span class="font-semibold text-white">Kirim Ulang Verifikasi</span>
                    </button>
                </form>
            </div>
            
            <script nonce="{{ request()->attributes->get('csp_nonce') }}">
                // Show resend section if there's a verification error
                document.addEventListener('DOMContentLoaded', function() {
                    const errorMessages = document.querySelectorAll('.text-red-600');
                    const resendSection = document.getElementById('resend-section');
                    
                    errorMessages.forEach(function(error) {
                        if (error.textContent.includes('belum terverifikasi') || error.textContent.includes('not verified')) {
                            resendSection.style.display = 'block';
                        }
                    });

                    // Show rate limit modal if blocked
                    const isRateLimited = {{ session('rate_limit_blocked') ? 'true' : 'false' }};
                    if (isRateLimited) {
                        const modal = document.getElementById('rateLimitModal');
                        const closeBtn = document.getElementById('rateLimitModalClose');
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        closeBtn.addEventListener('click', function() {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        });
                    }
                });
            </script>
        </section>
       
    </main>
@endsection