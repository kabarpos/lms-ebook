@props([
    'src' => '',
    'alt' => '',
    'class' => '',
    'loading' => 'lazy',
    'containerClass' => ''
])

<div class="lazy-image-container {{ $containerClass }}" x-data="{ loaded: false, error: false }">
    <!-- Placeholder -->
    <div x-show="!loaded" class="lazy-placeholder bg-gray-200 animate-pulse flex items-center justify-center">
        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
        </svg>
    </div>
    
    <!-- Image -->
    <img x-show="loaded"
         src="{{ $src }}"
         alt="{{ $alt }}"
         loading="{{ $loading }}"
         class="lazy-image {{ $class }}"
         @load="loaded = true"
         x-on:error="error = true; loaded = true" />
</div>

@push('styles')
<style>
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
</style>
@endpush