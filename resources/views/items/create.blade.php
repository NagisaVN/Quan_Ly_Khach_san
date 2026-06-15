@extends('layouts.app')
@section('title', 'Thêm dịch vụ')
@section('page-title', 'Thêm dịch vụ')
@section('content')
<x-adminlte-card>
<form method="POST" action="{{ route('services.items.store') }}">@csrf
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Danh mục *</label><select name="service_category_id" class="form-select select2" required>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('service_category_id')==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Tên *</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
<div class="col-md-4"><label class="form-label">Mã</label><input type="text" name="code" class="form-control" value="{{ old('code') }}"></div>
<div class="col-md-4"><label class="form-label">Đơn giá *</label><input type="number" name="unit_price" class="form-control" value="{{ old('unit_price', 0) }}" min="0" required></div>
<div class="col-md-4"><label class="form-label">Đơn vị</label><input type="text" name="unit" class="form-control" value="{{ old('unit', 'lần') }}"></div>
<div class="col-12"><label class="form-label">Mô tả</label><textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea></div>
<div class="col-md-6"><div class="form-check mt-2"><input type="checkbox" name="is_active" value="1" class="form-check-input" checked><label class="form-check-label">Hoạt động</label></div></div>
</div>
<div class="mt-3"><button type="submit" class="btn btn-primary">Lưu</button><a href="{{ route('services.items.index') }}" class="btn btn-secondary">Hủy</a></div>
</form>
</x-adminlte-card>
@endsection
