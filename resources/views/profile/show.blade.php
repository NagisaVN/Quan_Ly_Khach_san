@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <x-card>
                <div class="text-center">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle mb-3" width="150" height="150">
                    @else
                        <div class="avatar-placeholder bg-primary rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width: 150px; height: 150px; margin: 0 auto;">
                            <span class="text-white" style="font-size: 60px;">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <h5>{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->email }}</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Chỉnh sửa hồ sơ
                        </a>
                        <a href="{{ route('profile.security') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-lock"></i> Bảo mật
                        </a>
                    </div>
                </div>
            </x-card>
        </div>
        <div class="col-md-9">
            <x-card title="Thông tin hồ sơ" icon="user">
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Tên:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $user->name }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Email:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $user->email }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Điện thoại:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $user->phone ?? 'Chưa cập nhật' }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Công ty:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $user->company->name ?? 'Chưa gán' }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Chi nhánh mặc định:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $user->currentBranch->name ?? 'Chưa gán' }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Vai trò:</strong>
                    </div>
                    <div class="col-sm-9">
                        @foreach($user->roles as $role)
                            <span class="badge bg-primary">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <strong>Trạng thái:</strong>
                    </div>
                    <div class="col-sm-9">
                        @if($user->is_active)
                            <span class="badge bg-success">Hoạt động</span>
                        @else
                            <span class="badge bg-danger">Không hoạt động</span>
                        @endif
                    </div>
                </div>
            </x-card>

            <x-card title="Lịch sử đăng nhập gần đây" icon="history" class="mt-4">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Thời gian</th>
                                <th>Địa chỉ IP</th>
                                <th>Trình duyệt</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loginHistory as $log)
                                <tr>
                                    <td><small>{{ $log->created_at->format('d/m/Y H:i') }}</small></td>
                                    <td><small>{{ $log->ip_address }}</small></td>
                                    <td><small>{{ Str::limit($log->user_agent, 40) }}</small></td>
                                    <td>
                                        @if($log->success)
                                            <span class="badge bg-success">Thành công</span>
                                        @else
                                            <span class="badge bg-danger">Thất bại</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Không có lịch sử đăng nhập</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('profile.login-history') }}" class="btn btn-sm btn-secondary mt-2">
                    Xem tất cả
                </a>
            </x-card>
        </div>
    </div>
</div>
@endsection