@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <!-- Change Password -->
            <x-card title="Thay đổi mật khẩu" icon="key">
                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-form-group
                        name="current_password"
                        label="Mật khẩu hiện tại"
                        type="password"
                        required="true"
                    />

                    <x-form-group
                        name="password"
                        label="Mật khẩu mới"
                        type="password"
                        required="true"
                        help="Tối thiểu 8 ký tự"
                    />

                    <x-form-group
                        name="password_confirmation"
                        label="Xác nhận mật khẩu"
                        type="password"
                        required="true"
                    />

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật mật khẩu
                    </button>
                </form>
            </x-card>

            <!-- 2FA Settings -->
            <x-card title="Xác thực 2 yếu tố (2FA)" icon="shield-alt" class="mt-4">
                @if($twoFactorEnabled)
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Xác thực 2 yếu tố đã được bật
                    </div>
                    <p class="text-muted mb-3">Bạn đang bảo vệ tài khoản của mình bằng xác thực 2 yếu tố.</p>
                    <form action="{{ route('two-factor.disable') }}" method="POST" class="d-inline">
                        @csrf
                        <x-form-group
                            name="password"
                            label="Nhập mật khẩu để vô hiệu hóa 2FA"
                            type="password"
                            required="true"
                            class="mb-3"
                        />
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn chắc chắn muốn vô hiệu hóa 2FA?');">
                            <i class="fas fa-times"></i> Vô hiệu hóa 2FA
                        </button>
                    </form>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Xác thực 2 yếu tố chưa được bật
                    </div>
                    <p class="text-muted mb-3">Bật xác thực 2 yếu tố để tăng cường bảo mật tài khoản của bạn.</p>
                    <a href="{{ route('two-factor.setup') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Bật xác thực 2 yếu tố
                    </a>
                @endif
            </x-card>

            <!-- Active Sessions -->
            <x-card title="Các phiên hoạt động" icon="desktop" class="mt-4">
                <p class="text-muted mb-3">Danh sách các trình duyệt và thiết bị mà bạn đã đăng nhập.</p>
                <div class="list-group">
                    @foreach($loginSessions as $session)
                        @if($session->action === 'login')
                            <div class=\"list-group-item\">
                                <div class=\"d-flex justify-content-between align-items-center\">
                                    <div>
                                        <h6 class=\"mb-0\">
                                            <i class=\"fas fa-chrome\"></i> {{ Str::limit($session->user_agent ?? 'Unknown', 60) }}
                                        </h6>
                                        <small class=\"text-muted\">{{ $session->ip_address ?? 'N/A' }} • {{ $session->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                @if(count($loginSessions) > 1)
                    <form action="{{ route('profile.logout-others') }}" method="POST" class="mt-3">
                        @csrf
                        <x-form-group
                            name="password"
                            label="Nhập mật khẩu để thoát từ tất cả các phiên khác"
                            type="password"
                            required="true"
                            class="mb-3"
                        />
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Bạn sẽ bị thoát từ tất cả các phiên khác');">
                            <i class="fas fa-sign-out-alt"></i> Thoát từ tất cả các phiên khác
                        </button>
                    </form>
                @endif
            </x-card>
        </div>
        <div class="col-md-4">
            <x-card title="Thông tin bảo mật" icon="info-circle">
                <div class="mb-3">
                    <small class="text-muted">
                        <strong>Đăng nhập thất bại:</strong> {{ $failedLogins }} lần
                    </small>
                </div>
                <div class="mb-3">
                    <small class="text-muted">
                        <strong>Trạng thái tài khoản:</strong>
                        @if($isLocked)
                            <span class="badge bg-danger">Bị khóa</span>
                        @else
                            <span class="badge bg-success">Bình thường</span>
                        @endif
                    </small>
                </div>
                <hr>
                <small class="text-muted d-block">
                    Cập nhật lần cuối: {{ $user->updated_at->format('d/m/Y H:i') }}
                </small>
            </x-card>
        </div>
    </div>
</div>
@endsection