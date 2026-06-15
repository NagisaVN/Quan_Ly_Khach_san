@extends('layouts.app')

@section('title', 'Chi tiết Người dùng')
@section('page-title', 'Chi tiết Người dùng')

@section('content')
    <x-adminlte-card>
        <dl class="row">
            <dt class="col-sm-3">Tên</dt><dd class="col-sm-9">{{ $user->name ?? '—' }}</dd>
<dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $user->email ?? '—' }}</dd>
<dt class="col-sm-3">Vai trò</dt><dd class="col-sm-9">{{ $user->roles->pluck('name')->join(', ') }}</dd>
<dt class="col-sm-3">Trạng thái</dt><dd class="col-sm-9">@if($user->is_active) <span class="badge text-bg-success">Hoạt động</span> @else <span class="badge text-bg-secondary">Ngừng</span> @endif</dd>

        </dl>
        <div class="mt-3">
            @can('update', $user)
                <a href="{{ route('system.users.edit', $user) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Sửa</a>
            @endcan
            <a href="{{ route('system.users.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
        </div>
    </x-adminlte-card>
@endsection