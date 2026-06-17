@extends('layouts.app')

@section('title', 'Booking '.$booking->booking_code)
@section('page-title', 'Chi tiết booking')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">{{ $booking->booking_code }}</h5>
                    <span class="badge text-bg-primary">{{ $booking->status->value }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Khách hàng:</strong> {{ $booking->customer->full_name }}</p>
                            <p><strong>Điện thoại:</strong> {{ $booking->customer->phone }}</p>
                            <p><strong>Nguồn:</strong> {{ $booking->source }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Check-in:</strong> {{ $booking->check_in_date->format('d/m/Y') }}</p>
                            <p><strong>Check-out:</strong> {{ $booking->check_out_date->format('d/m/Y') }}</p>
                            <p><strong>Tổng tiền:</strong> {{ number_format($booking->total_amount, 0, ',', '.') }} đ</p>
                        </div>
                    </div>

                    <h6 class="mt-3">Phòng đã đặt</h6>
                    <table class="table table-sm">
                        <thead><tr><th>Phòng</th><th>Loại</th><th>Đêm</th><th>Giá/đêm</th><th>Thành tiền</th></tr></thead>
                        <tbody>
                            @foreach($booking->bookingRooms as $br)
                                <tr>
                                    <td>{{ $br->room->room_number }}</td>
                                    <td>{{ $br->room->roomType->name }}</td>
                                    <td>{{ $br->nights }}</td>
                                    <td>{{ number_format($br->rate_snapshot, 0, ',', '.') }} đ</td>
                                    <td>{{ number_format($br->total_amount, 0, ',', '.') }} đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($booking->serviceItems->count())
                        <h6 class="mt-3">Dịch vụ</h6>
                        <table class="table table-sm">
                            <thead><tr><th>Dịch vụ</th><th>SL</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead>
                            <tbody>
                                @foreach($booking->serviceItems as $item)
                                    <tr>
                                        <td>{{ $item->service->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->unit_price, 0, ',', '.') }} đ</td>
                                        <td>{{ number_format($item->total_amount, 0, ',', '.') }} đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            @if($booking->status->value === 'checked_in' && $services->count())
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Thêm dịch vụ</h5></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('bookings.services', $booking) }}" class="row g-2">
                            @csrf
                            <div class="col-md-6">
                                <select name="service_id" class="form-select" required>
                                    <option value="">-- Chọn dịch vụ --</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }} ({{ number_format($service->unit_price, 0, ',', '.') }} đ)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-outline-primary w-100">Thêm</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Thao tác</h5></div>
                <div class="card-body d-grid gap-2">
                    @if($booking->status->value === 'confirmed')
                        @can('checkIn', $booking)
                            <form method="POST" action="{{ route('bookings.check-in', $booking) }}">
                                @csrf
                                <button class="btn btn-success w-100"><i class="fas fa-door-open me-1"></i> Check-in</button>
                            </form>
                        @endcan
                        @can('cancel', $booking)
                            <button class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">Hủy booking</button>
                        @endcan
                    @endif

                    @if($booking->status->value === 'checked_in')
                        @php $invoice = $booking->invoices->first(); @endphp
                        @if($invoice)
                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-file-invoice me-1"></i> Xem hóa đơn
                            </a>
                            @if($invoice->balance > 0)
                                <a href="{{ route('payments.create', $invoice) }}" class="btn btn-warning w-100">
                                    <i class="fas fa-credit-card me-1"></i> Thanh toán
                                </a>
                            @endif
                        @endif
                        @can('checkOut', $booking)
                            <form method="POST" action="{{ route('bookings.check-out', $booking) }}">
                                @csrf
                                <button class="btn btn-primary w-100" @if($invoice && $invoice->balance > 0) disabled title="Cần thanh toán đủ trước khi check-out" @endif>
                                    <i class="fas fa-door-closed me-1"></i> Check-out
                                </button>
                            </form>
                        @endcan
                    @endif

                    <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary w-100">Quay lại</a>
                </div>
            </div>

            @if(in_array($booking->status->value, ['confirmed', 'checked_in']))
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Gia hạn</h5></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('bookings.extend', $booking) }}">
                            @csrf
                            <input type="date" name="new_check_out_date" class="form-control mb-2"
                                   min="{{ $booking->check_out_date->copy()->addDay()->toDateString() }}" required>
                            <button class="btn btn-sm btn-outline-primary w-100">Gia hạn</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Hủy booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="cancel_reason" class="form-control" rows="3" placeholder="Lý do hủy..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                </div>
            </form>
        </div>
    </div>
@endsection
