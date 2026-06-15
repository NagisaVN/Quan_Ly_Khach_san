@extends('layouts.app')

@section('title', 'Chi tiết Tiện ích')
@section('page-title', 'Chi tiết Tiện ích')

@section('content')
    <x-adminlte-card>
        <dl class="row">
            <dt class="col-sm-3">Tên</dt><dd class="col-sm-9">{{ $amenity->name ?? '—' }}</dd>
<dt class="col-sm-3">Icon</dt><dd class="col-sm-9">{{ $amenity->icon ?? '—' }}</dd>
<dt class="col-sm-3">Trạng thái</dt><dd class="col-sm-9">@if($amenity->is_active) <span class="badge text-bg-success">Hoạt động</span> @else <span class="badge text-bg-secondary">Ngừng</span> @endif</dd>

        </dl>
        <div class="mt-3">
            @can('update', $amenity)
                <a href="{{ route('rooms.amenities.edit', $amenity) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Sửa</a>
            @endcan
            <a href="{{ route('rooms.amenities.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
        </div>
    </x-adminlte-card>
@endsection