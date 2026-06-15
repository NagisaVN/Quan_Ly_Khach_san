# Đặc tả Chức năng (Functional Specification)

**Phiên bản:** 1.0 | **Hệ thống:** Hotel Management System

---

## 1. Giới thiệu

Tài liệu mô tả chi tiết input, output, validation và business rules cho các chức năng chính. Format chuẩn cho mỗi function:

- **Function ID** — Tên chức năng  
- **Input** — Dữ liệu đầu vào  
- **Output** — Dữ liệu đầu ra  
- **Validation** — Quy tắc kiểm tra  
- **Business Rules** — Ràng buộc nghiệp vụ  
- **API Endpoint** — RESTful (dự kiến Giai đoạn 5+)  

---

## 2. Module Bảo mật

### FN-SEC-001: Đăng nhập

| Thuộc tính | Chi tiết |
|------------|----------|
| Input | `email` (required, email), `password` (required, min:8), `remember` (boolean) |
| Output | Redirect dashboard hoặc 2FA screen; session created |
| Validation | Email tồn tại; password bcrypt match; account `is_active=true`; `locked_until` null hoặc expired |
| Business Rules | 5 lần sai → lock 30 phút; ghi `login_logs` |
| API | `POST /api/auth/login` |

### FN-SEC-002: Xác thực 2FA

| Input | `code` (6 digits TOTP) |
| Output | Session hoàn tất |
| Validation | Google2FA verify với secret của user |
| Business Rules | Code hết hạn 30s window ±1 |

### FN-SEC-003: Quên mật khẩu

| Input | `email` |
| Output | Email chứa reset link (token 60 phút) |
| Validation | Email tồn tại (không reveal nếu không tồn tại — security) |

---

## 3. Module Quản lý Phòng

### FN-ROOM-001: Tạo phòng

| Input | `branch_id`, `floor_id`, `room_type_id`, `room_number`, `status`, `notes` |
| Output | Room record + activity log |
| Validation | `room_number` unique trong branch; floor thuộc branch; room_type tồn tại |
| Business Rules | Status mặc định: `available` |

### FN-ROOM-002: Cập nhật trạng thái phòng

| Input | `room_id`, `status` enum: available\|occupied\|reserved\|maintenance\|cleaning |
| Output | Updated room |
| Validation | Không đổi occupied → available nếu có booking active |
| Business Rules | BR-12: maintenance block check-in |

### FN-ROOM-003: Sơ đồ phòng (Floor Map)

| Input | `branch_id`, `floor_id` (optional) |
| Output | JSON grid: `[{room_id, number, status, type, color}]` |
| Validation | User có quyền `rooms.view` trong branch |

---

## 4. Module Khách hàng

### FN-CUS-001: Tạo khách hàng

| Input | `full_name`, `email`, `phone`, `address`, `nationality`, `date_of_birth`, `id_type`, `id_number`, `id_image` (file) |
| Output | Customer record |
| Validation | `phone` hoặc `email` required; `id_number` unique nếu có; image max 2MB jpg/png |
| Business Rules | Auto generate `customer_code`: `CUS-{branch_id}-{seq}` |

### FN-CUS-002: Tích điểm loyalty

| Input | `customer_id`, `points`, `type` (earn\|redeem), `reference` (payment_id) |
| Output | Updated `loyalty_points` balance |
| Business Rules | 1 point = 10,000 VND chi tiêu; redeem min 100 points |

---

## 5. Module Đặt phòng (Core)

### FN-BOOK-001: Kiểm tra phòng trống

| Input | `branch_id`, `check_in_date`, `check_out_date`, `room_type_id` (optional), `room_ids[]` (optional) |
| Output | `{available_rooms: [...], total: N}` |
| Validation | check_out > check_in; dates >= today |
| Business Rules | BR-11: Exclude rooms có booking overlap; exclude maintenance |

**Overlap SQL logic:**
```
NOT EXISTS booking_rooms WHERE room_id = X
AND booking.status NOT IN (cancelled, checked_out)
AND (check_in < request.check_out AND check_out > request.check_in)
```

### FN-BOOK-002: Tạo booking

