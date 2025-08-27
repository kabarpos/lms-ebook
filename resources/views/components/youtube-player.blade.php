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

    <!-- YouTube Player Container - BULLETPROOF -->
    <div class="youtube-player-wrapper relative w-full overflow-hidden rounded-lg shadow-lg" 
         style="padding-bottom: 56.25%; height: 0; position: relative; z-index: 1; background: #000;"
         data-youtube-component="true">
        <!-- YouTube Iframe - IMMEDIATELY VISIBLE - BULLETPROOF -->
        <iframe 
            id="youtube-iframe-{{ $videoId }}"
            class="absolute top-0 left-0 w-full h-full border-0 youtube-component-iframe"
            style="display: block !important; visibility: visible !important; opacity: 1 !important; position: absolute !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; border: none !important; z-index: 2 !important; background: #000 !important;"
            src="https://www.youtube.com/embed/{{ $videoId }}?rel=0&modestbranding=1&controls=1&playsinline=1&enablejsapi=0"
            title="{{ $title }}"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen"
            allowfullscreen
            webkitallowfullscreen
            mozallowfullscreen
            referrerpolicy="strict-origin-when-cross-origin"
            data-youtube-processed="true">
        </iframe>
    </div>

   
</div>

@push('after-styles')
<style>
    /* CRITICAL: YouTube Player Styling - Proper Z-Index */
    .youtube-player-container {
        position: relative !important;
        z-index: 1 !important;
        background: transparent !important;
    }
    
    .youtube-player-wrapper {
        background-color: #000 !important;
        border-radius: 0.5rem !important;
        overflow: hidden !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        position: relative !important;
        z-index: 1 !important;
    }
    
    /* BULLETPROOF iframe styling - Proper Z-Index */
    .youtube-player-wrapper iframe,
    .youtube-player-container iframe,
    iframe[id*="youtube-iframe"] {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        border: none !important;
        border-radius: 0.5rem !important;
        z-index: 2 !important;
        background: #000 !important;
        transform: none !important;
        transition: none !important;
    }
    
    /* Prevent any potential CSS resets */
    .youtube-player-container .youtube-player-wrapper {
        position: relative !important;
        z-index: 1 !important;
    }
    
    /* Prevent JavaScript wrapper interference */
    .youtube-player-container .responsive-video-wrapper {
        position: static !important;
        padding-bottom: 0 !important;
        height: auto !important;
    }
</style>
@endpush

@push('scripts')
<script>
// CRITICAL: Following specification - iframe harus langsung visible tanpa display:none
document.addEventListener('DOMContentLoaded', function() {
    const iframe = document.getElementById('youtube-iframe-{{ $videoId }}');
    
    if (iframe) {
        // BULLETPROOF visibility enforcement
        iframe.style.cssText = `
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            border: none !important;
            border-radius: 0.5rem !important;
            z-index: 2 !important;
            background: #000 !important;
            pointer-events: auto !important;
        `;
        
        // Mark this iframe as processed to prevent TipTap interference
        iframe.setAttribute('data-youtube-processed', 'true');
        iframe.classList.add('youtube-component-iframe');
        
        // Ensure parent container is not interfered with
        const container = iframe.closest('.youtube-player-container');
        if (container) {
            container.setAttribute('data-youtube-component', 'true');
        }
    }
    
    // PREVENT TipTap script interference with our YouTube component
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && node.classList && node.classList.contains('responsive-video-wrapper')) {
                        // Check if this wrapper is trying to wrap our component iframe
                        const componentIframe = node.querySelector('iframe[data-youtube-processed="true"]');
                        if (componentIframe) {
                            // Unwrap it immediately
                            const originalParent = node.parentNode;
                            originalParent.insertBefore(componentIframe, node);
                            originalParent.removeChild(node);
                        }
                    }
                });
            }
        });
    });
    
    // Observe the entire document for interference
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
</script>
@endpush
@endif