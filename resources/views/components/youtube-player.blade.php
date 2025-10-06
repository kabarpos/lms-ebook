@props(['videoId', 'title' => 'Video'])

@if($videoId)
<div class="youtube-player-container mb-6">
    {{-- <!-- Video Title -->
    @if($title && $title !== 'Video')
    <div class="mb-3">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
            </svg>
            {{ $title }}
        </h3>
    </div>
    @endif --}}

    <!-- YouTube Player enhanced by Plyr -->
    <div class="youtube-player-wrapper relative w-full aspect-video bg-gray-900 rounded-lg overflow-hidden">
        <div class="plyr__video-embed" id="plyr-{{ $videoId }}">
            <iframe
                class="youtube-component-iframe"
                src="https://www.youtube-nocookie.com/embed/{{ $videoId }}?iv_load_policy=3&modestbranding=1&rel=0&playsinline=1&enablejsapi=1"
                title="{{ $title }}"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
                webkitallowfullscreen
                mozallowfullscreen
                referrerpolicy="strict-origin-when-cross-origin"
                frameborder="0"
            ></iframe>
        </div>
    </div>
</div>

<!-- Plyr assets and initialization -->
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css">
<script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
<script nonce="{{ request()->attributes->get('csp_nonce') }}">
document.addEventListener('DOMContentLoaded', function() {
    var target = document.getElementById('plyr-{{ $videoId }}');
    if (target && window.Plyr) {
        var player = new Plyr(target, {
            ratio: '16:9',
            youtube: {
                noCookie: true
            },
            controls: [
                'play-large','play','progress','current-time','mute','volume','settings','pip','airplay','fullscreen'
            ]
        });
    }
});
</script>

 

@push('after-styles')
<style nonce="{{ request()->attributes->get('csp_nonce') }}">
    .youtube-player-container {
        position: relative;
        background: transparent;
    }
    
    .youtube-player-wrapper {
        position: relative;
        background: #000;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .youtube-player-wrapper iframe {
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 0.5rem;
    }
</style>
@endpush
@endif