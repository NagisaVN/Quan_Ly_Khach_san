@props(['id' => null, 'class' => ''])

<div class="skeleton-loader {{ $class }}" id="{{ $id }}">
    <div class="placeholder-glow">
        <span class="placeholder col-12 mb-2"></span>
        <span class="placeholder col-10 mb-2"></span>
        <span class="placeholder col-8"></span>
    </div>
</div>
