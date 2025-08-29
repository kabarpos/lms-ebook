@props([
    'src' => '',
    'alt' => '',
    'class' => '',
    'placeholder' => asset('assets/images/placeholder.svg'),
    'loading' => 'lazy',
    'decoding' => 'async',
    'containerClass' => ''
])

<div class="lazy-image-container {{ $containerClass }}" x-data="lazyImage()" x-init="init()">
    <!-- Placeholder/Loading State -->
    <div x-show="!loaded" class="lazy-placeholder bg-gray-200 animate-pulse flex items-center justify-center">
        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
        </svg>
    </div>
    
    <!-- Actual Image -->
    <img 
        x-show="loaded"
        x-ref="image"
        src="{{ $src }}"
        alt="{{ $alt }}"
        loading="{{ $loading }}"
        decoding="{{ $decoding }}"
        class="lazy-image {{ $class }} transition-opacity duration-300"
        @load="loaded = true"
        style="display: none;"
    >
</div>

@push('scripts')

<script>
function lazyImage() {
    return {
        loaded: false,
        error: false,
        
        init() {
            // Use Intersection Observer for better performance
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.loadImage();
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    rootMargin: '50px'
                });
                
                observer.observe(this.$el);
            } else {
                // Fallback for older browsers
                this.loadImage();
            }
        },
        
        loadImage() {
            const img = this.$refs.image;
            if (img && img.src) {
                // Image will load and trigger @load event
                img.style.display = 'block';
            }
        },
        
        handleError() {
            this.error = true;
            this.loaded = true; // Show error state
            console.warn('Failed to load image:', this.$refs.image.src);
        }
    }
}
</script>
@endpush

@push('styles')
<style>
.lazy-image-container {
    position: relative;
    display: inline-block;
}

.lazy-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
}

.lazy-image {
    display: block;
    max-width: 100%;
    height: auto;
}

.lazy-image[x-show="loaded"] {
    opacity: 0;
    animation: fadeIn 0.3s ease-in-out forwards;
}

@keyframes fadeIn {
    to {
        opacity: 1;
    }
}
</style>
@endpush