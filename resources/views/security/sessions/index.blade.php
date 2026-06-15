@extends('layouts.app')

@section('title', 'Phiên đăng nhập')
@section('page-title', 'Phiên đăng nhập')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-desktop me-2"></i>
                @if ($canViewAll)
                    Tất cả phiên đăng nhập
                @else
                    Phiên đăng nhập của bạn
                @endif
            </h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        @if ($canViewAll)
                            <th>Người dùng</th>
                            <th>Email</th>
                        @endif
                        <th>IP</th>
                        <th>Trình duyệt</th>
                        <th>Hoạt động cuối</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sessions as $session)
                        <tr>
                            @if ($canViewAll)
                                <td>{{ $session->user_name ?? '—' }}</td>
                                <td>{{ $session->user_email ?? '—' }}</td>
                            @endif
                            <td>{{ $session->ip_address ?? '—' }}</td>
                            <td>
                                <span class="text-truncate d-inline-block" style="max-width: 250px;"
                                      title="{{ $session->user_agent }}">
                                    {{ $session->user_agent ?? '—' }}
                                </span>
                            </td>
                            <td>{{ $session->last_activity_at->format('d/m/Y H:i:s') }}</td>
                            <td>
                                @if ($session->is_current)
                                    <span class="badge text-bg-primary">Phiên hiện tại</span>
                                @else
                                    <span class="badge text-bg-secondary">Hoạt động</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if (! $session->is_current)
                                    <form action="{{ route('security.sessions.destroy', $session->id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Bạn có chắc muốn đăng xuất phiên này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canViewAll ? 7 : 5 }}" class="text-center text-muted py-4">
                                Không có phiên đăng nhập nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
