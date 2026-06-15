@extends('layouts.app')
@section('title', 'Sửa chi nhánh')
@section('page-title', 'Sửa chi nhánh')
@section('content')
<x-adminlte-card>
<form method="POST" action="{{ route('enterprise.branches.update', $branch) }}">@csrf @method('PUT')
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Công ty *</label><select name="company_id" class="form-select select2" required>@foreach($companies as $c)<option value="{{ $c->id }}" @selected(old('company_id', $branch->company_id)==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Tên chi nhánh *</label><input type="text" name="name" class="form-control" value="{{ old('name', $branch->name) }}" required></div>
<div class="col-md-6"><label class="form-label">Mã chi nhánh *</label><input type="text" name="code" class="form-control" value="{{ old('code', $branch->code) }}" required></div>
<div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $branch->email) }}"></div>
<div class="col-md-6"><label class="form-label">Điện thoại</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $branch->phone) }}"></div>
<div class="col-12"><label class="form-label">Địa chỉ</label><textarea name="address" class="form-control" rows="2">{{ old('address', $branch->address) }}</textarea></div>
<div class="col-md-6"><div class="form-check mt-4"><input type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $branch->is_active))><label class="form-check-label">Hoạt động</label></div></div>
</div>
<div class="mt-3"><button type="submit" class="btn btn-primary">Cập nhật</button><a href="{{ route('enterprise.branches.show', $branch) }}" class="btn btn-secondary">Hủy</a></div>
</form>
</x-adminlte-card>
@endsection
