<!-- Sidebar Navigation -->
<nav class="sidebar bg-dark text-white" style="width: 250px; height: 100vh; position: fixed; left: 0; top: 0; overflow-y: auto; z-index: 1000;">
    <!-- Logo -->
    <div class="p-3 border-bottom">
        <h5 class="mb-0">
            <i class="fas fa-hotel me-2"></i>Khách sạn
        </h5>
    </div>
    
    <!-- Navigation Menu -->
    <ul class="list-unstyled mt-3 px-2">
        <!-- Dashboard -->
        <li class="mb-2">
            <a href="{{ route('dashboard') }}" class="nav-link text-white text-decoration-none {{ request()->routeIs('dashboard') ? 'bg-primary' : '' }} rounded px-3 py-2 d-block">
                <i class="fas fa-chart-line me-2 w-5"></i>Dashboard
            </a>
        </li>
        
        <!-- System (SuperAdmin only) -->
        @can('system.view')
        <li class="mb-2">
            <a href="#" class="nav-link text-white text-decoration-none rounded px-3 py-2 d-block" data-bs-toggle="collapse" data-bs-target="#systemMenu">
                <i class="fas fa-cog me-2 w-5"></i>Hệ thống
                <i class="fas fa-chevron-down float-end"></i>
            </a>
            <ul class="collapse list-unstyled px-3" id="systemMenu">
                @can('system.view')
                <li><a href="{{ route('users.index') }}" class="nav-link text-white-50 text-decoration-none small py-1"><i class="fas fa-users me-2 ms-2"></i>Người dùng</a></li>
                @endcan
                @can('system.view')
                <li><a href="{{ route('roles.index') }}" class="nav-link text-white-50 text-decoration-none small py-1"><i class="fas fa-shield me-2 ms-2"></i>Vai trò</a></li>
                @endcan
            </ul>
        </li>
        @endcan
        
        <!-- Enterprise -->
        @can('enterprise.view')
        <li class="mb-2">
            <a href="#" class="nav-link text-white text-decoration-none rounded px-3 py-2 d-block" data-bs-toggle="collapse" data-bs-target="#enterpriseMenu">
                <i class="fas fa-building me-2 w-5"></i>Doanh nghiệp
                <i class="fas fa-chevron-down float-end"></i>
            </a>
            <ul class="collapse list-unstyled px-3" id="enterpriseMenu">
                @can('enterprise.view')
                <li><a href="{{ route('companies.index') }}" class="nav-link text-white-50 text-decoration-none small py-1"><i class="fas fa-industry me-2 ms-2"></i>Công ty</a></li>
                @endcan
                @can('enterprise.view')
                <li><a href="{{ route('branches.index') }}" class="nav-link text-white-50 text-decoration-none small py-1"><i class="fas fa-map-pin me-2 ms-2"></i>Chi nhánh</a></li>
                @endcan
            </ul>
        </li>
        @endcan
        
        <!-- Rooms -->
        @can('rooms.view')
        <li class="mb-2">
            <a href="#" class="nav-link text-white text-decoration-none rounded px-3 py-2 d-block" data-bs-toggle="collapse" data-bs-target="#roomsMenu">
                <i class="fas fa-door-open me-2 w-5"></i>Phòng
                <i class="fas fa-chevron-down float-end"></i>
            </a>
            <ul class="collapse list-unstyled px-3" id="roomsMenu">
                <li><a href="{{ route('rooms.index') }}" class="nav-link text-white-50 text-decoration-none small py-1"><i class="fas fa-list me-2 ms-2"></i>Danh sách phòng</a></li>
                <li><a href="{{ route('room-types.index') }}" class="nav-link text-white-50 text-decoration-none small py-1"><i class="fas fa-layer-group me-2 ms-2"></i>Loại phòng</a></li>
                <li><a href="{{ route('amenities.index') }}" class="nav-link text-white-50 text-decoration-none small py-1"><i class="fas fa-star me-2 ms-2"></i>Tiện nghi</a></li>
            </ul>
        </li>
        @endcan
        
        <!-- Customers -->
        @can('customers.view')
        <li class="mb-2">
            <a href="{{ route('customers.index') }}" class="nav-link text-white text-decoration-none {{ request()->routeIs('customers.*') ? 'bg-primary' : '' }} rounded px-3 py-2 d-block">
                <i class="fas fa-users me-2 w-5"></i>Khách hàng
            </a>
        </li>
        @endcan
        
        <!-- Bookings -->
        @can('bookings.view')
        <li class="mb-2">
            <a href="{{ route('bookings.index') }}" class="nav-link text-white text-decoration-none {{ request()->routeIs('bookings.*') ? 'bg-primary' : '' }} rounded px-3 py-2 d-block">
                <i class="fas fa-calendar me-2 w-5"></i>Đặt phòng
            </a>
        </li>
        @endcan
        
        <!-- Payments -->
        @can('payments.view')
        <li class="mb-2">
            <a href="#" class="nav-link text-white text-decoration-none rounded px-3 py-2 d-block" data-bs-toggle="collapse" data-bs-target="#paymentsMenu">
                <i class="fas fa-credit-card me-2 w-5"></i>Thanh toán
                <i class="fas fa-chevron-down float-end"></i>
            </a>
            <ul class="collapse list-unstyled px-3" id="paymentsMenu">
                <li><a href="{{ route('invoices.index') }}" class="nav-link text-white-50 text-decoration-none small py-1"><i class="fas fa-file-invoice me-2 ms-2"></i>Hóa đơn</a></li>
                <li><a href="{{ route('payments.index') }}" class="nav-link text-white-50 text-decoration-none small py-1"><i class="fas fa-money-check-alt me-2 ms-2"></i>Thanh toán</a></li>
            </ul>
        </li>
        @endcan
        
        <!-- Services -->
        @can('services.view')
        <li class="mb-2">
            <a href="{{ route('services.index') }}" class="nav-link text-white text-decoration-none {{ request()->routeIs('services.*') ? 'bg-primary' : '' }} rounded px-3 py-2 d-block">
                <i class="fas fa-concierge-bell me-2 w-5"></i>Dịch vụ
            </a>
        </li>
        @endcan
        
        <!-- Reports -->
        @can('reports.view')
        <li class="mb-2">
            <a href="#" class="nav-link text-white text-decoration-none rounded px-3 py-2 d-block" data-bs-toggle="collapse" data-bs-target="#reportsMenu">
                <i class="fas fa-chart-bar me-2 w-5"></i>Báo cáo
                <i class="fas fa-chevron-down float-end"></i>
            </a>
            <ul class="collapse list-unstyled px-3" id="reportsMenu">
                <li><a href="{{ route('reports.revenue') }}" class="nav-link text-white-50 text-decoration-none small py-1"><i class="fas fa-money-bill me-2 ms-2"></i>Doanh thu</a></li>
                <li><a href="{{ route('reports.occupancy') }}" class="nav-link text-white-50 text-decoration-none small py-1"><i class="fas fa-chart-pie me-2 ms-2"></i>Lắp đầy</a></li>
            </ul>
        </li>
        @endcan
    </ul>
</nav>

<!-- Main Content Offset -->
<style>
    body { margin-left: 250px; }
    @media (max-width: 768px) {
        .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
        body { margin-left: 0; }
        .sidebar.show { transform: translateX(0); }
    }
</style>
