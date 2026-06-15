<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Đăng nhập') — Quản lý Khách sạn</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/css/adminlte.min.css">
    <style>
        body.login-page {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%);
            min-height: 100vh;
        }
        .login-box .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        .login-logo a {
            color: #fff;
            font-weight: 700;
            font-size: 1.5rem;
            text-decoration: none;
        }
        .login-logo a:hover {
            color: #e0e0e0;
        }
    </style>
    @stack('styles')
</head>
<body class="login-page">
<div class="login-box">
    <div class="login-logo mb-4 text-center">
        <a href="{{ route('login') }}">
            <i class="fas fa-hotel me-2"></i>Quản lý Khách sạn
        </a>
    </div>
    <div class="card">
        <div class="card-body login-card-body p-4">
            @yield('content')
        </div>
    </div>
    <p class="text-center text-white-50 mt-3 small">&copy; {{ date('Y') }} Hotel Management System</p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
