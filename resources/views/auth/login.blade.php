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
        </section>
       
    </main>
@endsection