# API Reference (Brief)

**Base URL:** `/` (web routes, session auth)  
**Response format (AJAX):** `{ success, message, data, errors, statusCode }`

---

## Booking

| Method | Endpoint | Permission | Mô tả |
|--------|----------|------------|-------|
| GET | `/bookings` | `bookings.view` | Danh sách booking |
| GET | `/bookings/create` | `bookings.create` | Form tạo booking |
| POST | `/bookings` | `bookings.create` | Tạo booking |
| GET | `/bookings/{id}` | `bookings.view` | Chi tiết booking |
| GET | `/bookings/availability` | `bookings.view` | AJAX kiểm tra phòng trống |
| POST | `/bookings/{id}/check-in` | `bookings.update` | Check-in (BR-12, BR-14) |
| POST | `/bookings/{id}/check-out` | `bookings.update` | Check-out (BR-13, BR-16) |
| POST | `/bookings/{id}/cancel` | `bookings.update` | Hủy booking |
| POST | `/bookings/{id}/extend` | `bookings.update` | Gia hạn |
| POST | `/bookings/{id}/change-room` | `bookings.update` | Đổi phòng |
| POST | `/bookings/{id}/services` | `bookings.update` | Thêm dịch vụ |

### Availability (AJAX)

**GET** `/bookings/availability?check_in_date=2026-06-15&check_out_date=2026-06-17&room_type_id=1`

```json
{
  "success": true,
  "data": {
    "total": 3,
    "available_rooms": [
      { "id": 1, "room_number": "101", "room_type": "Standard", "base_price": "800000.00" }
    ]
  }
}
```

### Create Booking

**POST** `/bookings`

| Field | Type | Required |
|-------|------|----------|
| customer_id | int | ✓ |
| check_in_date | date | ✓ |
| check_out_date | date | ✓ |
| room_ids[] | array | ✓ |
| adults | int | ✓ |
| children | int | |
| source | enum | |
| special_requests | string | |

---

## Payment

| Method | Endpoint | Permission | Mô tả |
|--------|----------|------------|-------|
| GET | `/invoices/{id}` | `payments.view` | Chi tiết hóa đơn |
| GET | `/invoices/{id}/pdf` | `payments.print` | Tải PDF |
| GET | `/invoices/{id}/pay` | `payments.create` | Form thanh toán |
| POST | `/invoices/{id}/pay` | `payments.create` | Xử lý thanh toán (BR-15) |
| GET | `/payments/callback` | public | Mock gateway callback |

### Process Payment

**POST** `/invoices/{id}/pay`

| Field | Type | Required |
|-------|------|----------|
| amount | decimal | ✓ |
| payment_method | cash\|bank\|momo\|vnpay\|qr | ✓ |
| reference | string | |
| notes | string | |

---

## Pricing

| Method | Endpoint | Permission | Mô tả |
|--------|----------|------------|-------|
| GET | `/pricing` | `pricing.view` | Danh sách rules + seasonal |
| POST | `/pricing/rules` | `pricing.create` | Tạo pricing rule |
| DELETE | `/pricing/rules/{id}` | `pricing.delete` | Xóa rule |
| POST | `/pricing/seasonal` | `pricing.create` | Tạo seasonal rate |
| DELETE | `/pricing/seasonal/{id}` | `pricing.delete` | Xóa seasonal rate |

---

## Reports

| Method | Endpoint | Permission | Mô tả |
|--------|----------|------------|-------|
| GET | `/reports` | `reports.view` | Dashboard báo cáo (Chart.js) |
| GET | `/reports/export/excel` | `reports.export` | Export Excel |
| GET | `/reports/export/pdf` | `reports.export` | Export PDF |

**Query params:** `from_date`, `to_date`, `group_by` (day\|week\|month)

---

## Error Codes

| Exception | HTTP | Message |
|-----------|------|---------|
| `RoomNotAvailableException` | 422 | Phòng đã được đặt trong khoảng thời gian này |
| `RoomMaintenanceException` | 422 | Không thể check-in: phòng đang bảo trì |
| `PaymentRequiredException` | 422 | Vui lòng thanh toán đủ trước khi check-out |
| `BookingInvalidStatusException` | 422 | Trạng thái booking không hợp lệ |

---

## Environment

```env
PAYMENT_DRIVER=mock
SMS_DRIVER=mock
OTA_DRIVER=mock
DOOR_LOCK_DRIVER=mock
HOTEL_TAX_RATE=0.10
```

---

## API (Sanctum)

**Base URL:** `/api`  
**Auth:** Bearer token (Sanctum)

| Method | Endpoint | Mô tả |
|--------|----------|-------|
| GET | `/api/bookings/availability` | Kiểm tra phòng trống (query: check_in_date, check_out_date, room_type_id) |

---

## Portal Khách hàng

**Prefix:** `/portal`  
**Auth:** Session + role `customer` hoặc liên kết `customers.user_id`

| Method | Endpoint | Mô tả |
|--------|----------|-------|
| GET | `/portal` | Dashboard khách hàng |
| GET | `/portal/bookings` | Danh sách booking của tôi |
| GET | `/portal/bookings/create` | Form đặt phòng online |
| POST | `/portal/bookings` | Tạo booking |
| GET | `/portal/bookings/{id}` | Chi tiết + hóa đơn |
