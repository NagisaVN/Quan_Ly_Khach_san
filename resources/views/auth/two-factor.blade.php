@extends('layouts.auth')

@section('title', 'Xác thực 2 bước')

@section('content')
    <p class="login-box-msg fw-semibold mb-2">Xác thực hai yếu tố</p>
    <p class="text-muted small mb-4">Nhập mã 6 chữ số từ ứng dụng xác thực của bạn.</p>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('two-factor.verify') }}" method="POST">
        @csrf
        <div class="input-group mb-4">
            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror text-center fs-4 letter-spacing"
                   placeholder="000000" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required autofocus>
            <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check me-1"></i> Xác nhận
            </button>
            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại đăng nhập
            </a>
        </div>
    </form>
@endsection

@push('styles')
<style>
    .letter-spacing { letter-spacing: 0.5em; }
</style>
@endpush
