@extends('layouts.app')

@section('title', 'Chi tiết Phòng')
@section('page-title', 'Chi tiết Phòng')

@section('content')
    <x-adminlte-card>
        <dl class="row">
            <dt class="col-sm-3">Số phòng</dt><dd class="col-sm-9">{{ $room->room_number ?? '—' }}</dd>
<dt class="col-sm-3">Loại</dt><dd class="col-sm-9">{{ $room->roomType?->name ?? '—' }}</dd>
<dt class="col-sm-3">Tầng</dt><dd class="col-sm-9">{{ $room->floor?->name ?? '—' }}</dd>
<dt class="col-sm-3">Trạng thái</dt><dd class="col-sm-9">{{ $room->status ?? '—' }}</dd>

        </dl>
        <div class="mt-3">
            @can('update', $room)
                <a href="{{ route('rooms.rooms.edit', $room) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Sửa</a>
            @endcan
            <a href="{{ route('rooms.rooms.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
        </div>
    </x-adminlte-card>
@endsection