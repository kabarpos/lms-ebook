@props([
    'src' => '',
    'alt' => '',
    'class' => '',
    'loading' => 'lazy',
    'containerClass' => '',
    'placeholderHeight' => '120px'
])

@php
    // Generate a deterministic class for min-height to avoid inline style attributes (CSP-friendly)
    $placeholderClass = 'lazy-min-'.substr(md5($placeholderHeight), 0, 8);
@endphp

<div x-data="{
    loaded: false,
    error: false,
    isLoading: true,
    init() {
        this.loadImage();
    },
    loadImage() {
        if (!this.$refs.image || !this.$refs.image.src) return;
        
        // Set loading state
        this.isLoading = true;
        this.loaded = false;
        this.error = false;
        
        // Create new image to preload
        const img = new Image();
        
        img.onload = () => {
            this.loaded = true;
            this.error = false;
            this.isLoading = false;
        };
        
        img.onerror = () => {
            this.error = true;
            this.loaded = false;
            this.isLoading = false;
        };
        
        // Start loading
        img.src = this.$refs.image.src;
    }
}" class="{{ $containerClass ?? '' }} relative overflow-hidden bg-gray-100">
    
    <!-- Loading placeholder -->
    <div x-show="isLoading && !error" 
         class="absolute inset-0 flex items-center justify-center bg-gray-200 animate-pulse {{ $placeholderClass }}">
        <div class="flex flex-col items-center space-y-2">
            <svg class="w-6 h-6 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-xs text-gray-500">Loading...</span>
        </div>
    </div>
    
    <!-- Error state -->
    <div x-show="error" 
         class="absolute inset-0 flex items-center justify-center bg-gray-100 text-gray-500 {{ $placeholderClass }}">
        <div class="text-center">
            <svg class="w-6 h-6 mx-auto mb-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-xs">Image not found</span>
        </div>
    </div>
    
    <!-- Actual image -->
    <img x-ref="image"
         x-show="loaded"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         src="{{ $src }}"
         alt="{{ $alt ?? '' }}"
         class="{{ $class ?? '' }} {{ $placeholderClass }}"
         loading="{{ $loading ?? 'lazy' }}"
         />
</div>

@push('styles')
<style nonce="{{ request()->attributes->get('csp_nonce') }}">
.lazy-image-container {
    position: relative;
    display: inline-block;
    overflow: hidden;
}

.lazy-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    min-height: 40px;
    z-index: 1;
}

.lazy-image {
    display: block;
    max-width: 100%;
    height: auto;
    position: relative;
    z-index: 2;
    transition: opacity 0.3s ease;
}

/* Deterministic min-height class injected per instance to avoid inline style attributes */
.{{ $placeholderClass }} {
    min-height: {{ $placeholderHeight }};
}
</style>
@endpush