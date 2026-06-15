@extends('layouts.app')
@section('title', $title ?? 'Module')
@section('page-title', $title ?? 'Module')
@section('content')
<x-adminlte-card>
    @if(isset($createRoute))<a href="{{ $createRoute }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Thêm</a>@endif
    @if(isset($items))
        <table class="table table-striped"><thead><tr>@foreach($columns as $col)<th>{{ $col['label'] }}</th>@endforeach<th></th></tr></thead>
        <tbody>@forelse($items as $row)<tr>@foreach($columns as $col)<td>{{ data_get($row, $col['key']) }}</td>@endforeach
        <td>@if(isset($showRoute))<a href="{{ $showRoute($row) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>@endif</td></tr>@empty<tr><td colspan="10" class="text-center">Chưa có dữ liệu</td></tr>@endforelse</tbody></table>
        @if(method_exists($items, 'links')){{ $items->links() }}@endif
    @else @yield('module-content') @endif
</x-adminlte-card>
@endsection
