@extends('layouts.app')
@section('title', 'Sơ đồ phòng')
@section('page-title', 'Sơ đồ phòng')

@section('content')
    <x-adminlte-card title="Sơ đồ phòng theo tầng" icon="fas fa-th">
        <div class="mb-3 d-flex gap-2 flex-wrap">
            <span class="badge text-bg-success">Trống</span>
            <span class="badge text-bg-danger">Đang ở</span>
            <span class="badge text-bg-warning">Đã đặt</span>
            <span class="badge text-bg-secondary">Bảo trì</span>
            <span class="badge text-bg-info">Đang dọn</span>
        </div>

        @forelse($floors as $floor)
            <h5 class="mt-3"><i class="fas fa-layer-group me-1"></i> {{ $floor->name }}</h5>
            <div class="row g-2 mb-4">
                @forelse($floor->rooms as $room)
                    @php $color = $statusColors[$room->status->value] ?? 'secondary'; @endphp
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="card border-{{ $color }} text-center h-100">
                            <div class="card-body p-2">
                                <div class="fw-bold fs-5">{{ $room->room_number }}</div>
                                <small class="text-muted d-block">{{ $room->roomType?->name }}</small>
                                <span class="badge text-bg-{{ $color }} mt-1">{{ $room->status->value }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-muted">Chưa có phòng trên tầng này.</div>
                @endforelse
            </div>
        @empty
            <p class="text-muted mb-0">Chưa có dữ liệu tầng/phòng cho chi nhánh hiện tại.</p>
        @endforelse
    </x-adminlte-card>
@endsection
