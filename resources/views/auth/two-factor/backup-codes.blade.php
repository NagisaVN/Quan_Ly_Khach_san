@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card title="Mã sao lưu xác thực 2 yếu tố" icon="key">
                <div class="alert alert-warning" role="alert">
                    <h6 class="alert-heading">
                        <i class="fas fa-exclamation-triangle"></i> Lưu các mã này ở nơi an toàn
                    </h6>
                    <p class="mb-0">
                        Nếu bạn mất quyền truy cập vào ứng dụng xác thực, bạn có thể sử dụng những mã này để đăng nhập.
                        Mỗi mã chỉ có thể được sử dụng một lần.
                    </p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        @foreach(array_slice($codes, 0, 5) as $index => $code)
                            <div class="mb-2 font-monospace">
                                {{ $index + 1 }}. <strong>{{ $code }}</strong>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-md-6">
                        @foreach(array_slice($codes, 5) as $index => $code)
                            <div class="mb-2 font-monospace">
                                {{ $index + 6 }}. <strong>{{ $code }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-secondary">
                        <i class="fas fa-print"></i> In
                    </button>
                    <button onclick="copyToClipboard()" class="btn btn-secondary">
                        <i class="fas fa-copy"></i> Sao chép
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary ms-auto">
                        <i class="fas fa-check"></i> Tôi đã lưu các mã
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>

<script>
function copyToClipboard() {
    const codes = document.querySelectorAll('.font-monospace');
    let text = '';
    codes.forEach(code => {
        text += code.textContent + '\n';
    });
    navigator.clipboard.writeText(text);
    alert('Các mã đã được sao chép vào bộ nhớ tạm');
}
</script>
@endsection
