@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <x-card title="Thiết lập Xác thực 2 yếu tố" icon="lock">
                <form action="{{ route('two-factor.enable') }}" method="POST">
                    @csrf

                    <div class="alert alert-info mb-4">
                        <p class="mb-0">
                            <strong>Bước 1:</strong> Quét mã QR bên dưới bằng ứng dụng Google Authenticator, Microsoft Authenticator hoặc Authy
                        </p>
                    </div>

                    <div class="text-center mb-4">
                        <img src="{{ $qrCodeUrl }}" alt="QR Code" width="250" height="250">
                    </div>

                    <div class="alert alert-warning mb-4">
                        <p class="mb-0">
                            <strong>Khóa bí mật:</strong> <code>{{ $secret }}</code>
                        </p>
                        <small>Lưu khóa này ở nơi an toàn để khôi phục quyền truy cập nếu cần</small>
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">
                            <strong>Bước 2:</strong> Nhập mã 6 chữ số từ ứng dụng
                        </label>
                        <input type="text" 
                               class="form-control @error('code') is-invalid @enderror" 
                               id="code" 
                               name="code" 
                               placeholder="000000"
                               maxlength="6"
                               autocomplete="off"
                               required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <input type="hidden" name="secret" value="{{ $secret }}">

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Xác nhận & Kích hoạt 2FA
                        </button>
                        <a href="{{ route('profile.security') }}" class="btn btn-secondary">
                            Hủy bỏ
                        </a>
                    </div>
                </form>
            </x-card>

            <div class="mt-4">
                <div class="alert alert-warning" role="alert">
                    <h6 class="alert-heading">
                        <i class="fas fa-exclamation-triangle"></i> Tuyên bố từ chối trách nhiệm
                    </h6>
                    <ul class="mb-0">
                        <li>Nếu bạn mất quyền truy cập vào thiết bị, bạn sẽ không thể đăng nhập</li>
                        <li>Hãy lưu khóa bí mật ở nơi an toàn</li>
                        <li>Sau khi kích hoạt, bạn sẽ cần cung cấp mã 6 chữ số khi đăng nhập</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
