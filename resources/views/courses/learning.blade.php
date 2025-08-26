@extends('front.layouts.app')
@section('title', $currentContent->name . ' - ' . $course->name)
@section('content')
<div x-data="{ 
    sidebarOpen: false,
    currentProgress: {{ $progressPercentage ?? 0 }},
    totalLessons: {{ $totalLessons ?? $course->courseSections->sum(fn($s) => $s->sectionContents->count()) }},
    completedLessons: {{ $completedLessons ?? 0 }},
    isLessonCompleted: {{ $isCurrentCompleted ? 'true' : 'false' }},
    isLoading: false,
    
    // Mark lesson as complete using database API
    async markLessonComplete() {
        if (this.isLessonCompleted || this.isLoading) return;
        
        this.isLoading = true;
        
        try {
            const response = await fetch('/api/lesson-progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    course_id: {{ $course->id }},
                    section_content_id: {{ $currentContent->id }},
                    time_spent: Math.floor(Math.random() * 600) + 300 // Random 5-15 minutes
                })
            });
            
            const data = await response.json();
            
            if (response.ok && data.status === 'success') {
                this.isLessonCompleted = true;
                this.completedLessons = data.data.course_progress.completed;
                this.currentProgress = data.data.course_progress.percentage;
                
                // Show success notification
                this.showNotification('✅ Lesson completed! Great progress!', 'success');
            } else {
                this.showNotification(data.message || 'Failed to mark lesson as complete', 'error');
            }
        } catch (error) {
            console.error('Error marking lesson complete:', error);
            this.showNotification('Network error. Please try again.', 'error');
        } finally {
            this.isLoading = false;
        }
    },
    
    // Simple notification system
    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
}" 
class="bg-gray-50 min-h-screen">
    
    <!-- Modern Sidebar -->
    <aside :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white w-80 lg:w-96 h-screen border-r border-gray-200 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
        
        <!-- Back to Dashboard Button (Above Sidebar) -->
        <div class="flex-shrink-0 px-6 py-4 border-b border-gray-100">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-lochmara-600 transition-colors text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
        
        <!-- Mobile Close Button -->
        <div class="lg:hidden flex items-center justify-end px-4 py-3 border-b border-gray-100">
            <button @click="sidebarOpen = false" class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <!-- Lesson Navigation -->
        <div class="flex-1 overflow-y-auto">
            <!-- Course Progress Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-lochmara-600 to-lochmara-700 text-white">
                <div class="mb-3">
                    <h2 class="font-semibold text-lg truncate">{{ $course->name }}</h2>
                    <p class="text-lochmara-100 text-sm mt-1">Course Progress</p>
                </div>
                
                <!-- Progress Bar -->
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-2 text-sm">
                        <span x-text="`${completedLessons} of ${totalLessons} lessons`"></span>
                        <span x-text="`${Math.round(currentProgress)}%`"></span>
                    </div>
                    <div class="w-full bg-lochmara-800 rounded-full h-2">
                        <div class="bg-white h-2 rounded-full transition-all duration-500" 
                             :style="`width: ${currentProgress}%`"></div>
                    </div>
                </div>
                
                <!-- Progress Stats -->
                <div class="flex items-center justify-between text-xs text-lochmara-100">
                    <div class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                        <span x-text="completedLessons + ' completed'"></span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <span x-text="(totalLessons - completedLessons) + ' remaining'"></span>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4">
                @foreach($course->courseSections as $sectionIndex => $section)
                <div class="mb-6 last:mb-0">
                    <!-- Section Header -->
                    <button type="button" 
                            data-expand="section-{{ $section->id }}" 
                            class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-lochmara-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-lochmara-700 font-semibold text-sm">{{ $sectionIndex + 1 }}</span>
                            </div>
                            <div class="text-left">
                                <h3 class="font-semibold text-gray-900 text-sm group-hover:text-lochmara-700 transition-colors">{{ $section->name }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $section->sectionContents->count() }} lessons</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-lochmara-600 transition-all duration-200 section-arrow" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <!-- Section Content -->
                    <div id="section-{{ $section->id }}" class="section-content mt-3 ml-11 space-y-2">
                        @foreach($section->sectionContents as $contentIndex => $content)
                        @php
                            $isActive = $currentSection && $section->id == $currentSection->id && $currentContent->id == $content->id;
                            $lessonNumber = $contentIndex + 1;
                            $isCompleted = isset($userProgress[$content->id]) && $userProgress[$content->id]->is_completed;
                        @endphp
                        <a href="{{ route('dashboard.course.learning', [
                                'course' => $course->slug,
                                'courseSection' => $section->id,
                                'sectionContent' => $content->id,
                            ]) }}"
                           @click="sidebarOpen = false" 
                           class="group block">
                            <div class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 {{ $isActive ? 'bg-lochmara-50 border border-lochmara-200' : 'hover:bg-gray-50 border border-transparent hover:border-gray-200' }}">
                                <!-- Lesson Status Icon -->
                                <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 
                                    @if($isCompleted) 
                                        bg-green-500 text-white
                                    @elseif($isActive) 
                                        bg-lochmara-600 text-white
                                    @else 
                                        bg-gray-100 text-gray-400 group-hover:bg-lochmara-100 group-hover:text-lochmara-600
                                    @endif">
                                    @if($isCompleted)
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                        </svg>
                                    @elseif($isActive)
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    @else
                                        <span class="text-xs font-semibold">{{ $lessonNumber }}</span>
                                    @endif
                                </div>
                                
                                <!-- Lesson Info -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-sm {{ $isActive ? 'text-lochmara-900' : 'text-gray-900 group-hover:text-lochmara-700' }} line-clamp-2 leading-tight">
                                        {{ $content->name }}
                                        @if($isCompleted)
                                            <span class="ml-2 text-green-600 text-xs">✓</span>
                                        @endif
                                    </h4>
                                    <div class="flex items-center space-x-4 mt-1 text-xs {{ $isActive ? 'text-lochmara-600' : 'text-gray-500' }}">
                                        <span class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            5-8 min
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Article
                                        </span>
                                        @if($isCompleted)
                                        <span class="flex items-center text-green-600">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                            </svg>
                                            Completed
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Active Indicator -->
                                @if($isActive)
                                <div class="w-2 h-8 bg-lochmara-600 rounded-full flex-shrink-0"></div>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </aside>
    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" 
         class="mobile-overlay fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>
    
    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen bg-white lg:ml-96">
        <!-- Top Navigation Bar -->
        <header class="flex-shrink-0 bg-white border-b border-gray-200 px-4 lg:px-8 py-4">
            <div class="main-content-wrapper">
                <div class="content-inner">
                    <div class="flex items-center justify-between">
                        <!-- Mobile Menu Button (Hidden on Desktop) -->
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        
                        <!-- Breadcrumb Navigation -->
                        <nav class="hidden lg:flex items-center space-x-2 text-sm">
                            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-lochmara-600 transition-colors">Dashboard</a>
                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-lochmara-600 transition-colors truncate max-w-xs">{{ $course->name }}</a>
                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <span class="text-gray-900 font-medium truncate max-w-xs">{{ $currentContent->name }}</span>
                        </nav>
                        
                        <!-- Lesson Navigation Controls -->
                        <div class="flex items-center space-x-3">
                            <!-- Progress Info -->
                            <div class="hidden sm:flex items-center space-x-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-lochmara-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <span x-text="`Lesson ${completedLessons + 1} of ${totalLessons}`"></span>
                            </div>
                            
                            <!-- Quick Navigation -->
                            <div class="flex items-center space-x-1">
                                @if(isset($prevContent))
                                <a href="{{ route('dashboard.course.learning', [
                                        'course' => $course->slug,
                                        'courseSection' => $prevContent->course_section_id,
                                        'sectionContent' => $prevContent->id,
                                    ]) }}" 
                                   class="p-2 rounded-lg text-gray-400 hover:text-lochmara-600 hover:bg-lochmara-50 transition-colors" 
                                   title="Previous Lesson">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </a>
                                @endif
                                
                                @if(isset($nextContent))
                                <a href="{{ route('dashboard.course.learning', [
                                        'course' => $course->slug,
                                        'courseSection' => $nextContent->course_section_id,
                                        'sectionContent' => $nextContent->id,
                                    ]) }}" 
                                   class="p-2 rounded-lg text-gray-400 hover:text-lochmara-600 hover:bg-lochmara-50 transition-colors" 
                                   title="Next Lesson">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Content Area -->
        <main class="flex-1 overflow-y-auto content-area">
            <!-- Content Container with Proper Width Constraints -->
            <div class="main-content-wrapper px-4 sm:px-6 lg:px-8">
                <div class="content-inner py-6 lg:py-10">
                <!-- Content Header -->
                <header class="mb-6 lg:mb-8">
                    <div class="content-card rounded-2xl mb-6">
                        <div class="px-6 sm:px-8 lg:px-10 py-6 lg:py-8">
                            <div class="flex items-start justify-between mb-6">
                        <div class="flex-1">
                            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4 leading-tight">
                                {{ $currentContent->name }}
                            </h1>
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                <span class="inline-flex items-center px-3 py-1 bg-lochmara-100 text-lochmara-700 rounded-full">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    5-8 min read
                                </span>
                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-full">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    {{ $currentSection->name ?? 'Section' }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-full">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Article
                                </span>
                            </div>
                        </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-lochmara-600 to-lochmara-500 h-2 rounded-full transition-all duration-500" 
                                     :style="`width: ${currentProgress}%`"></div>
                            </div>
                        </div>
                    </div>
                </header>
                
                <!-- Article Content with Proper Constraints -->
                <article class="prose prose-lg prose-gray max-w-none">
                    <div class="content-card rounded-2xl hover:shadow-md transition-shadow duration-300">
                        <div class="px-6 sm:px-8 lg:px-10 py-8 lg:py-12">
                            <div class="prose prose-lg max-w-none content-typography">
                                {!! $currentContent->content !!}
                            </div>
                        </div>
                    </div>
                </article>
                
                <!-- Lesson Navigation -->
                <div class="mt-8 lg:mt-12">
                    <div class="content-card rounded-2xl">
                        <div class="px-6 sm:px-8 lg:px-10 py-6 lg:py-8">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                    <!-- Previous Lesson -->
                    <div class="flex-1">
                        @if(isset($prevContent))
                        <a href="{{ route('dashboard.course.learning', [
                                'course' => $course->slug,
                                'courseSection' => $prevContent->course_section_id,
                                'sectionContent' => $prevContent->id,
                            ]) }}" 
                           class="group inline-flex items-center text-sm font-medium text-gray-600 hover:text-lochmara-600 transition-colors">
                            <svg class="w-4 h-4 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <div class="text-left">
                                <div class="text-xs text-gray-500">Previous</div>
                                <div class="font-semibold line-clamp-1">{{ $prevContent->name ?? 'Previous Lesson' }}</div>
                            </div>
                        </a>
                        @endif
                    </div>
                    
                    <!-- Next Lesson -->
                    <div class="flex-1 text-right">
                        @if(isset($nextContent))
                        <a href="{{ route('dashboard.course.learning', [
                                'course' => $course->slug,
                                'courseSection' => $nextContent->course_section_id,
                                'sectionContent' => $nextContent->id,
                            ]) }}" 
                           class="group inline-flex items-center text-sm font-medium text-gray-600 hover:text-lochmara-600 transition-colors">
                            <div class="text-right mr-2">
                                <div class="text-xs text-gray-500">Next</div>
                                <div class="font-semibold line-clamp-1">{{ $nextContent->name ?? 'Next Lesson' }}</div>
                            </div>
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                        @endif
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </main>
                
                <!-- Action Buttons Below Content -->
                <div class="mt-8 lg:mt-12">
                    <div class="content-card rounded-2xl">
                        <div class="px-6 sm:px-8 lg:px-10 py-6 lg:py-8">
                            <div class="flex flex-col sm:flex-row items-stretch gap-4">
                                <!-- Mark Complete Button -->
                                <button 
                                    @click="markLessonComplete()" 
                                    :disabled="isLessonCompleted || isLoading"
                                    class="inline-flex items-center justify-center px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 min-w-[180px]"
                                    :class="isLessonCompleted ? 
                                        'bg-green-100 text-green-800 border border-green-300 cursor-not-allowed' : 
                                        isLoading ? 'bg-gray-100 text-gray-500 border border-gray-300 cursor-not-allowed' :
                                        'border border-green-300 text-green-700 bg-green-50 hover:bg-green-100 hover:border-green-400'"
                                >
                                    <!-- Loading Spinner -->
                                    <div x-show="isLoading" class="w-4 h-4 mr-2 animate-spin rounded-full border-2 border-gray-300 border-t-green-600"></div>
                                    
                                    <!-- Checkmark Icon -->
                                    <svg x-show="!isLoading" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    
                                    <!-- Button Text -->
                                    <span x-text="isLoading ? 'Saving...' : (isLessonCompleted ? 'Completed ✅' : 'Mark as Complete')"></span>
                                </button>
                                
                                <!-- Continue Learning Button -->
                                @if (!$isFinished && isset($nextContent))
                                <a href="{{ route('dashboard.course.learning', [
                                            'course' => $course->slug,
                                            'courseSection' => $nextContent->course_section_id,
                                            'sectionContent' => $nextContent->id,
                                        ]) }}" 
                                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-lochmara-600 to-lochmara-700 text-white text-sm font-medium rounded-lg hover:from-lochmara-700 hover:to-lochmara-800 transition-all duration-200">
                                    <span>Continue Learning</span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>
                                @elseif ($isFinished)
                                <a href="{{ route('dashboard.course.learning.finished', $course->slug) }}" 
                                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Complete Course</span>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
    </div>
    
    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" 
         class="mobile-overlay fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>
