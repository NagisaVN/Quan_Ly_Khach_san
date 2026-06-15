@extends('layouts.app')

@section('title', 'Chi tiết Tầng')
@section('page-title', 'Chi tiết Tầng')

@section('content')
    <x-adminlte-card>
        <dl class="row">
            <dt class="col-sm-3">Tên</dt><dd class="col-sm-9">{{ $floor->name ?? '—' }}</dd>
<dt class="col-sm-3">Số tầng</dt><dd class="col-sm-9">{{ $floor->floor_number ?? '—' }}</dd>
<dt class="col-sm-3">Trạng thái</dt><dd class="col-sm-9">@if($floor->is_active) <span class="badge text-bg-success">Hoạt động</span> @else <span class="badge text-bg-secondary">Ngừng</span> @endif</dd>

        </dl>
        <div class="mt-3">
            @can('update', $floor)
                <a href="{{ route('rooms.floors.edit', $floor) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Sửa</a>
            @endcan
            <a href="{{ route('rooms.floors.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
        </div>
    </x-adminlte-card>
@endsection