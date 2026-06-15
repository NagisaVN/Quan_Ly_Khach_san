@extends('layouts.app')

@section('title', 'Đặt phòng')
@section('page-title', 'Quản lý đặt phòng')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách booking</h5>
            @can('bookings.create')
                <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Tạo booking
                </a>
            @endcan
        </div>
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Mã booking, tên, SĐT..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        @foreach(['pending','confirmed','checked_in','checked_out','cancelled'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100">Lọc</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Khách hàng</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Phòng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td><strong>{{ $booking->booking_code }}</strong></td>
                                <td>{{ $booking->customer->full_name }}</td>
                                <td>{{ $booking->check_in_date->format('d/m/Y') }}</td>
                                <td>{{ $booking->check_out_date->format('d/m/Y') }}</td>
                                <td>{{ $booking->bookingRooms->pluck('room.room_number')->join(', ') }}</td>
                                <td>{{ number_format($booking->total_amount, 0, ',', '.') }} đ</td>
                                <td><span class="badge text-bg-secondary">{{ $booking->status->value }}</span></td>
                                <td>
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">Chưa có booking</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $bookings->withQueryString()->links() }}
        </div>
    </div>
@endsection
