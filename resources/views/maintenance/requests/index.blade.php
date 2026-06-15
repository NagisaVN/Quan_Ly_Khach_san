@extends('layouts.app')
@section('title', 'Bảo trì')
@section('page-title', 'Yêu cầu bảo trì')
@section('content')
<x-adminlte-card>
<a href="{{ route('maintenance.requests.create') }}" class="btn btn-primary mb-3">Tạo yêu cầu</a>
<table class="table"><thead><tr><th>Tiêu đề</th><th>Phòng</th><th>Trạng thái</th></tr></thead>
<tbody>@foreach($requests as $r)<tr><td><a href="{{ route('maintenance.requests.show', $r) }}">{{ $r->title }}</a></td><td>{{ $r->room?->room_number }}</td><td>{{ $r->status }}</td></tr>@endforeach</tbody></table>
{{ $requests->links() }}
</x-adminlte-card>
@endsection
