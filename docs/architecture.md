# Kiến trúc Hệ thống

**Hotel Management System** — Laravel 13, PHP 8.3+

---

## Tổng quan

```
┌─────────────┐     ┌─────────────┐     ┌──────────────────┐     ┌────────┐
│  Controller │ ──► │   Service   │ ──► │   Repository     │ ──► │ Model  │
│  (HTTP/UI)  │     │ (Business)  │     │  (Data Access)   │     │  (ORM) │
└─────────────┘     └─────────────┘     └──────────────────┘     └────────┘
       │                    │
       ▼                    ▼
  Form Request         DB Transaction
  Policy/RBAC          Custom Exceptions
```

---

## Layers

| Layer | Vai trò | Ví dụ |
|-------|---------|-------|
| **Controller** | Nhận request, authorize, trả view/JSON | `BookingController` |
| **Service** | Business logic, transaction, BR rules | `BookingService`, `PaymentService` |
| **Repository** | Truy vấn DB, tái sử dụng query | `BookingRepository` |
| **Model** | Eloquent entity, relations | `Booking`, `Invoice` |
| **Adapter** | Tích hợp bên ngoài (payment gateway) | `MockVnPayAdapter` |

---

## Module chính (Phase 6–9)

### Booking (Phase 6)
- `BookingService`: availability (BR-11), create, check-in (BR-12/14), check-out (BR-13/16), cancel, extend, change room
- `PricingService`: snapshot giá khi tạo booking
- `BookingPolicy`: branch scope + Spatie permissions

### Payment (Phase 7)
- `InvoiceService`: tạo HĐ từ booking + dịch vụ
- `PaymentService`: thanh toán DB transaction (BR-15)
- Mock gateways: VNPay, Momo (`PAYMENT_DRIVER=mock`)

### Dynamic Pricing (Phase 8)
- `PricingService` rule engine: seasonal, weekend, holiday, occupancy, loyalty
- CRUD `pricing_rules`, `seasonal_rates`

### Reports (Phase 9)
- `ReportService`: revenue, occupancy, top services, top customers
- Export: Laravel Excel + DomPDF

---

## Middleware & Security

| Middleware | Mô tả |
|------------|-------|
| `auth` | Session authentication |
| `active` | Chặn user bị vô hiệu hóa |
| `branch.context` | Gán `current_branch_id` vào session |
| `permission` | Spatie RBAC (`bookings.view`, ...) |

---

## Business Rules (Transaction)

| BR | Mô tả | Service |
|----|-------|---------|
| BR-11 | Không đặt trùng phòng | `BookingService::createBooking` |
| BR-12 | Không check-in phòng bảo trì | `BookingService::checkIn` |
| BR-13 | Check-out khi HĐ đã thanh toán đủ | `BookingService::checkOut` |
| BR-14 | Check-in trong transaction | `BookingService::checkIn` |
| BR-15 | Payment trong transaction | `PaymentService::processPayment` |
| BR-16 | Check-out trong transaction | `BookingService::checkOut` |

---

## Cấu trúc thư mục

```
app/
├── Adapters/Contracts/     # PaymentGatewayInterface
├── Adapters/Mock/          # MockVnPayAdapter, MockMomoAdapter
├── DTOs/                   # CreateBookingDto
├── Exceptions/             # RoomNotAvailableException, ...
├── Exports/                # RevenueExport
├── Http/Controllers/
│   ├── Booking/
│   ├── Payment/
│   ├── Pricing/
│   └── Report/
├── Policies/               # BookingPolicy
├── Repositories/           # BookingRepository
└── Services/               # BookingService, PricingService, ...
```

---

## Tech Stack

- **UI:** AdminLTE 4 + Blade + Bootstrap 5.3 + Chart.js
- **Auth:** Session + Sanctum
- **RBAC:** Spatie Permission (6 roles × 8 actions)
- **PDF:** barryvdh/laravel-dompdf
- **Excel:** maatwebsite/excel
