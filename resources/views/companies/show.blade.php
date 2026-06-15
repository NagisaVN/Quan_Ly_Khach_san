@extends('layouts.app')

@section('title', 'Chi tiết Công ty')
@section('page-title', 'Chi tiết Công ty')

@section('content')
    <x-adminlte-card>
        <dl class="row">
            <dt class="col-sm-3">Tên</dt><dd class="col-sm-9">{{ $company->name ?? '—' }}</dd>
<dt class="col-sm-3">Mã</dt><dd class="col-sm-9">{{ $company->code ?? '—' }}</dd>
<dt class="col-sm-3">Điện thoại</dt><dd class="col-sm-9">{{ $company->phone ?? '—' }}</dd>
<dt class="col-sm-3">Trạng thái</dt><dd class="col-sm-9">@if($company->is_active) <span class="badge text-bg-success">Hoạt động</span> @else <span class="badge text-bg-secondary">Ngừng</span> @endif</dd>

        </dl>
        <div class="mt-3">
            @can('update', $company)
                <a href="{{ route('enterprise.companies.edit', $company) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Sửa</a>
            @endcan
            <a href="{{ route('enterprise.companies.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
        </div>
    </x-adminlte-card>
@endsection