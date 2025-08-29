@extends('front.layouts.app')
@section('title', 'Register - LMS EBook')

@section('content')
    <x-nav-guest />
    <main class="min-h-screen flex items-center justify-center py-12 px-5">
        <section class="w-full max-w-lg">
            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data"
                class="flex flex-col w-full rounded-[20px] border border-LMS-grey p-8 gap-4 bg-white shadow-lg">
                @csrf
                <h1 class="font-bold text-[22px] leading-[33px] text-center mb-4" style="font-family: 'Manrope', sans-serif;">Upgrade Your Skills</h1>
                <div class="flex flex-col gap-2">
                    <p style="font-family: 'Manrope', sans-serif;">Complete Name</p>
                    <label class="relative group">
                        <input name="name" type="text" required
                            class="appearance-none outline-none w-full rounded-full border border-LMS-grey py-[14px] px-5 pl-12 font-semibold placeholder:font-normal placeholder:text-LMS-text-secondary group-focus-within:border-LMS-green transition-all duration-300"
                            placeholder="Type your complete name">
                        <img src="{{ asset('assets/images/icons/profile.svg') }}""
                            class="absolute size-5 flex shrink-0 transform -translate-y-1/2 top-1/2 left-5" alt="icon">
                    </label>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="flex flex-col gap-2">
                    <p style="font-family: 'Manrope', sans-serif;">WhatsApp Number</p>
                    <label class="relative group">
                        <input name="whatsapp_number" type="tel" required
                            class="appearance-none outline-none w-full rounded-full border border-LMS-grey py-[14px] px-5 pl-12 font-semibold placeholder:font-normal placeholder:text-LMS-text-secondary group-focus-within:border-LMS-green transition-all duration-300"
                            placeholder="e.g., +62812345678">
                        <svg class="absolute w-5 h-5 flex shrink-0 transform -translate-y-1/2 top-1/2 left-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </label>
                    <x-input-error :messages="$errors->get('whatsapp_number')" class="mt-2" />
                </div>
                <div class="flex flex-col gap-2">
                    <p style="font-family: 'Manrope', sans-serif;">Email Address</p>
                    <label class="relative group">
                        <input name="email" type="email" required
                            class="appearance-none outline-none w-full rounded-full border border-LMS-grey py-[14px] px-5 pl-12 font-semibold placeholder:font-normal placeholder:text-LMS-text-secondary group-focus-within:border-LMS-green transition-all duration-300"
                            placeholder="Type your valid email address">
                        <img src="{{ asset('assets/images/icons/sms.svg') }}""
                            class="absolute size-5 flex shrink-0 transform -translate-y-1/2 top-1/2 left-5" alt="icon">
                    </label>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div class="flex flex-col gap-2">
                    <p style="font-family: 'Manrope', sans-serif;">Password</p>
                    <label class="relative group">
                        <input name="password" type="password" required
                            class="appearance-none outline-none w-full rounded-full border border-LMS-grey py-[14px] px-5 pl-12 font-semibold placeholder:font-normal placeholder:text-LMS-text-secondary group-focus-within:border-LMS-green transition-all duration-300"
                            placeholder="Type your password">
                        <img src="{{ asset('assets/images/icons/shield-security.svg') }}""
                            class="absolute size-5 flex shrink-0 transform -translate-y-1/2 top-1/2 left-5" alt="icon">
                    </label>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div class="flex flex-col gap-2">
                    <p style="font-family: 'Manrope', sans-serif;">Confirm Password</p>
                    <label class="relative group">
                        <input name="password_confirmation" type="password" required
                            class="appearance-none outline-none w-full rounded-full border border-LMS-grey py-[14px] px-5 pl-12 font-semibold placeholder:font-normal placeholder:text-LMS-text-secondary group-focus-within:border-LMS-green transition-all duration-300"
                            placeholder="Confirm your password">
                        <img src="{{ asset('assets/images/icons/shield-security.svg') }}""
                            class="absolute size-5 flex shrink-0 transform -translate-y-1/2 top-1/2 left-5" alt="icon">
                    </label>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
                <button type="submit"
                    class="flex items-center justify-center gap-[10px] rounded-full py-[14px] px-5 bg-LMS-green hover:drop-shadow-effect transition-all duration-300 cursor-pointer">
                    <span class="font-semibold text-white">Create My Account</span>
                </button>
            </form>
            
            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600" style="font-family: 'Manrope', sans-serif;">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-LMS-green font-semibold hover:underline cursor-pointer">Masuk Sekarang</a>
                </p>
            </div>
        </section>
        
    </main>
@endsection