| Input | `customer_id`, `branch_id`, `check_in_date`, `check_out_date`, `room_ids[]`, `adults`, `children`, `source` (offline\|online\|ota), `notes`, `group_name` (optional) |
| Output | Booking + booking_rooms với `rate_snapshot` |
| Validation | All rooms available; customer tồn tại; branch scope |
| Business Rules | Status initial: `confirmed`; PricingService snapshot; BR-11, BR-14 N/A (chưa check-in) |
| API | `POST /api/bookings` |

**Processing flow:**
1. Validate input (Form Request)
2. BookingService.checkAvailability()
3. PricingService.calculateTotal()
4. DB::transaction: insert bookings, booking_rooms, booking_history
5. NotificationService.sendConfirmation()
6. Return BookingResource

### FN-BOOK-003: Check-in

| Input | `booking_id`, `room_assignments[]` (optional nếu đã gán), `guest_documents[]` |
| Output | Booking status = `checked_in`; rooms = `occupied` |
| Validation | Booking status = confirmed; today >= check_in_date; rooms != maintenance |
| Business Rules | **BR-12, BR-14** — DB::transaction |
| API | `POST /api/bookings/{id}/check-in` |

**Transaction steps:**
1. Lock booking row (pessimistic)
2. Validate all rooms
3. Update booking.status → checked_in, checked_in_at = now
4. Update each room.status → occupied
5. Insert booking_history
6. Commit

### FN-BOOK-004: Check-out

| Input | `booking_id`, `notes` |
| Output | Booking status = `checked_out`; rooms = `cleaning` |
| Validation | Booking status = checked_in; **invoice.balance <= 0 (BR-13)** |
| Business Rules | **BR-16** — DB::transaction; trigger loyalty points |
| API | `POST /api/bookings/{id}/check-out` |

### FN-BOOK-005: Hủy booking

| Input | `booking_id`, `cancel_reason` |
| Output | Status = cancelled |
| Validation | Status in (confirmed, pending); not checked_in |
| Business Rules | Release rooms; apply cancel policy refund nếu có prepayment |

### FN-BOOK-006: Gia hạn / Đổi phòng

| Input (extend) | `booking_id`, `new_check_out_date` |
| Input (change) | `booking_id`, `old_room_id`, `new_room_id` |
| Validation | New dates/rooms available; recalculate price diff |

---

## 6. Module Dịch vụ

### FN-SVC-001: Gán dịch vụ cho booking

| Input | `booking_id`, `service_id`, `quantity`, `unit_price` (optional override), `notes` |
| Output | booking_service record; invoice item draft updated |
| Validation | Booking status = checked_in; service active; quantity > 0 |
| Business Rules | Chỉ QL mới override unit_price |

---

## 7. Module Thanh toán

### FN-PAY-001: Tạo / Cập nhật hóa đơn

| Input | `booking_id` |
| Output | Invoice với items: room nights, services, tax, discount |
| Calculation | `subtotal = sum(items)` → `tax = subtotal * tax_rate` → `total = subtotal + tax - discount` → `balance = total - paid` |

### FN-PAY-002: Thanh toán

| Input | `invoice_id`, `amount`, `payment_method` (cash\|bank\|momo\|vnpay\|qr), `reference` (optional) |
| Output | Payment record; invoice balance updated |
| Validation | amount > 0; amount <= balance (hoặc overpay → credit) |
| Business Rules | **BR-15** — DB::transaction |
| API | `POST /api/invoices/{id}/payments` |

**Mock gateway flow (Momo/VNPay):**
1. PaymentService.createPendingPayment()
2. MockAdapter.generatePaymentUrl()
3. User simulate pay → callback
4. PaymentService.confirmPayment() in transaction

### FN-PAY-003: Áp dụng coupon

| Input | `invoice_id`, `coupon_code` |
| Validation | Code valid, not expired, usage limit, min order amount |
| Output | discount applied; recalculate total |

### FN-PAY-004: Hoàn tiền

| Input | `payment_id`, `amount`, `reason` |
| Validation | QL approve (`payments.approve`); amount <= paid |
| Output | Refund record; balance adjusted |

### FN-PAY-005: In hóa đơn PDF

| Input | `invoice_id` |
| Output | PDF stream (DomPDF) |
| Permission | `invoices.print` |

---

## 8. Module Giá động

### FN-PRC-001: PricingService.calculateNightlyRate

