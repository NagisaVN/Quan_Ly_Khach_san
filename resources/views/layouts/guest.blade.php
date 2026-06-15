<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal Khách hàng')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
        <div class="container">
            <a href="{{ route('portal.dashboard') }}" class="navbar-brand"><b>Hotel</b> Portal</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('portal.bookings.index') }}">Booking của tôi</a></li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">@csrf<button class="nav-link btn btn-link">Đăng xuất</button></form>
                </li>
            </ul>
        </div>
    </nav>
    <div class="content-wrapper">
        <div class="content-header"><div class="container"><h1>@yield('page-title')</h1></div></div>
        <section class="content"><div class="container">@include('components.alert') @yield('content')</div></section>
    </div>
</div>
</body>
</html>
