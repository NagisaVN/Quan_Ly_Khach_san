@extends('layouts.auth')

@section('title', 'Đặt lại mật khẩu')

@section('content')
    <p class="login-box-msg fw-semibold mb-4">Đặt lại mật khẩu</p>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   placeholder="Email" value="{{ old('email', $email) }}" required autofocus>
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
        </div>

        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="Mật khẩu mới" required>
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
        </div>

        <div class="input-group mb-3">
            <input type="password" name="password_confirmation" class="form-control"
                   placeholder="Xác nhận mật khẩu" required>
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-key me-1"></i> Đặt lại mật khẩu
            </button>
            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại đăng nhập
            </a>
        </div>
    </form>
@endsection
