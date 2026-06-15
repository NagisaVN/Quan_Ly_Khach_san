@extends('layouts.app')
@section('title', 'Sửa công ty')
@section('page-title', 'Sửa công ty')
@section('content')
<x-adminlte-card>
<form method="POST" action="{{ route('enterprise.companies.update', $company) }}">@csrf @method('PUT')
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Tên công ty *</label><input type="text" name="name" class="form-control" value="{{ old('name', $company->name) }}" required></div>
<div class="col-md-6"><label class="form-label">Mã công ty *</label><input type="text" name="code" class="form-control" value="{{ old('code', $company->code) }}" required></div>
<div class="col-md-6"><label class="form-label">Mã số thuế</label><input type="text" name="tax_code" class="form-control" value="{{ old('tax_code', $company->tax_code) }}"></div>
<div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $company->email) }}"></div>
<div class="col-md-6"><label class="form-label">Điện thoại</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone) }}"></div>
<div class="col-md-6"><label class="form-label">Website</label><input type="url" name="website" class="form-control" value="{{ old('website', $company->website) }}"></div>
<div class="col-12"><label class="form-label">Địa chỉ</label><textarea name="address" class="form-control" rows="2">{{ old('address', $company->address) }}</textarea></div>
<div class="col-md-6"><div class="form-check mt-4"><input type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $company->is_active))><label class="form-check-label">Hoạt động</label></div></div>
</div>
<div class="mt-3"><button type="submit" class="btn btn-primary">Cập nhật</button><a href="{{ route('enterprise.companies.show', $company) }}" class="btn btn-secondary">Hủy</a></div>
</form>
</x-adminlte-card>
@endsection
