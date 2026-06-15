@extends('layouts.auth')

@section('title', 'Đăng nhập')

@section('content')
    <p class="login-box-msg fw-semibold mb-4">Đăng nhập hệ thống</p>

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

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   placeholder="Email" value="{{ old('email') }}" required autofocus>
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
        </div>

        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="Mật khẩu" required>
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
        </div>

        <div class="row mb-3">
            <div class="col-7">
                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input"
                           value="1" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                </div>
            </div>
            <div class="col-5 text-end">
                <a href="{{ route('password.request') }}" class="small">Quên mật khẩu?</a>
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt me-1"></i> Đăng nhập
            </button>
        </div>
    </form>
@endsection
