<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Quản lý Khách sạn</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@2.0.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <style>
        .skeleton-loader .placeholder { min-height: 1rem; display: block; }
        .small-box .icon { top: 10px; }
    </style>
    @stack('styles')
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
<div class="app-wrapper">
    <nav class="app-header navbar navbar-expand bg-body border-bottom">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-md-block">
                    <span class="nav-link fw-semibold"><i class="fas fa-hotel me-1"></i> Quản lý Khách sạn</span>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
                @if($userBranches->count() > 0)
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-store me-1"></i>
                            {{ $userBranches->firstWhere('id', $currentBranchId)?->name ?? 'Chi nhánh' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach($userBranches as $branch)
                                <li>
                                    <form action="{{ route('branch.switch') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                        <button type="submit" class="dropdown-item {{ $currentBranchId == $branch->id ? 'active' : '' }}">{{ $branch->name }}</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif
                <li class="nav-item me-2">
                    <button type="button" class="nav-link btn btn-link" id="darkModeToggle" title="Chế độ tối"><i class="fas fa-moon"></i></button>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}" class="brand-link text-decoration-none px-3 py-3 d-block">
                <i class="fas fa-hotel me-2"></i><span class="brand-text fw-semibold">Hotel MS</span>
            </a>
        </div>
        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                    @foreach($sidebarMenu as $section)
                        @if(!empty($section['header']))
                            <li class="nav-header text-uppercase small">{{ $section['header'] }}</li>
                        @endif
                        @foreach($section['items'] as $item)
                            @if(!empty($item['children']))
                                <li class="nav-item {{ $menuService->isTreeOpen($item) ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ $menuService->isTreeOpen($item) ? 'active' : '' }}">
                                        <i class="nav-icon {{ $item['icon'] }}"></i>
                                        <p>{{ $item['label'] }} <i class="nav-arrow fas fa-chevron-right"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @foreach($item['children'] as $child)
                                            <li class="nav-item">
                                                <a href="{{ route($child['route']) }}" class="nav-link {{ $menuService->isActive($child['route']) ? 'active' : '' }}">
                                                    <i class="nav-icon {{ $child['icon'] ?? 'far fa-circle' }}"></i>
                                                    <p>{{ $child['label'] }}</p>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a href="{{ route($item['route']) }}" class="nav-link {{ $menuService->isActive($item['route']) ? 'active' : '' }}">
                                        <i class="nav-icon {{ $item['icon'] }}"></i>
                                        <p>{{ $item['label'] }}</p>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endforeach
                </ul>
            </nav>
        </div>
    </aside>

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-sm-6"><h3 class="mb-0">@yield('page-title', 'Bảng điều khiển')</h3></div>
                    <div class="col-sm-6">
                        @hasSection('breadcrumb')
                            @yield('breadcrumb')
                        @else
                            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => trim($__env->yieldContent('page-title'))]]" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <x-alert />
                @yield('content')
            </div>
        </div>
    </main>

    <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">Laravel {{ app()->version() }}</div>
        <strong>&copy; {{ date('Y') }} Hotel Management System.</strong>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@2.0.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const html = document.documentElement;
    const toggle = document.getElementById('darkModeToggle');
    const saved = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-bs-theme', saved);
    if (toggle) {
        toggle.querySelector('i').className = saved === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        toggle.addEventListener('click', function () {
            const next = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', next);
            localStorage.setItem('theme', next);
            toggle.querySelector('i').className = next === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        });
    }
    if (typeof $ !== 'undefined' && $.fn.select2) $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('.datatable').each(function () {
            if (!$.fn.DataTable.isDataTable(this)) {
                $(this).DataTable({ language: { url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/vi.json' }, responsive: true });
            }
        });
    }
    document.querySelectorAll('.btn-delete').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const form = btn.closest('form');
            Swal.fire({ title: 'Xác nhận xóa?', text: 'Hành động này không thể hoàn tác.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Hủy', confirmButtonText: 'Xóa' })
                .then((r) => { if (r.isConfirmed) form.submit(); });
        });
    });
});
</script>
@stack('scripts')
</body>
</html>
