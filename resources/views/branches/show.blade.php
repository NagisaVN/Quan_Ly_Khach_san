@extends('layouts.app')

@section('title', 'Chi tiết Chi nhánh')
@section('page-title', 'Chi tiết Chi nhánh')

@section('content')
    <x-adminlte-card>
        <dl class="row">
            <dt class="col-sm-3">Tên</dt><dd class="col-sm-9">{{ $branch->name ?? '—' }}</dd>
<dt class="col-sm-3">Mã</dt><dd class="col-sm-9">{{ $branch->code ?? '—' }}</dd>
<dt class="col-sm-3">Công ty</dt><dd class="col-sm-9">{{ $branch->company?->name ?? '—' }}</dd>
<dt class="col-sm-3">Trạng thái</dt><dd class="col-sm-9">@if($branch->is_active) <span class="badge text-bg-success">Hoạt động</span> @else <span class="badge text-bg-secondary">Ngừng</span> @endif</dd>

        </dl>
        <div class="mt-3">
            @can('update', $branch)
                <a href="{{ route('enterprise.branches.edit', $branch) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Sửa</a>
            @endcan
            <a href="{{ route('enterprise.branches.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
        </div>
    </x-adminlte-card>
@endsection