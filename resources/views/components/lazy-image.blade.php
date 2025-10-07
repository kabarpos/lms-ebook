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

<div class="{{ $containerClass ?? '' }} relative overflow-hidden bg-gray-100 {{ $placeholderClass }}">
    
    <!-- Simple skeleton via container background; no overlay to avoid flicker -->
    
    <!-- Actual image (no Alpine gating to avoid invisible images) -->
    <img
         src="{{ $src }}"
         alt="{{ $alt ?? '' }}"
         class="{{ $class ?? '' }} {{ $placeholderClass }}"
         loading="{{ $loading ?? 'lazy' }}"
         decoding="async"
         />
</div>

@push('after-styles')
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