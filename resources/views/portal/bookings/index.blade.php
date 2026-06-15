@extends('layouts.guest')
@section('title', 'Booking của tôi')
@section('page-title', 'Booking của tôi')
@section('content')
<div class="card"><div class="card-body">
    <a href="{{ route('portal.bookings.create') }}" class="btn btn-primary mb-3">Đặt phòng mới</a>
    <table class="table"><thead><tr><th>Mã</th><th>Check-in</th><th>Trạng thái</th></tr></thead>
    <tbody>@foreach($bookings as $b)<tr><td><a href="{{ route('portal.bookings.show', $b) }}">{{ $b->booking_code }}</a></td><td>{{ $b->check_in_date->format('d/m/Y') }}</td><td>{{ $b->status->value }}</td></tr>@endforeach</tbody></table>
    {{ $bookings->links() }}
</div></div>
@endsection
