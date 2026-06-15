@extends('layouts.app')
@section('title', 'Sửa người dùng')
@section('page-title', 'Sửa người dùng')
@section('content')
<x-adminlte-card>
<form method="POST" action="{{ route('system.users.update', $user) }}">@csrf @method('PUT')
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Họ tên *</label><input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required></div>
<div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
<div class="col-md-6"><label class="form-label">Mật khẩu mới</label><input type="password" name="password" class="form-control" placeholder="Để trống nếu không đổi"></div>
<div class="col-md-6"><label class="form-label">Xác nhận mật khẩu</label><input type="password" name="password_confirmation" class="form-control"></div>
<div class="col-md-6"><label class="form-label">Điện thoại</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}"></div>
<div class="col-md-6"><label class="form-label">Công ty</label><select name="company_id" class="form-select select2"><option value="">-- Chọn --</option>@foreach($companies as $c)<option value="{{ $c->id }}" @selected(old('company_id', $user->company_id)==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Vai trò *</label><select name="role" class="form-select select2" required>@foreach($roles as $r)<option value="{{ $r }}" @selected(old('role', $user->roles->first()?->name)==$r)>{{ $r }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Chi nhánh</label><select name="branch_ids[]" class="form-select select2" multiple>@foreach($branches as $b)<option value="{{ $b->id }}" @selected(collect(old('branch_ids', $user->branches->pluck('id')))->contains($b->id))>{{ $b->name }}</option>@endforeach</select></div>
<div class="col-md-6"><div class="form-check mt-4"><input type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $user->is_active))><label class="form-check-label">Hoạt động</label></div></div>
</div>
<div class="mt-3"><button type="submit" class="btn btn-primary">Cập nhật</button><a href="{{ route('system.users.show', $user) }}" class="btn btn-secondary">Hủy</a></div>
</form>
</x-adminlte-card>
@endsection
