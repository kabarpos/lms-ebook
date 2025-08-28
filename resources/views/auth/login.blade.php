@extends('front.layouts.app')
@section('title', 'Login - LMS EBook')

@section('content')
    <x-nav-guest/>
    <main class="min-h-screen flex items-center justify-center py-12 px-5">
        <section class="w-full max-w-lg">
            <form  method="POST" action="{{ route('login') }}" class="flex flex-col w-full rounded-[20px] border border-LMS-grey p-8 gap-5 bg-white shadow-lg">
                @csrf
                <h1 class="font-bold text-[22px] leading-[33px] mb-5 text-center" style="font-family: 'Manrope', sans-serif;">Welcome Back, <br>Let's Upgrade Skills</h1>
                <div class="flex flex-col gap-2">
                    <p style="font-family: 'Manrope', sans-serif;">Email Address</p>
                    <label class="relative group">
                        <input name="email" type="email" required
                            class="appearance-none outline-none w-full rounded-full border border-LMS-grey py-[14px] px-5 pl-12 font-semibold placeholder:font-normal placeholder:text-LMS-text-secondary group-focus-within:border-LMS-green transition-all duration-300"
                            placeholder="Type your valid email address">
                        <img src="{{ asset('assets/images/icons/sms.svg') }}"
                            class="absolute size-5 flex shrink-0 transform -translate-y-1/2 top-1/2 left-5"
                            alt="icon">
                    </label>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div class="flex flex-col gap-3">
                    <p style="font-family: 'Manrope', sans-serif;">Password</p>
                    <label class="relative group">
                        <input name="password" type="password" required
                            class="appearance-none outline-none w-full rounded-full border border-LMS-grey py-[14px] px-5 pl-12 font-semibold placeholder:font-normal placeholder:text-LMS-text-secondary group-focus-within:border-LMS-green transition-all duration-300"
                            placeholder="Type your password">
                        <img src="{{ asset('assets/images/icons/shield-security.svg') }}"
                            class="absolute size-5 flex shrink-0 transform -translate-y-1/2 top-1/2 left-5"
                            alt="icon">
                    </label>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    <a href="#" class="text-sm text-LMS-green hover:underline cursor-pointer">Forgot My Password</a>
                </div>
                <button type="submit"
                    class="flex items-center justify-center gap-[10px] rounded-full py-[14px] px-5 bg-LMS-green hover:drop-shadow-effect transition-all duration-300 cursor-pointer">
                    <span class="font-semibold text-white">Sign In to My Account</span>
                </button>
            </form>
            
            <!-- Resend Verification Section -->
            <div class="mt-6 p-6 bg-yellow-50 border border-yellow-200 rounded-[20px]" id="resend-section" style="display: none;">
                <h3 class="font-bold text-lg mb-4 text-yellow-800">Kirim Ulang Verifikasi</h3>
                <p class="text-sm text-yellow-700 mb-4">Jika Anda belum menerima link verifikasi di WhatsApp, masukkan email Anda di bawah ini untuk mengirim ulang.</p>
                
                <form method="POST" action="{{ route('verification.resend') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="relative group">
                            <input name="email" type="email" required
                                class="appearance-none outline-none w-full rounded-full border border-yellow-300 py-[12px] px-5 pl-12 font-semibold placeholder:font-normal placeholder:text-gray-500 group-focus-within:border-yellow-500 transition-all duration-300"
                                placeholder="Masukkan email Anda">
                            <img src="{{ asset('assets/images/icons/sms.svg') }}"
                                class="absolute size-5 flex shrink-0 transform -translate-y-1/2 top-1/2 left-5"
                                alt="icon">
                        </label>
                    </div>
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-[10px] rounded-full py-[12px] px-5 bg-yellow-600 hover:bg-yellow-700 transition-all duration-300 cursor-pointer">
                        <span class="font-semibold text-white">Kirim Ulang Verifikasi</span>
                    </button>
                </form>
            </div>
            
            <script>
                // Show resend section if there's a verification error
                document.addEventListener('DOMContentLoaded', function() {
                    const errorMessages = document.querySelectorAll('.text-red-600');
                    const resendSection = document.getElementById('resend-section');
                    
                    errorMessages.forEach(function(error) {
                        if (error.textContent.includes('belum terverifikasi') || error.textContent.includes('not verified')) {
                            resendSection.style.display = 'block';
                        }
                    });
                });
            </script>
        </section>
       
    </main>
@endsection