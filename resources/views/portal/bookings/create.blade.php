@extends('layouts.guest')
@section('title', 'Đặt phòng')
@section('page-title', 'Đặt phòng online')
@section('content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('portal.bookings.store') }}">@csrf
    <div class="mb-3"><label>Check-in</label><input type="date" name="check_in_date" class="form-control" required></div>
    <div class="mb-3"><label>Check-out</label><input type="date" name="check_out_date" class="form-control" required></div>
    <div class="mb-3"><label>Số người lớn</label><input type="number" name="adults" class="form-control" value="2" min="1"></div>
    <div class="mb-3"><label>ID phòng (demo)</label><input type="text" name="room_ids[]" class="form-control" placeholder="Nhập room id" required></div>
    <button type="submit" class="btn btn-primary">Đặt phòng</button>
</form>
</div></div>
@endsection
