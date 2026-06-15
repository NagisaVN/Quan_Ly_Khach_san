@extends('layouts.app')
@section('title', 'Chi tiết bảo trì')
@section('page-title', '{{ $maintenanceRequest->title }}')
@section('content')
<x-adminlte-card>
<p>Phòng: {{ $maintenanceRequest->room?->room_number }} | Trạng thái: {{ $maintenanceRequest->status }}</p>
<a href="{{ route('maintenance.requests.edit', $maintenanceRequest) }}" class="btn btn-warning">Cập nhật</a>
</x-adminlte-card>
@endsection
