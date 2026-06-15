<!-- Breadcrumb Navigation -->
@if (isset($breadcrumbs) && count($breadcrumbs) > 0)
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0 ps-3 pe-3 pt-2">
        @foreach ($breadcrumbs as $name => $url)
            @if ($loop->last)
                <li class="breadcrumb-item active">{{ $name }}</li>
            @else
                <li class="breadcrumb-item"><a href="{{ $url }}" class="text-decoration-none">{{ $name }}</a></li>
            @endif
        @endforeach
    </ol>
</nav>
@endif