</div>


@endsection

@push('after-styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    <style>
        /* Unified Layout - Single Container Design */
        body, html {
            font-family: "Manrope", ui-sans-serif, system-ui, sans-serif !important;
        }
        
        /* Remove all visual separations */
        .content-card {
            background: transparent;
            border: none;
            box-shadow: none;
        }
        
        /* Seamless content flow */
        .main-content-wrapper {
            max-width: 100%;
            margin: 0;
        }
        
        .content-inner {
            max-width: 100%;
            margin: 0;
            width: 100%;
        }
        
        /* Content Area */
        .content-area {
            background: #ffffff;
        }
        
        /* Fixed Sidebar Positioning */
        aside {
            position: fixed !important;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 50;
        }
        
        @media (max-width: 1023px) {
            /* Mobile: Hidden by default, shows when toggled */
            aside {
                transform: translateX(-100%);
            }
            
            aside.translate-x-0 {
                transform: translateX(0) !important;
            }
        }
        
        @media (min-width: 1024px) {
            /* Desktop: Always visible and fixed */
            aside {
                transform: translateX(0) !important;
            }
        }
        
        /* Typography */
        .content-typography {
            line-height: 1.75;
            font-size: 1.125rem;
        }
        
        @media (max-width: 640px) {
            .content-typography {
                font-size: 1rem;
                line-height: 1.625;
            }
        }
        
        /* Enhanced prose styling */
        .prose {
            color: #374151;
            max-width: none;
            font-size: 1.125rem;
            line-height: 1.75;
        }
        
        .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
            color: #1f2937;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        
        .prose h1 {
            font-size: 2.25rem;
            line-height: 1.2;
            margin: 2rem 0 1rem;
            background: linear-gradient(135deg, #0f4c7a 0%, #1d4ed8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .prose h2 {
            font-size: 1.875rem;
            line-height: 1.3;
            margin: 1.75rem 0 1rem;
            color: #0f4c7a;
        }
        
        .prose h3 {
            font-size: 1.5rem;
            line-height: 1.4;
            margin: 1.5rem 0 0.75rem;
            color: #1e40af;
        }
        
        .prose p {
            margin-bottom: 1.25rem;
            text-align: justify;
        }
        
        .prose a {
            color: #0f4c7a;
            text-decoration: none;
            font-weight: 500;
            border-bottom: 1px solid transparent;
            transition: all 0.2s ease;
        }
        
        .prose a:hover {
            color: #0c3d61;
            border-bottom-color: #0f4c7a;
        }
        
        .prose ul, .prose ol {
            margin: 1.5rem 0;
            padding-left: 2rem;
        }
        
        .prose li {
            margin-bottom: 0.75rem;
            line-height: 1.75;
        }
        
        .prose li::marker {
            color: #0f4c7a;
            font-weight: 600;
        }
        
        .prose blockquote {
            border-left: 4px solid #0f4c7a;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            padding: 1.5rem;
            margin: 2rem 0;
            border-radius: 0.75rem;
            font-style: italic;
            position: relative;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .prose blockquote::before {
            content: '\201C';
            position: absolute;
            top: -0.5rem;
            left: 1rem;
            font-size: 4rem;
            color: #0f4c7a;
            opacity: 0.3;
            font-family: Georgia, serif;
        }
        
        .prose code {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            color: #dc2626;
            font-weight: 500;
        }
        
        .prose pre {
            background: #1e293b;
            color: #f1f5f9;
            padding: 1.5rem;
            border-radius: 0.75rem;
            overflow-x: auto;
            margin: 2rem 0;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border: 1px solid #334155;
        }
        
        .prose pre code {
            background: transparent;
            border: none;
            color: inherit;
            padding: 0;
        }
        
        .prose img {
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            transition: transform 0.3s ease;
        }
        
        .prose img:hover {
            transform: scale(1.02);
        }
        
        .prose table {
            width: 100%;
            border-collapse: collapse;
            margin: 2rem 0;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .prose th, .prose td {
            border: 1px solid #e5e7eb;
            padding: 1rem;
            text-align: left;
        }
        
        .prose th {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            font-weight: 700;
            color: #374151;
        }
        
        .prose tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .prose tbody tr:hover {
            background-color: #f0f9ff;
        }
        
        /* Content wrapper styling */
        .content-wrapper {
            background: #ffffff;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        /* Section accordion animation */
        .section-content {
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
            overflow: hidden;
        }
        
        .section-arrow {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .section-arrow.expanded {
            transform: rotate(180deg);
        }
        
        /* Mobile optimizations */
        @media (max-width: 640px) {
            .prose {
                font-size: 1rem;
                line-height: 1.625;
            }
            
            .prose h1 {
                font-size: 1.75rem;
            }
            
            .prose h2 {
                font-size: 1.5rem;
            }
            
            .prose h3 {
                font-size: 1.25rem;
            }
            
            .prose blockquote {
                padding: 1rem;
                margin: 1.5rem 0;
            }
            
            .prose pre {
                padding: 1rem;
                margin: 1.5rem 0;
            }
        }
        
        /* Animation utilities */
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Focus states for accessibility */
        .focus\:ring-lochmara:focus {
            --tw-ring-color: rgba(15, 76, 122, 0.5);
        }
        
        /* Custom shadows */
        .shadow-3xl {
            box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
        }
        
        /* Line clamp utilities */
        .line-clamp-1 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
        }
        
        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
        
        .line-clamp-3 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
        }
        
        /* Custom gradient backgrounds */
        .bg-gradient-lochmara {
            background: linear-gradient(135deg, #0f4c7a 0%, #1d4ed8 100%);
        }
        
        /* Enhanced button styles */
        .btn-primary {
            background: linear-gradient(135deg, #0f4c7a 0%, #1d4ed8 100%);
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(15, 76, 122, 0.1);
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(15, 76, 122, 0.2);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
    </style>
@endpush

@push('after-scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Initialize sidebar state
    function initializeSidebar() {
        const alpineData = document.querySelector('[x-data]').__x?.$data;
        if (alpineData && window.innerWidth < 1024) {
            alpineData.sidebarOpen = false;
        }
    }
    
    initializeSidebar();
    
    // Enhanced code highlighting with multiple languages
    document.querySelectorAll('pre').forEach(pre => {
        if (!pre.querySelector('code')) {
            const code = document.createElement('code');
            code.textContent = pre.textContent.trim();
            pre.innerHTML = '';
            pre.appendChild(code);
        }
    });
    hljs.highlightAll();
    
    // Enhanced accordion functionality
    function initializeAccordions() {
        document.querySelectorAll('[data-expand]').forEach(button => {
            const targetId = button.getAttribute('data-expand');
            const targetElement = document.getElementById(targetId);
            const arrow = button.querySelector('.section-arrow');
            
            if (!targetElement) return;
            
            // Set initial state
            const hasActiveLesson = targetElement.querySelector('.group a[href*="{{ $currentContent->id }}"]');
            const isInitiallyExpanded = hasActiveLesson;
            
            if (isInitiallyExpanded) {
                targetElement.style.maxHeight = targetElement.scrollHeight + 'px';
                targetElement.style.opacity = '1';
                arrow?.classList.add('expanded');
            } else {
                targetElement.style.maxHeight = '0px';
                targetElement.style.opacity = '0';
            }
            
            // Add click listener
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const isExpanded = targetElement.style.maxHeight && targetElement.style.maxHeight !== '0px';
                
                // Close all other sections first
                document.querySelectorAll('[data-expand]').forEach(otherButton => {
                    if (otherButton !== button) {
                        const otherId = otherButton.getAttribute('data-expand');
                        const otherElement = document.getElementById(otherId);
                        const otherArrow = otherButton.querySelector('.section-arrow');
                        
                        if (otherElement && otherElement.style.maxHeight !== '0px') {
                            otherElement.style.maxHeight = '0px';
                            otherElement.style.opacity = '0';
                            otherArrow?.classList.remove('expanded');
                        }
                    }
                });
                
                // Toggle current section
                if (isExpanded) {
                    targetElement.style.maxHeight = '0px';
                    targetElement.style.opacity = '0';
                    arrow?.classList.remove('expanded');
                } else {
                    targetElement.style.maxHeight = targetElement.scrollHeight + 'px';
                    targetElement.style.opacity = '1';
                    arrow?.classList.add('expanded');
                }
            });
        });
    }
    
    // Initialize accordions
    initializeAccordions();
    
    // Auto-close mobile sidebar when clicking on a lesson
    function handleMobileSidebarClose() {
        document.querySelectorAll('aside a[href*="/learning/"]').forEach(link => {
            link.addEventListener('click', () => {
                const alpineData = document.querySelector('[x-data]').__x?.$data;
                if (alpineData && window.innerWidth < 1024) {
                    alpineData.sidebarOpen = false;
                }
            });
        });
    }
    
    handleMobileSidebarClose();
    
    // Smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Reading progress tracking
    function trackReadingProgress() {
        const article = document.querySelector('article');
        const progressBar = document.querySelector('[\\:style*="currentProgress"]');
        
        if (!article || !progressBar) return;
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const scrolled = window.scrollY;
                    const articleHeight = article.offsetHeight;
                    const windowHeight = window.innerHeight;
                    const totalScroll = articleHeight - windowHeight;
                    
                    if (totalScroll > 0) {
                        const progress = Math.min((scrolled / totalScroll) * 100, 100);
                        // Update Alpine.js data if available
                        const alpineData = document.querySelector('[x-data]').__x?.$data;
                        if (alpineData) {
                            alpineData.currentProgress = Math.max(progress, alpineData.currentProgress);
                        }
                    }
                }
            });
        });
        
        observer.observe(article);
    }
    
    trackReadingProgress();
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        // Navigate with arrow keys (if no input is focused)
        if (document.activeElement.tagName !== 'INPUT' && 
            document.activeElement.tagName !== 'TEXTAREA') {
            
            if (e.key === 'ArrowLeft' && e.altKey) {
                // Previous lesson
                const prevLink = document.querySelector('a[title="Previous Lesson"]');
                if (prevLink) {
                    e.preventDefault();
                    prevLink.click();
                }
            } else if (e.key === 'ArrowRight' && e.altKey) {
                // Next lesson
                const nextLink = document.querySelector('a[title="Next Lesson"]');
                if (nextLink) {
                    e.preventDefault();
                    nextLink.click();
                }
            } else if (e.key === 's' && e.altKey) {
                // Toggle sidebar
                e.preventDefault();
                const alpineData = document.querySelector('[x-data]').__x?.$data;
                if (alpineData) {
                    alpineData.sidebarOpen = !alpineData.sidebarOpen;
                }
            }
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', () => {
        const alpineData = document.querySelector('[x-data]').__x?.$data;
        if (alpineData && window.innerWidth < 1024) {
            alpineData.sidebarOpen = false;
        }
        
        // Recalculate accordion heights
        document.querySelectorAll('.section-content').forEach(element => {
            if (element.style.maxHeight && element.style.maxHeight !== '0px') {
                element.style.maxHeight = element.scrollHeight + 'px';
            }
        });
    });
    
    // Add loading states to navigation buttons
    document.querySelectorAll('a[href*="/learning/"]').forEach(link => {
        link.addEventListener('click', () => {
            const loader = document.createElement('div');
            loader.className = 'inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2';
            
            const text = link.querySelector('span');
            if (text) {
                text.prepend(loader);
            }
        });
    });
    
    // Add copy code functionality
    document.querySelectorAll('pre').forEach(pre => {
        const button = document.createElement('button');
        button.className = 'absolute top-2 right-2 px-3 py-1 text-xs bg-gray-700 hover:bg-gray-600 text-white rounded transition-colors opacity-0 group-hover:opacity-100';
        button.textContent = 'Copy';
        
        pre.classList.add('group', 'relative');
        pre.appendChild(button);
        
        button.addEventListener('click', () => {
            const code = pre.querySelector('code')?.textContent || pre.textContent;
            navigator.clipboard.writeText(code).then(() => {
                button.textContent = 'Copied!';
                setTimeout(() => {
                    button.textContent = 'Copy';
                }, 2000);
            });
        });
    });
    
    // Add entrance animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-slide-in');
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.prose > *').forEach(el => {
        observer.observe(el);
    });
    
    console.log('🎓 Course Learning UI initialized successfully!');
});
</script>
@endpush
