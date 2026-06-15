@extends('layouts.app')

@section('title', 'Chi tiết Loại phòng')
@section('page-title', 'Chi tiết Loại phòng')

@section('content')
    <x-adminlte-card>
        <dl class="row">
            <dt class="col-sm-3">Tên</dt><dd class="col-sm-9">{{ $roomType->name ?? '—' }}</dd>
<dt class="col-sm-3">Mã</dt><dd class="col-sm-9">{{ $roomType->code ?? '—' }}</dd>
<dt class="col-sm-3">Giá cơ bản</dt><dd class="col-sm-9">{{ $roomType->base_price ?? '—' }}</dd>
<dt class="col-sm-3">Trạng thái</dt><dd class="col-sm-9">@if($roomType->is_active) <span class="badge text-bg-success">Hoạt động</span> @else <span class="badge text-bg-secondary">Ngừng</span> @endif</dd>

        </dl>
        <div class="mt-3">
            @can('update', $roomType)
                <a href="{{ route('rooms.room-types.edit', $roomType) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Sửa</a>
            @endcan
            <a href="{{ route('rooms.room-types.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
        </div>
    </x-adminlte-card>
@endsection