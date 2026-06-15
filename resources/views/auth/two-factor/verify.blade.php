@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <x-card title="Xác thực 2 yếu tố" icon="shield-alt">
                <form action="{{ route('two-factor.verify') }}" method="POST">
                    @csrf

                    <p class="text-muted mb-4">
                        Nhập mã 6 chữ số từ ứng dụng xác thực của bạn
                    </p>

                    <div class="mb-3">
                        <label for="code" class="form-label">
                            <strong>Mã xác thực</strong>
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg @error('code') is-invalid @enderror" 
                               id="code" 
                               name="code" 
                               placeholder="000000"
                               maxlength="6"
                               autocomplete="off"
                               autofocus
                               required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-check"></i> Xác nhận
                        </button>
                    </div>

                    <hr>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Bạn không có quyền truy cập vào ứng dụng xác thực của mình?
                            <a href="#" data-bs-toggle="modal" data-bs-target="#backupCodesModal">
                                Sử dụng mã sao lưu
                            </a>
                        </small>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
