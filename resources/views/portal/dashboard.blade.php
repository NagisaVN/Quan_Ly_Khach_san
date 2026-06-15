@extends('layouts.guest')
@section('title', 'Portal')
@section('page-title', 'Trang khách hàng')
@section('content')
<div class="card"><div class="card-body">
    <p>Xin chào <strong>{{ $customer->full_name }}</strong></p>
    <a href="{{ route('portal.bookings.create') }}" class="btn btn-primary">Đặt phòng online</a>
    <h5 class="mt-4">Booking gần đây</h5>
    <ul>@forelse($bookings as $b)<li><a href="{{ route('portal.bookings.show', $b) }}">{{ $b->booking_code }}</a> — {{ $b->status->value }}</li>@empty<li>Chưa có booking</li>@endforelse</ul>
</div></div>
@endsection
