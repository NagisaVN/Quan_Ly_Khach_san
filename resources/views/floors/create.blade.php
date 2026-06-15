@extends('layouts.app')
@section('title', 'Thêm tầng')
@section('page-title', 'Thêm tầng')
@section('content')
<x-adminlte-card>
<form method="POST" action="{{ route('rooms.floors.store') }}">@csrf
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Tên tầng *</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
<div class="col-md-6"><label class="form-label">Số tầng *</label><input type="number" name="floor_number" class="form-control" value="{{ old('floor_number') }}" required></div>
<div class="col-12"><label class="form-label">Mô tả</label><textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea></div>
<div class="col-md-6"><div class="form-check mt-2"><input type="checkbox" name="is_active" value="1" class="form-check-input" checked><label class="form-check-label">Hoạt động</label></div></div>
</div>
<div class="mt-3"><button type="submit" class="btn btn-primary">Lưu</button><a href="{{ route('rooms.floors.index') }}" class="btn btn-secondary">Hủy</a></div>
</form>
</x-adminlte-card>
@endsection