| Input | `room_type_id`, `branch_id`, `date`, `customer_id` (optional) |
| Output | `{base_rate, adjustments[], final_rate}` |
| Rules applied (priority desc) | 1. Seasonal 2. Event 3. Weekend/Holiday 4. Occupancy 5. Loyalty discount |
| Business Rules | BR-10; snapshot at booking time |

### FN-PRC-002: CRUD Pricing Rule

| Input | `name`, `type`, `conditions` (JSON), `adjustment_type` (percent\|fixed), `value`, `priority`, `active` |
| Validation | priority unique per branch+type; value >= 0 |

---

## 9. Module Báo cáo

### FN-RPT-001: Báo cáo doanh thu

| Input | `branch_id`, `from_date`, `to_date`, `group_by` (day\|week\|month) |
| Output | `{labels[], revenue[], payments_by_method{}}` |
| API | `GET /api/reports/revenue` |

### FN-RPT-002: Occupancy Rate

| Input | `branch_id`, `from_date`, `to_date` |
| Output | `{date, occupied_rooms, total_rooms, rate_percent}` |
| Formula | `occupancy = occupied_room_nights / (total_rooms * days) * 100` |

### FN-RPT-003: Export Excel

| Input | Report type + filters |
| Output | `.xlsx` file download (Laravel Excel) |

---

## 10. Module Hành lý

### FN-LUG-001: Ký gửi hành lý

| Input | `customer_id`, `booking_id` (optional), `description`, `quantity`, `storage_location` |
| Output | Luggage record + QR code image |
| Validation | Customer tồn tại |
| Business Rules | Auto tag code: `LUG-{branch}-{timestamp}` |

---

## 11. Module RBAC

### FN-RBAC-001: Kiểm tra quyền

| Input | User, permission string, model (optional) |
| Output | boolean |
| Implementation | Spatie `@can` + Policy + BranchScope |

### FN-RBAC-002: Menu động

| Input | Authenticated user permissions |
| Output | Filtered menu tree (AdminLTE sidebar) |
| Implementation | `config/menu.php` filtered by `MenuService` |

---

## 12. API Response Format (Global)

| Field | Type | Mô tả |
|-------|------|--------|
| success | boolean | true/false |
| message | string | Thông báo |
| data | object\|array\|null | Payload |
| pagination | object\|null | current_page, per_page, total, last_page |
| errors | object\|null | Validation errors |
| statusCode | integer | HTTP status |

**HTTP Status codes:** 200 OK, 201 Created, 400 Bad Request, 401 Unauthorized, 403 Forbidden, 404 Not Found, 422 Validation Error, 500 Server Error

---

## 13. Validation Rules tổng hợp (Form Request)

| Entity | Rules |
|--------|-------|
| User | email unique, password min 8 confirmed, role required |
| Branch | company_id exists, name required, code unique per company |
| Room | room_number unique per branch, floor_id belongs to branch |
| Booking | check_out after check_in, room_ids min 1, customer required |
| Payment | amount numeric min 0.01, method in enum |
| Customer | phone regex VN, email nullable unique |

---

## 14. Error Messages (Vietnamese)

| Code | Message |
|------|---------|
| ROOM_NOT_AVAILABLE | Phòng đã được đặt trong khoảng thời gian này |
| ROOM_MAINTENANCE | Không thể check-in: phòng đang bảo trì |
| PAYMENT_REQUIRED | Vui lòng thanh toán đủ trước khi check-out |
| BOOKING_INVALID_STATUS | Trạng thái booking không hợp lệ cho thao tác này |
| PERMISSION_DENIED | Bạn không có quyền thực hiện thao tác này |
| BRANCH_SCOPE | Dữ liệu không thuộc chi nhánh của bạn |

---

## 15. Database Audit Columns (Global)

Mọi bảng nghiệp vụ:
```sql
created_at TIMESTAMP
updated_at TIMESTAMP
deleted_at TIMESTAMP NULL  -- soft delete
created_by BIGINT UNSIGNED NULL FK users
updated_by BIGINT UNSIGNED NULL FK users
```

---

**Tài liệu liên quan:**
- [01-requirements-analysis.md](./01-requirements-analysis.md)
- [02-use-case-specification.md](./02-use-case-specification.md)
- [../uml/activity-diagrams/](../uml/activity-diagrams/)
- [../uml/sequence-diagrams/](../uml/sequence-diagrams/)
