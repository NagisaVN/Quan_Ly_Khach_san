@extends('layouts.app')

@section('title', 'Lịch sử đăng nhập')
@section('page-title', 'Lịch sử đăng nhập')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter me-2"></i>Bộ lọc</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('security.login-logs.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" name="email" id="email" class="form-control"
                           value="{{ request('email') }}" placeholder="Tìm theo email">
                </div>
                <div class="col-md-3">
                    <label for="success" class="form-label">Trạng thái</label>
                    <select name="success" id="success" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="1" @selected(request('success') === '1')>Thành công</option>
                        <option value="0" @selected(request('success') === '0')>Thất bại</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date" class="form-label">Ngày</label>
                    <input type="date" name="date" id="date" class="form-control"
                           value="{{ request('date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Lọc
                    </button>
                    <a href="{{ route('security.login-logs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history me-2"></i>Danh sách lịch sử đăng nhập</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Thời gian</th>
                        <th>Email</th>
                        <th>Người dùng</th>
                        <th>IP</th>
                        <th>Trình duyệt</th>
                        <th>Trạng thái</th>
                        <th>Lý do thất bại</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($loginLogs as $log)
                        <tr>
                            <td>{{ $loginLogs->firstItem() + $loop->index }}</td>
                            <td>{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $log->email }}</td>
                            <td>{{ $log->user?->name ?? '—' }}</td>
                            <td>{{ $log->ip_address ?? '—' }}</td>
                            <td>
                                <span class="text-truncate d-inline-block" style="max-width: 200px;"
                                      title="{{ $log->user_agent }}">
                                    {{ $log->user_agent ?? '—' }}
                                </span>
                            </td>
                            <td>
                                @if ($log->success)
                                    <span class="badge text-bg-success">Thành công</span>
                                @else
                                    <span class="badge text-bg-danger">Thất bại</span>
                                @endif
                            </td>
                            <td>
                                @if ($log->failure_reason)
                                    @switch($log->failure_reason)
                                        @case('invalid_credentials')
                                            Sai mật khẩu
                                            @break
                                        @case('account_locked')
                                            Tài khoản bị khóa
                                            @break
                                        @case('account_inactive')
                                            Tài khoản không hoạt động
                                            @break
                                        @default
                                            {{ $log->failure_reason }}
                                    @endswitch
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Không có dữ liệu lịch sử đăng nhập.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($loginLogs->hasPages())
            <div class="card-footer">
                {{ $loginLogs->links() }}
            </div>
        @endif
    </div>
@endsection
