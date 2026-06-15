@extends('layouts.app')
@section('title', 'Thêm tiện ích')
@section('page-title', 'Thêm tiện ích')
@section('content')
<x-adminlte-card>
<form method="POST" action="{{ route('rooms.amenities.store') }}">@csrf
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Tên *</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
<div class="col-md-6"><label class="form-label">Icon</label><input type="text" name="icon" class="form-control" value="{{ old('icon') }}" placeholder="wifi, tv..."></div>
<div class="col-12"><label class="form-label">Mô tả</label><textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea></div>
<div class="col-md-6"><div class="form-check mt-2"><input type="checkbox" name="is_active" value="1" class="form-check-input" checked><label class="form-check-label">Hoạt động</label></div></div>
</div>
<div class="mt-3"><button type="submit" class="btn btn-primary">Lưu</button><a href="{{ route('rooms.amenities.index') }}" class="btn btn-secondary">Hủy</a></div>
</form>
</x-adminlte-card>
@endsection
