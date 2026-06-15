@extends('layouts.app')
@section('title', 'Thêm khách hàng')
@section('page-title', 'Thêm khách hàng')
@section('content')
<x-adminlte-card>
<form method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data">@csrf
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Họ *</label><input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required></div>
<div class="col-md-6"><label class="form-label">Tên *</label><input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required></div>
<div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}"></div>
<div class="col-md-6"><label class="form-label">Điện thoại</label><input type="text" name="phone" class="form-control" value="{{ old('phone') }}"></div>
<div class="col-md-6"><label class="form-label">Số CCCD/CMND</label><input type="text" name="id_number" class="form-control" value="{{ old('id_number') }}"></div>
<div class="col-md-6"><label class="form-label">Loại giấy tờ</label><select name="id_type" class="form-select"><option value="cccd" @selected(old('id_type')=='cccd')>CCCD</option><option value="cmnd" @selected(old('id_type')=='cmnd')>CMND</option><option value="passport" @selected(old('id_type')=='passport')>Hộ chiếu</option></select></div>
<div class="col-md-6"><label class="form-label">Ảnh CCCD</label><input type="file" name="cccd_image" class="form-control" accept="image/jpeg,image/png"></div>
<div class="col-md-6"><label class="form-label">Ngày sinh</label><input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}"></div>
<div class="col-md-6"><label class="form-label">Giới tính</label><select name="gender" class="form-select"><option value="">-- Chọn --</option><option value="male" @selected(old('gender')=='male')>Nam</option><option value="female" @selected(old('gender')=='female')>Nữ</option><option value="other" @selected(old('gender')=='other')>Khác</option></select></div>
<div class="col-md-6"><label class="form-label">Quốc tịch</label><input type="text" name="nationality" class="form-control" value="{{ old('nationality', 'Việt Nam') }}"></div>
<div class="col-12"><label class="form-label">Địa chỉ</label><textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea></div>
<div class="col-12"><label class="form-label">Ghi chú</label><textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea></div>
<div class="col-md-6"><div class="form-check mt-2"><input type="checkbox" name="is_active" value="1" class="form-check-input" checked><label class="form-check-label">Hoạt động</label></div></div>
</div>
<div class="mt-3"><button type="submit" class="btn btn-primary">Lưu</button><a href="{{ route('customers.index') }}" class="btn btn-secondary">Hủy</a></div>
</form>
</x-adminlte-card>
@endsection
