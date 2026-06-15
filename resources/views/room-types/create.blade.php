@extends('layouts.app')
@section('title', 'Thêm loại phòng')
@section('page-title', 'Thêm loại phòng')
@section('content')
<x-adminlte-card>
<form method="POST" action="{{ route('rooms.room-types.store') }}">@csrf
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Tên *</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
<div class="col-md-6"><label class="form-label">Mã *</label><input type="text" name="code" class="form-control" value="{{ old('code') }}" required></div>
<div class="col-md-4"><label class="form-label">Giá cơ bản *</label><input type="number" name="base_price" class="form-control" value="{{ old('base_price', 0) }}" min="0" required></div>
<div class="col-md-4"><label class="form-label">Sức chứa</label><input type="number" name="max_occupancy" class="form-control" value="{{ old('max_occupancy', 2) }}" min="1"></div>
<div class="col-md-4"><label class="form-label">Diện tích (m²)</label><input type="number" name="area_sqm" class="form-control" value="{{ old('area_sqm') }}" step="0.01"></div>
<div class="col-12"><label class="form-label">Mô tả</label><textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea></div>
<div class="col-md-6"><div class="form-check mt-2"><input type="checkbox" name="is_active" value="1" class="form-check-input" checked><label class="form-check-label">Hoạt động</label></div></div>
</div>
<div class="mt-3"><button type="submit" class="btn btn-primary">Lưu</button><a href="{{ route('rooms.room-types.index') }}" class="btn btn-secondary">Hủy</a></div>
</form>
</x-adminlte-card>
@endsection
