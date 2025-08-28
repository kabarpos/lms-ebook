@extends('front.layouts.app')
@section('title', 'Success Checkout - LMS DripCourse')
@section('content')
    <x-navigation-auth />

    <main class="flex justify-center py-5 flex-1 items-center">
        <div class="w-[500px] flex flex-col gap-[30px]">
            <div class="flex flex-col gap-[10px]">
                <div class="rounded-full !w-fit mx-auto py-2 px-[14px] bg-LMS-light-green flex items-center gap-[6px]">
                    <img src="{{ asset('assets/images/icons/crown-green.svg') }}" alt="icon" class="size-[20px] shrink-0" />
                    <p class="font-bold text-sm leading-[21px]">PRO UNLOCKED</p>
                </div>
                <h1 class="font-bold text-[28px] leading-[42px] text-center">Payment Successful</h1>
                <p class="text-center leading-[28px] text-LMS-text-secondary">Anda telah memiliki akses kelas materi
                    terbaru sebagai persiapan bekerja di era digital industri saat ini, yay!</p>
            </div>
            <section id="card"
                class="relative rounded-[20px] border border-LMS-grey p-[10px] flex items-center gap-4 bg-white">
                <div class="flex items-center justify-center rounded-[14px] overflow-hidden w-[180px] h-[130px]">
                    <img src="{{ asset('assets/images/thumbnails/succes-checkout.png') }}" alt="image"
                        class="w-full h-full object-cover" />
                </div>
                <div class="flex flex-col gap-[10px]">
                    <h2 class="font-bold">
                        Subscription Active: <br />
                        {{ $pricing->name }}
                    </h2>
                    <div class="flex items-center gap-[6px]">
                        <img src="{{ asset('assets/images/icons/calendar-green.svg') }}" alt="icon" class="size-[20px] shrink-0" />
                        <p class="text-LMS-text-secondary text-sm leading-[21px]">{{ $pricing->duration }} Months Access</p>
                    </div>
                    <div class="flex items-center gap-[6px]">
                        <img src="{{ asset('assets/images/icons/briefcase-green.svg') }}" alt="icon" class="size-[20px] shrink-0" />
                        <p class="text-LMS-text-secondary text-sm leading-[21px]">Job-Ready Skills</p>
                    </div>
                </div>
                <img src="{{ asset('assets/images/icons/cup-green-fill.svg') }}" alt="icon"
                    class="absolute top-1/2 right-0 size-[50px] shrink-0 -translate-y-1/2 translate-x-1/2" />
            </section>
            <div class="flex items-center gap-[14px] mx-auto">
                <a href="{{ route('dashboard.subscriptions') }}" class="cursor-pointer">
                    <div
                        class="flex items-center px-5 justify-center border border-LMS-grey rounded-full py-[10px] bg-white hover:border-LMS-green transition-all duration-300">
                        <p class="font-semibold">My Transactions</p>
                    </div>
                </a>
                <a href="{{ route('dashboard') }}" class="cursor-pointer">
                    <div
                        class="flex items-center px-5 justify-center text-white rounded-full py-[10px] bg-LMS-green hover:drop-shadow-effect transition-all duration-300">
                        <p class="font-semibold">Start Learning</p>
                    </div>
                </a>
            </div>
        </div>
    </main>


@endsection
