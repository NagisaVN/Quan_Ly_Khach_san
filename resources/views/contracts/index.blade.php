@extends('layouts.app')
@section('title', 'Hợp đồng')
@section('page-title', 'Quản lý hợp đồng')
@section('content')
<x-adminlte-card>
<a href="{{ route('contracts.create') }}" class="btn btn-primary mb-3">Thêm hợp đồng</a>
<table class="table"><thead><tr><th>Số HĐ</th><th>Tiêu đề</th><th>Trạng thái</th></tr></thead>
<tbody>@foreach($contracts as $c)<tr><td><a href="{{ route('contracts.show', $c) }}">{{ $c->contract_number }}</a></td><td>{{ $c->title }}</td><td>{{ $c->status }}</td></tr>@endforeach</tbody></table>
{{ $contracts->links() }}
</x-adminlte-card>
@endsection
