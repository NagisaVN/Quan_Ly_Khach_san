@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <x-card title="Lịch sử đăng nhập" icon="history">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Địa chỉ IP</th>
                                <th>Trình duyệt / Thiết bị</th>
                                <th>Hành động</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                                <tr>
                                    <td>
                                        <strong>{{ $session->ip_address ?? 'N/A' }}</strong>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($session->user_agent ?? 'Unknown', 50) }}</small>
                                    </td>
                                    <td>
                                        @if($session->action === 'login')
                                            <span class="badge bg-success">Đăng nhập</span>
                                        @elseif($session->action === 'logout')
                                            <span class="badge bg-secondary">Đăng xuất</span>
                                        @else
                                            <span class="badge bg-info">{{ ucfirst($session->action) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $session->created_at->format('d/m/Y H:i:s') }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Không có lịch sử
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <p class="text-muted mb-0">Tổng cộng: {{ $sessions->total() }} mục</p>
                    {{ $sessions->links('pagination::bootstrap-5') }}
                </div>
            </x-card>

            <a href="{{ route('profile.show') }}" class="btn btn-secondary mt-3">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
</div>
@endsection
