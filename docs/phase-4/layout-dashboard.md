# Giai đoạn 4 — AdminLTE Layout + Dashboard

## Hoàn thành

| Hạng mục | File |
|----------|------|
| Menu động | config/menu.php, app/Services/MenuService.php |
| Dashboard KPI | app/Services/DashboardService.php |
| Layout AdminLTE | resources/views/layouts/app.blade.php |
| Components | alert, breadcrumb, adminlte-card, skeleton, modal, datatable |
| Dashboard | resources/views/dashboard/index.blade.php |

## Tính năng UI

- Sidebar treeview theo permission RBAC
- Branch switcher trên navbar
- Dark mode (localStorage)
- DataTables, SweetAlert2, Chart.js, Select2 qua CDN
- KPI: phòng trống, check-in/out, doanh thu, tỷ lệ lấp đầy
- Biểu đồ: doanh thu 7 ngày, occupancy, trạng thái booking
- Bảng đặt phòng gần đây