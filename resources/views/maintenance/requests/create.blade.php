@extends('layouts.app')
@section('title', 'Tạo bảo trì')
@section('page-title', 'Tạo yêu cầu bảo trì')
@section('content')
<x-adminlte-card><form method="POST" action="{{ route('maintenance.requests.store') }}">@csrf
<div class="mb-3"><label>Phòng</label><select name="room_id" class="form-select">@foreach($rooms as $room)<option value="{{ $room->id }}">{{ $room->room_number }}</option>@endforeach</select></div>
<div class="mb-3"><label>Tiêu đề</label><input name="title" class="form-control" required></div>
<div class="mb-3"><label>Ưu tiên</label><select name="priority" class="form-select"><option value="low">Thấp</option><option value="medium" selected>Trung bình</option><option value="high">Cao</option></select></div>
<button class="btn btn-primary">Gửi</button></form></x-adminlte-card>
@endsection
