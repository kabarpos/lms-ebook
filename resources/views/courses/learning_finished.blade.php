<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Course Finished - {{ $course->name }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Manrope', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif !important;
        }
    </style>
</head>
<body class="antialiased">
<div class="relative flex justify-center">
    <div id="backgroundImage" class="absolute top-0 left-0 right-0">
        <img src="{{ asset('assets/images/backgrounds/learning-finished.png') }}" alt="image" class="h-[777px] object-cover object-bottom w-full" />
    </div>
    <main class="relative mt-[178px] flex flex-col gap-[30px] p-[30px] w-[560px] rounded-[20px] border bg-white border-obito-grey">
        <img src="{{ asset('assets/images/icons/cup-green-fill.svg') }}" alt="icon" class="size-[60px] shrink-0 mx-auto" />
        <div class="mx-auto flex w-[500px] flex-col gap-[10px] items-center">
            <h1 class="text-center font-bold text-[28px] leading-[42px]">What a Day! Now<br>You’re Ready to Work</h1>
            <p class="text-center text-obito-text-secondary leading-[28px]">Anda telah menyelesaikan materi kelas dengan baik selanjutnya dapat membuat portfolio dan mengikuti magang</p>
        </div>
        <div id="card" class="flex items-center pt-[10px] pb-[10px] pl-[10px] pr-4 border border-obito-grey rounded-[20px] gap-4">
            <div class="flex justify-center items-center overflow-hidden shrink-0 w-[180px] h-[130px] rounded-[14px]">
                @if($course->thumbnail)
                    @if(str_starts_with($course->thumbnail, 'http'))
                        <img src="{{ $course->thumbnail }}" alt="image" class="w-full h-full object-cover" />
                    @else
                        <img src="{{ Storage::url($course->thumbnail) }}" alt="image" class="w-full h-full object-cover" />
                    @endif
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-obito-green to-green-600">
                        <span class="text-white font-bold text-lg">{{ substr($course->name, 0, 2) }}</span>
                    </div>
                @endif
            </div>
            <div class="flex flex-col gap-[10px]">
                <h2 class="font-bold">{{ $course->name }}</h2>
                <div class="flex items-center gap-[6px]">
                    <img src="{{ asset('assets/images/icons/crown-green.svg') }}" alt="icon" class="size-5 shrink-0" />
                    <p class="text-sm leading-[21px] text-obito-text-secondary">{{ $course->category->name }}</p>
                </div>
                <div class="flex items-center gap-[6px]">
                    <img src="{{ asset('assets/images/icons/menu-board-green.svg') }}" alt="icon" class="size-5 shrink-0" />
                    <p class="text-sm leading-[21px] text-obito-text-secondary">{{ $course->content_count }} Lessons</p>
                </div>
            </div>
        </div>
        <div class="buttons grid grid-cols-2 gap-[12px]">
            <a href="#" class="border border-obito-grey rounded-full py-[10px] flex justify-center items-center hover:border-obito-green transition-all duration-300">
                <span class="font-semibold">Get My Certificate</span>
            </a>
            <a href="{{ route('dashboard') }}" class="text-white rounded-full py-[10px] flex justify-center items-center bg-obito-green hover:drop-shadow-effect transition-all duration-300">
                <span class="font-semibold">Explore Courses</span>
            </a>
        </div>
    </main>
</div>
</body>
</html>
