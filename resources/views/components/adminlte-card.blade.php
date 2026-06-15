@props(['title' => '', 'icon' => null, 'tools' => null])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title)
        <div class="card-header">
            <h3 class="card-title">
                @if($icon)<i class="{{ $icon }} me-2"></i>@endif
                {{ $title }}
            </h3>
            @if($tools)
                <div class="card-tools">{{ $tools }}</div>
            @endif
        </div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
