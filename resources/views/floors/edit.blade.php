@extends('layouts.app')
@section('title', 'Sửa tầng')
@section('page-title', 'Sửa tầng')
@section('content')
<x-adminlte-card>
<form method="POST" action="{{ route('rooms.floors.update', $floor) }}">@csrf @method('PUT')
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Tên tầng *</label><input type="text" name="name" class="form-control" value="{{ old('name', $floor->name) }}" required></div>
<div class="col-md-6"><label class="form-label">Số tầng *</label><input type="number" name="floor_number" class="form-control" value="{{ old('floor_number', $floor->floor_number) }}" required></div>
<div class="col-12"><label class="form-label">Mô tả</label><textarea name="description" class="form-control" rows="2">{{ old('description', $floor->description) }}</textarea></div>
<div class="col-md-6"><div class="form-check mt-2"><input type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $floor->is_active))><label class="form-check-label">Hoạt động</label></div></div>
</div>
<div class="mt-3"><button type="submit" class="btn btn-primary">Cập nhật</button><a href="{{ route('rooms.floors.show', $floor) }}" class="btn btn-secondary">Hủy</a></div>
</form>
</x-adminlte-card>
@endsection
