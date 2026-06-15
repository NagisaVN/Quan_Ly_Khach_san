@extends('layouts.guest')
@section('title', 'Chi tiết booking')
@section('page-title', 'Booking {{ $booking->booking_code }}')
@section('content')
<div class="card"><div class="card-body">
    <p>Trạng thái: <strong>{{ $booking->status->value }}</strong></p>
    <p>{{ $booking->check_in_date->format('d/m/Y') }} → {{ $booking->check_out_date->format('d/m/Y') }}</p>
    <p>Tổng: {{ number_format($booking->total_amount, 0, ',', '.') }}đ</p>
    @if($booking->invoices->isNotEmpty())
        <h5>Hóa đơn</h5>
        @foreach($booking->invoices as $inv)
            <p>{{ $inv->invoice_number }} — Còn lại: {{ number_format($inv->balance, 0, ',', '.') }}đ</p>
        @endforeach
    @endif
</div></div>
@endsection
