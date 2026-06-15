@extends('layouts.app')
@section('title', 'Hành lý')
@section('page-title', 'Quản lý hành lý')
@section('content')
<x-adminlte-card>
    <a href="{{ route('luggage.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Thêm</a>
    <table class="table table-striped"><thead><tr><th>Mã tag</th><th>Khách</th><th>Trạng thái</th><th></th></tr></thead>
    <tbody>@foreach($items as $item)<tr><td>{{ $item->tag_code }}</td><td>{{ $item->customer?->full_name }}</td><td>{{ $item->status }}</td><td><a href="{{ route('luggage.show', $item) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td></tr>@endforeach</tbody></table>
    {{ $items->links() }}
</x-adminlte-card>
@endsection
