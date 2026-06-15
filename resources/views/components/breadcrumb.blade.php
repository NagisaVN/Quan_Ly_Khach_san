@props(['items' => []])

<ol class="breadcrumb float-sm-end mb-0">
    @foreach($items as $item)
        @if($loop->last)
            <li class="breadcrumb-item active">{{ $item['label'] }}</li>
        @else
            <li class="breadcrumb-item">
                <a href="{{ $item['url'] ?? '#' }}">{{ $item['label'] }}</a>
            </li>
        @endif
    @endforeach
</ol>
