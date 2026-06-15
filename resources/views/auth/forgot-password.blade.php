@extends('layouts.auth')

@section('title', 'Quên mật khẩu')

@section('content')
    <p class="login-box-msg fw-semibold mb-4">Quên mật khẩu</p>
    <p class="text-muted small mb-4">Nhập email để nhận liên kết đặt lại mật khẩu.</p>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   placeholder="Email" value="{{ old('email') }}" required autofocus>
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-1"></i> Gửi liên kết
            </button>
            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại đăng nhập
            </a>
        </div>
    </form>
@endsection
