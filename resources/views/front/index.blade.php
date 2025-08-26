@extends('front.layouts.app')
@section('title', 'Obito BuildWithAngga')
@section('content')
    <x-nav-guest />
        <main class="flex flex-1 items-center py-[70px]">
            <div class="w-full flex gap-[77px] justify-between items-center pl-[calc(((100%-1280px)/2)+75px)]">
                <div class="flex flex-col max-w-[500px] gap-[50px]">
                    <div class="flex flex-col gap-[30px]">
                        <p class="flex items-center gap-[6px] w-fit rounded-full py-2 px-[14px] bg-obito-light-green">
                            <img src="{{ asset('assets/images/icons/crown-green.svg') }}" class="flex shrink-0 w-5" alt="icon">
                            <span class="font-bold text-sm">TRUSTED BY 500 FORTUNE ANGGA COMPANIES</span>
                        </p>
                        <div>
                            <h1 class="font-extrabold text-[50px] leading-[65px]">Tingkatkan Skills, <br>Get Higher Salary</h1>
                            <p class="leading-7 mt-[10px] text-obito-text-secondary">Materi terbaru disusun oleh professional dan perusahaan besar agar lebih sesuai kebutuhan dan anda.</p>
                        </div>
                        <div class="flex items-center gap-[18px]">
                            <a href="{{ route('register') }}" class="flex items-center rounded-full h-[67px] py-5 px-[30px] gap-[10px] bg-obito-green hover:drop-shadow-effect transition-all duration-300">
                                <span class="text-white font-semibold text-lg">Get Started</span>
                            </a>
                            <a href="{{ route('front.pricing') }}" class="flex items-center rounded-full h-[67px] border border-obito-grey py-5 px-[30px] bg-white gap-[10px] hover:border-obito-green transition-all duration-300">
                                <img src="{{ asset('assets/images/icons/play-circle-fill.svg') }}" class="size-8 flex shrink-0" alt="icon">
                                <span class="font-semibold text-lg">View Pricing</span>
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center gap-[14px]">
                        <img src="{{ asset('assets/images/photos/group.png') }}" class="flex shrink-0 h-[50px]" alt="group photo">
                        <div>
                            <div class="flex gap-1 items-center">
                                <div class="flex">
                                    <img src="{{ asset('assets/images/icons/Star 1.svg') }}" class="flex shrink-0 w-5" alt="star">
                                    <img src="{{ asset('assets/images/icons/Star 1.svg') }}" class="flex shrink-0 w-5" alt="star">
                                    <img src="{{ asset('assets/images/icons/Star 1.svg') }}" class="flex shrink-0 w-5" alt="star">
                                    <img src="{{ asset('assets/images/icons/Star 1.svg') }}" class="flex shrink-0 w-5" alt="star">
                                    <img src="{{ asset('assets/images/icons/Star 1.svg') }}" class="flex shrink-0 w-5" alt="star">
                                </div>
                                <span class="font-bold">5.0</span>
                            </div>
                            <p class="font-bold mt-1">{{ number_format($totalStudents ?? 0) }}+ Students | {{ $totalCourses ?? 0 }} Courses</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex shrink-0 h-[590px] w-[666px] justify-end">
                <img src="{{ asset('assets/images/backgrounds/hero-image.png') }}" alt="hero-image">
            </div>
        </main>
        
        @if(isset($featuredCourses) && $featuredCourses->count() > 0)
        <section id="featured-courses" class="w-full py-[50px] bg-gray-50">
            <div class="w-full max-w-[1280px] px-[75px] mx-auto">
                <div class="flex flex-col items-center gap-[30px]">
                    <div class="text-center">
                        <h2 class="font-extrabold text-[32px] leading-[48px]">Featured Courses</h2>
                        <p class="text-obito-text-secondary mt-2">Kursus terpopuler yang diikuti oleh ribuan students</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
                        @foreach($featuredCourses as $course)
                        <a href="{{ route('front.course.details', $course->slug) }}" class="flex flex-col bg-white rounded-[20px] border border-obito-grey p-5 gap-4 hover:border-obito-green transition-all duration-300">
                            <div class="flex w-full h-[200px] rounded-[12px] overflow-hidden bg-obito-grey">
                                @if($course->thumbnail)
                                    @if(str_starts_with($course->thumbnail, 'http'))
                                        <img src="{{ $course->thumbnail }}" class="w-full h-full object-cover" alt="{{ $course->name }}">
                                    @else
                                        <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-full object-cover" alt="{{ $course->name }}">
                                    @endif
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-obito-green to-green-600">
                                        <span class="text-white font-bold text-lg">{{ substr($course->name, 0, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex flex-col gap-3">
                                <div class="flex items-center gap-2">
                                    @if($course->category)
                                        <span class="text-xs font-semibold bg-obito-light-green text-obito-green px-2 py-1 rounded-full">
                                            {{ $course->category->name }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-obito-text-secondary">
                                        {{ $course->course_students_count ?? 0 }} students
                                    </span>
                                </div>
                                
                                <h3 class="font-bold text-lg leading-[27px] line-clamp-2">{{ $course->name }}</h3>
                                
                                <p class="text-sm text-obito-text-secondary line-clamp-3">{{ $course->about }}</p>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1">
                                        <div class="flex">
                                            @for($i = 1; $i <= 5; $i++)
                                                <img src="{{ asset('assets/images/icons/Star 1.svg') }}" class="flex shrink-0 w-4" alt="star">
                                            @endfor
                                        </div>
                                        <span class="text-sm font-semibold">5.0</span>
                                    </div>
                                    
                                    <span class="text-sm font-semibold text-obito-green">
                                        View Details →
                                    </span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    
                    <div class="text-center">
                        <a href="{{ route('front.pricing') }}" 
                           class="inline-flex items-center rounded-full py-3 px-6 bg-obito-green text-white font-semibold hover:drop-shadow-effect transition-all duration-300">
                            View All Courses
                        </a>
                    </div>
                </div>
            </div>
        </section>
        @endif
@endsection
