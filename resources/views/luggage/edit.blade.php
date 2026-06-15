@extends('layouts.app')
@section('title', 'Sửa hành lý')
@section('page-title', 'Sửa hành lý')
@section('content')
<x-adminlte-card>
<form method="POST" action="{{ route('luggage.update', $luggage) }}">@csrf @method('PUT')
    <div class="mb-3"><label>Trạng thái</label><select name="status" class="form-select"><option value="stored">Đang lưu</option><option value="retrieved">Đã lấy</option></select></div>
    <div class="mb-3"><label>Vị trí</label><input type="text" name="storage_location" class="form-control" value="{{ $luggage->storage_location }}"></div>
    <button class="btn btn-primary">Cập nhật</button>
</form>
</x-adminlte-card>
@endsection
