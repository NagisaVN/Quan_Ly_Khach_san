@extends('layouts.app')
@section('title', 'Sửa loại phòng')
@section('page-title', 'Sửa loại phòng')
@section('content')
<x-adminlte-card>
<form method="POST" action="{{ route('rooms.room-types.update', $roomType) }}">@csrf @method('PUT')
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Tên *</label><input type="text" name="name" class="form-control" value="{{ old('name', $roomType->name) }}" required></div>
<div class="col-md-6"><label class="form-label">Mã *</label><input type="text" name="code" class="form-control" value="{{ old('code', $roomType->code) }}" required></div>
<div class="col-md-4"><label class="form-label">Giá cơ bản *</label><input type="number" name="base_price" class="form-control" value="{{ old('base_price', $roomType->base_price) }}" min="0" required></div>
<div class="col-md-4"><label class="form-label">Sức chứa</label><input type="number" name="max_occupancy" class="form-control" value="{{ old('max_occupancy', $roomType->max_occupancy) }}" min="1"></div>
<div class="col-md-4"><label class="form-label">Diện tích (m²)</label><input type="number" name="area_sqm" class="form-control" value="{{ old('area_sqm', $roomType->area_sqm) }}" step="0.01"></div>
<div class="col-12"><label class="form-label">Mô tả</label><textarea name="description" class="form-control" rows="2">{{ old('description', $roomType->description) }}</textarea></div>
<div class="col-md-6"><div class="form-check mt-2"><input type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $roomType->is_active))><label class="form-check-label">Hoạt động</label></div></div>
</div>
<div class="mt-3"><button type="submit" class="btn btn-primary">Cập nhật</button><a href="{{ route('rooms.room-types.show', $roomType) }}" class="btn btn-secondary">Hủy</a></div>
</form>
</x-adminlte-card>
@endsection
