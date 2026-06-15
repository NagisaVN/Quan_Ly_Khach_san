@extends('layouts.app')
@section('title', 'Cập nhật bảo trì')
@section('page-title', 'Cập nhật bảo trì')
@section('content')
<x-adminlte-card><form method="POST" action="{{ route('maintenance.requests.update', $maintenanceRequest) }}">@csrf @method('PUT')
<div class="mb-3"><label>Trạng thái</label><select name="status" class="form-select"><option value="pending">Chờ</option><option value="in_progress">Đang xử lý</option><option value="completed">Hoàn thành</option></select></div>
<button class="btn btn-primary">Lưu</button></form></x-adminlte-card>
@endsection
