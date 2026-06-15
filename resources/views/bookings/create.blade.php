@extends('layouts.app')

@section('title', 'Tạo booking')
@section('page-title', 'Tạo booking mới')

@section('content')
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('bookings.store') }}" id="booking-form">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Thông tin đặt phòng</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Khách hàng</label>
                                <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                    <option value="">-- Chọn khách hàng --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                                            {{ $customer->full_name }} ({{ $customer->phone }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Người lớn</label>
                                <input type="number" name="adults" class="form-control" value="{{ old('adults', 1) }}" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Trẻ em</label>
                                <input type="number" name="children" class="form-control" value="{{ old('children', 0) }}" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Check-in</label>
                                <input type="date" name="check_in_date" id="check_in_date" class="form-control @error('check_in_date') is-invalid @enderror"
                                       value="{{ old('check_in_date', now()->toDateString()) }}" required>
                                @error('check_in_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Check-out</label>
                                <input type="date" name="check_out_date" id="check_out_date" class="form-control @error('check_out_date') is-invalid @enderror"
                                       value="{{ old('check_out_date', now()->addDay()->toDateString()) }}" required>
                                @error('check_out_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Loại phòng</label>
                                <select id="room_type_id" class="form-select">
                                    <option value="">Tất cả loại phòng</option>
                                    @foreach($roomTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }} ({{ number_format($type->base_price, 0, ',', '.') }} đ)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Yêu cầu đặc biệt</label>
                                <textarea name="special_requests" class="form-control" rows="2">{{ old('special_requests') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Phòng trống</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-check-availability">
                            <i class="fas fa-search"></i> Kiểm tra
                        </button>
                    </div>
                    <div class="card-body" id="available-rooms-list">
                        <p class="text-muted mb-0">Chọn ngày và nhấn "Kiểm tra" để xem phòng trống.</p>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary w-100" id="btn-submit" disabled>
                            <i class="fas fa-save me-1"></i> Tạo booking
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const listEl = document.getElementById('available-rooms-list');
    const submitBtn = document.getElementById('btn-submit');
    const selected = new Set(@json(old('room_ids', [])).map(String));

    function renderRooms(rooms) {
        if (!rooms.length) {
            listEl.innerHTML = '<p class="text-danger mb-0">Không có phòng trống trong khoảng thời gian này.</p>';
            submitBtn.disabled = true;
            return;
        }

        listEl.innerHTML = rooms.map(room => `
            <div class="form-check mb-2">
                <input class="form-check-input room-checkbox" type="checkbox" name="room_ids[]"
                       value="${room.id}" id="room_${room.id}" ${selected.has(String(room.id)) ? 'checked' : ''}>
                <label class="form-check-label" for="room_${room.id}">
                    <strong>${room.room_number}</strong> — ${room.room_type}
                    <small class="text-muted d-block">${Number(room.base_price).toLocaleString('vi-VN')} đ/đêm</small>
                </label>
            </div>
        `).join('');

        listEl.querySelectorAll('.room-checkbox').forEach(cb => {
            cb.addEventListener('change', updateSubmit);
        });
        updateSubmit();
    }

    function updateSubmit() {
        const checked = listEl.querySelectorAll('.room-checkbox:checked');
        submitBtn.disabled = checked.length === 0;
    }

    document.getElementById('btn-check-availability').addEventListener('click', function () {
        const params = new URLSearchParams({
            check_in_date: document.getElementById('check_in_date').value,
            check_out_date: document.getElementById('check_out_date').value,
            room_type_id: document.getElementById('room_type_id').value
        });

        listEl.innerHTML = '<p class="text-muted">Đang tải...</p>';

        fetch(`{{ route('bookings.availability') }}?${params}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => renderRooms(res.data?.available_rooms || []))
        .catch(() => {
            listEl.innerHTML = '<p class="text-danger">Lỗi khi kiểm tra phòng trống.</p>';
        });
    });

    if (selected.size) {
        document.getElementById('btn-check-availability').click();
    }
});
</script>
@endpush
