@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card title="Chỉnh sửa hồ sơ" icon="user-edit">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <x-form-group
                        name="name"
                        label="Tên đầy đủ"
                        type="text"
                        :value="old('name', $user->name)"
                        required="true"
                    />

                    <x-form-group
                        name="email"
                        label="Email"
                        type="email"
                        :value="old('email', $user->email)"
                        required="true"
                    />

                    <x-form-group
                        name="phone"
                        label="Điện thoại"
                        type="tel"
                        :value="old('phone', $user->phone)"
                        placeholder="+84 (0) 123 456 789"
                    />

                    <div class="mb-3">
                        <label for="avatar" class="form-label">Avatar</label>
                        <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
                        <small class="form-text text-muted">JPG, PNG. Tối đa 2MB</small>
                        @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật hồ sơ
                        </button>
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                            Hủy bỏ
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
