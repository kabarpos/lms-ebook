@props(['videoId', 'title' => 'Video'])

@if($videoId)
<div class="youtube-player-container mb-6">
    <!-- Video Title -->
    @if($title && $title !== 'Video')
    <div class="mb-3">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
            </svg>
            Video Pembelajaran
        </h3>
    </div>
    @endif

    <!-- YouTube Player Container -->
    <div class="relative w-full" style="padding-bottom: 56.25%; /* 16:9 aspect ratio */">
        <div class="absolute inset-0 rounded-lg overflow-hidden shadow-lg bg-black">
            <!-- Loading State -->
            <div id="youtube-loading-{{ $videoId }}" class="absolute inset-0 flex items-center justify-center bg-gray-900">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto mb-4"></div>
                    <p class="text-white text-sm">Loading video...</p>
                </div>
            </div>
            
            <!-- YouTube Iframe -->
            <iframe 
                id="youtube-iframe-{{ $videoId }}"
                class="absolute inset-0 w-full h-full"
                src="https://www.youtube.com/embed/{{ $videoId }}?enablejsapi=1&origin={{ url('/') }}&rel=0&modestbranding=1&showinfo=0"
                title="{{ $title }}"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
                onload="document.getElementById('youtube-loading-{{ $videoId }}').style.display='none'"
                style="display: none;">
            </iframe>
        </div>
    </div>

    <!-- Video Controls and Info -->
    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
        <!-- Video Info -->
        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
            </svg>
            <span>Video pembelajaran interaktif</span>
        </div>

        <!-- External Link -->
        <a href="https://www.youtube.com/watch?v={{ $videoId }}" 
           target="_blank" 
           rel="noopener noreferrer"
           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer">
            <span>Buka di YouTube</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
        </a>
    </div>
</div>

@push('after-styles')
<style>
    .youtube-player-container iframe {
        transition: opacity 0.3s ease-in-out;
    }
    
    .youtube-player-container iframe[onload] {
        opacity: 0;
    }
    
    .youtube-player-container iframe.loaded {
        opacity: 1;
        display: block !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const iframe = document.getElementById('youtube-iframe-{{ $videoId }}');
    const loading = document.getElementById('youtube-loading-{{ $videoId }}');
    
    if (iframe && loading) {
        iframe.addEventListener('load', function() {
            loading.style.display = 'none';
            iframe.style.display = 'block';
            iframe.classList.add('loaded');
        });
        
        // Fallback to show iframe after 3 seconds
        setTimeout(function() {
            if (loading.style.display !== 'none') {
                loading.style.display = 'none';
                iframe.style.display = 'block';
                iframe.classList.add('loaded');
            }
        }, 3000);
    }
});
</script>
@endpush
@endif