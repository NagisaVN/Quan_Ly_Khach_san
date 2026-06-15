<!-- Reusable Card Component -->
<div class="card {{ $class ?? '' }}">
    @if (isset($title))
    <div class="card-header bg-light border-bottom">
        <h5 class="card-title mb-0">
            @if (isset($icon))
            <i class="fas fa-{{ $icon }} me-2"></i>
            @endif
            {{ $title }}
        </h5>
    </div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
    @if (isset($footer))
    <div class="card-footer bg-light">
        {{ $footer }}
    </div>
    @endif
</div>
