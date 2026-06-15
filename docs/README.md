# Hotel Management System — Tài liệu Dự án

**Hệ thống Quản lý Khách sạn** — Đồ án tốt nghiệp  
**Tech stack:** Laravel 13, PHP 8.3+, MySQL, AdminLTE 4, Blade

---

## Cấu trúc tài liệu

| Giai đoạn | Thư mục / File | Nội dung |
|-----------|----------------|----------|
| **1** | [phase-1/](./phase-1/) | Phân tích yêu cầu, Use Case, Functional Spec |
| **2** | [phase-2/](./phase-2/) | ERD, Migrations, Seeders |
| **Kiến trúc** | [architecture.md](./architecture.md) | Layers, modules, business rules |
| **API** | [api-reference.md](./api-reference.md) | Endpoints booking, payment, reports, portal |
| **Deploy** | [deployment.md](./deployment.md) | Checklist cài đặt Laragon/production |
| UML | [uml/](./uml/) | Use Case, Activity, Sequence diagrams |

---

## Giai đoạn 1 — Hoàn thành ✅

| Tài liệu | Mô tả |
|----------|--------|
| [01-requirements-analysis.md](./phase-1/01-requirements-analysis.md) | Phân tích 15 module, actors, business rules, NFR |
| [02-use-case-specification.md](./phase-1/02-use-case-specification.md) | 92 Use Cases chi tiết |
| [03-functional-specification.md](./phase-1/03-functional-specification.md) | Input/Output/Validation/API cho chức năng core |

---

## Giai đoạn 2 — Hoàn thành ✅

| Hạng mục | Chi tiết |
|----------|----------|
| [erd.md](./phase-2/erd.md) | ERD đầy đủ ~50 bảng |
| Migrations | 10 file migration + Spatie + Sanctum |
| Seeders | 120 permissions, 6 roles, demo data |
| Models | Company, Branch, Room, Booking, Invoice... |

---

## Giai đoạn 3 — Hoàn thành ✅

| Hạng mục | Chi tiết |
|----------|----------|
| AuthService | Đăng nhập, khóa tài khoản, 2FA, logout |
| Middleware | `active`, `permission`, `branch.context` |
| Views | AdminLTE 4: login, dashboard, security |
| Tests | Feature test đăng nhập |

---

## Giai đoạn 4 — AdminLTE Layout + Dashboard ✅

| Hạng mục | Chi tiết |
|----------|----------|
| [layout-dashboard.md](./phase-4/layout-dashboard.md) | Tài liệu giai đoạn 4 |
| MenuService | Sidebar động theo permission + treeview |
| DashboardService | KPI thực từ DB + Chart.js |
| Layout | Branch switcher, dark mode, breadcrumb |
| Components | card, alert, breadcrumb, skeleton, modal, datatable |

---

## Giai đoạn 5 — CRUD Modules ✅

| Module | Controllers |
|--------|-------------|
| Enterprise | Company, Branch |
| Phòng | RoomType, Floor, Room, Amenity |
| Khách hàng | Customer |
| Dịch vụ | ServiceCategory, Service |
| Hệ thống | User |

---

## Giai đoạn 6 — Booking (Core Business) ✅

| Hạng mục | Chi tiết |
|----------|----------|
| BookingService | availability, create, check-in/out, cancel, extend, change room |
| PricingService | calculateNightlyRate, calculateBookingTotal (snapshot) |
| BookingRepository | overlap query BR-11, pagination |
| BookingController | resource + AJAX availability |
| BookingPolicy | branch scope + RBAC |
| Views | bookings/index, create, show |
| Exceptions | RoomNotAvailable, RoomMaintenance, PaymentRequired |

---

## Giai đoạn 7 — Payment ✅

| Hạng mục | Chi tiết |
|----------|----------|
| InvoiceService | generateFromBooking, recalculate totals |
| PaymentService | processPayment DB transaction BR-15 |
| Adapters | MockVnPayAdapter, MockMomoAdapter |
| Controllers | InvoiceController, PaymentController |
| Views | invoices/show, payments/create, invoices/pdf (DomPDF) |

---

## Giai đoạn 8 — Dynamic Pricing ✅

| Hạng mục | Chi tiết |
|----------|----------|
| PricingService | Full rule engine: season, weekend, holiday, occupancy, loyalty |
| PricingRuleController | CRUD pricing_rules, seasonal_rates |
| View | pricing/index |

---

## Giai đoạn 9 — Reports ✅

| Hạng mục | Chi tiết |
|----------|----------|
| ReportService | revenue, occupancy, top services, customers |
| ReportController | Chart.js dashboard + export |
| RevenueExport | Laravel Excel |
| PDF export | DomPDF |

---

## Giai đoạn 10 — Tests + Docs ✅

| Hạng mục | Chi tiết |
|----------|----------|
| BookingFlowTest | create → check-in → payment → check-out |
| BookingServiceAvailabilityTest | overlap, maintenance, weekend pricing |
| architecture.md | Kiến trúc hệ thống |
| api-reference.md | API endpoints tóm tắt |

---

## Lộ trình triển khai

```
Giai đoạn 1   Phân tích + UML           [DONE]
Giai đoạn 2   ERD + Migration           [DONE]
Giai đoạn 3   Auth + RBAC               [DONE]
Giai đoạn 4   AdminLTE + Dashboard      [DONE]
Giai đoạn 5   CRUD modules              [DONE]
Giai đoạn 6   Booking                   [DONE]
Giai đoạn 7   Payment                   [DONE]
Giai đoạn 8   Dynamic Pricing           [DONE]
Giai đoạn 9   Reports                   [DONE]
Giai đoạn 10  Test + Documentation      [DONE]
```

---

## Kiến trúc

```
Controller → Service → Repository → Model → Database
UI: AdminLTE 4 + Blade + Bootstrap 5.3 + Chart.js
Auth: Session + Sanctum (AJAX)
RBAC: Spatie Permission (6 roles × 8 actions)
Payment: Adapter pattern (mock VNPay/Momo)
```

Chi tiết: [architecture.md](./architecture.md)

---

## Business Rules cốt lõi

- **BR-11:** Không đặt trùng phòng
- **BR-12:** Không check-in phòng bảo trì
- **BR-13:** Không check-out chưa thanh toán
- **BR-14/15/16:** DB Transaction cho Check-in, Payment, Check-out

---

## Cài đặt & Truy cập

```bash
cd d:\laragon\www\Quan_Ly_Khach_San
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

### URL truy cập (Laragon)

| Cách | URL đăng nhập |
|------|----------------|
| **Khuyến nghị** (sau khi Restart Laragon) | http://quan-ly-khach-san.test/login |
| Localhost (đường dẫn đầy đủ) | http://localhost/Quan_Ly_Khach_San/public/login |
| Portal khách hàng | http://quan-ly-khach-san.test/portal |
| Artisan serve | http://127.0.0.1:8000/login |

> **Lưu ý:** Không dùng URL `localhost/quan_ly_khach_san/quan_ly_khach_san/...` (sai chữ hoa/thường → 404).  
> Sau khi sửa vhost, vào Laragon → **Stop All** → **Start All** để kích hoạt `quan-ly-khach-san.test`.

### Tài khoản demo (password: `password`)

| Email | Vai trò |
|-------|---------|
| superadmin@demo.vn | Super Admin |
| letan.hcm@demo.vn | Lễ tân (booking + payment) |
| customer@demo.vn | Khách hàng (Portal) |
