<?php

return [
    ['label' => 'Bảng điều khiển', 'route' => 'dashboard', 'icon' => 'fas fa-tachometer-alt', 'permission' => null],
    ['label' => 'Đặt phòng', 'route' => 'bookings.index', 'icon' => 'fas fa-calendar-check', 'permission' => 'bookings.view'],
    ['label' => 'Khách hàng', 'route' => 'customers.index', 'icon' => 'fas fa-users', 'permission' => 'customers.view'],
    [
        'label' => 'Phòng',
        'icon' => 'fas fa-bed',
        'permission' => 'rooms.view',
        'children' => [
            ['label' => 'Danh sách phòng', 'route' => 'rooms.rooms.index', 'icon' => 'far fa-circle', 'permission' => 'rooms.view'],
            ['label' => 'Loại phòng', 'route' => 'rooms.room-types.index', 'icon' => 'far fa-circle', 'permission' => 'rooms.view'],
            ['label' => 'Tầng', 'route' => 'rooms.floors.index', 'icon' => 'far fa-circle', 'permission' => 'rooms.view'],
            ['label' => 'Sơ đồ phòng', 'route' => 'rooms.floors.map', 'icon' => 'far fa-circle', 'permission' => 'rooms.view'],
            ['label' => 'Tiện nghi', 'route' => 'rooms.amenities.index', 'icon' => 'far fa-circle', 'permission' => 'rooms.view'],
        ],
    ],
    [
        'label' => 'Dịch vụ',
        'icon' => 'fas fa-concierge-bell',
        'permission' => 'services.view',
        'children' => [
            ['label' => 'Danh mục', 'route' => 'services.categories.index', 'icon' => 'far fa-circle', 'permission' => 'services.view'],
            ['label' => 'Dịch vụ', 'route' => 'services.items.index', 'icon' => 'far fa-circle', 'permission' => 'services.view'],
        ],
    ],
    ['label' => 'Thanh toán', 'route' => 'invoices.index', 'icon' => 'fas fa-file-invoice-dollar', 'permission' => 'payments.view'],
    ['label' => 'Báo cáo', 'route' => 'reports.index', 'icon' => 'fas fa-chart-bar', 'permission' => 'reports.view'],
    ['label' => 'Giá phòng', 'route' => 'pricing-rules.index', 'icon' => 'fas fa-tags', 'permission' => 'pricing.view'],
    [
        'label' => 'Doanh nghiệp',
        'icon' => 'fas fa-building',
        'permission' => 'enterprise.view',
        'children' => [
            ['label' => 'Công ty', 'route' => 'enterprise.companies.index', 'icon' => 'far fa-circle', 'permission' => 'enterprise.view'],
            ['label' => 'Chi nhánh', 'route' => 'enterprise.branches.index', 'icon' => 'far fa-circle', 'permission' => 'enterprise.view'],
            ['label' => 'Phòng ban', 'route' => 'enterprise.departments.index', 'icon' => 'far fa-circle', 'permission' => 'enterprise.view'],
            ['label' => 'Nhà cung cấp', 'route' => 'enterprise.suppliers.index', 'icon' => 'far fa-circle', 'permission' => 'enterprise.view'],
            ['label' => 'Tài khoản NH', 'route' => 'enterprise.bank-accounts.index', 'icon' => 'far fa-circle', 'permission' => 'enterprise.view'],
            ['label' => 'Thuế', 'route' => 'enterprise.taxes.index', 'icon' => 'far fa-circle', 'permission' => 'enterprise.view'],
            ['label' => 'Phí dịch vụ', 'route' => 'enterprise.service-fees.index', 'icon' => 'far fa-circle', 'permission' => 'enterprise.view'],
        ],
    ],
    ['label' => 'Hành lý', 'route' => 'luggage.index', 'icon' => 'fas fa-suitcase', 'permission' => 'luggage.view'],
    ['label' => 'Kho hàng', 'route' => 'inventory.products.index', 'icon' => 'fas fa-boxes', 'permission' => 'inventory.view'],
    ['label' => 'Bảo trì', 'route' => 'maintenance.requests.index', 'icon' => 'fas fa-tools', 'permission' => 'maintenance.view'],
    ['label' => 'Hợp đồng', 'route' => 'contracts.index', 'icon' => 'fas fa-file-contract', 'permission' => 'contracts.view'],
    [
        'label' => 'Hệ thống',
        'icon' => 'fas fa-cog',
        'permission' => 'system.view',
        'children' => [
            ['label' => 'Người dùng', 'route' => 'system.users.index', 'icon' => 'far fa-circle', 'permission' => 'system.view'],
            ['label' => 'Cấu hình', 'route' => 'system.configs.index', 'icon' => 'far fa-circle', 'permission' => 'system.view'],
            ['label' => 'Nhật ký', 'route' => 'system.activity-logs.index', 'icon' => 'far fa-circle', 'permission' => 'system.view'],
            ['label' => 'Thông báo', 'route' => 'system.notifications.index', 'icon' => 'far fa-circle', 'permission' => null],
            ['label' => 'Backup', 'route' => 'system.backups.index', 'icon' => 'far fa-circle', 'permission' => 'system.view'],
            ['label' => 'Lịch sử đăng nhập', 'route' => 'security.login-logs.index', 'icon' => 'far fa-circle', 'permission' => 'security.view'],
            ['label' => 'Phiên đăng nhập', 'route' => 'security.sessions.index', 'icon' => 'far fa-circle', 'permission' => 'security.view'],
        ],
    ],
];
