# Phân tích Yêu cầu — Hệ thống Quản lý Khách sạn

**Dự án:** Hotel Management System (HMS)  
**Phiên bản tài liệu:** 1.0  
**Ngày:** 15/06/2026  
**Tech stack:** Laravel 13, PHP 8.3+, MySQL, AdminLTE 4, Blade  

---

## 1. Tổng quan dự án

### 1.1. Mục tiêu

Xây dựng hệ thống phần mềm quản lý khách sạn đa chi nhánh, hỗ trợ toàn bộ quy trình vận hành từ đặt phòng, nhận/trả phòng, dịch vụ, thanh toán đến báo cáo và quản trị nâng cao. Hệ thống phục vụ đồ án tốt nghiệp đại học với kiến trúc rõ ràng, có thể bảo vệ trước hội đồng.

### 1.2. Phạm vi

| Trong phạm vi | Ngoài phạm vi (giai đoạn 1) |
|---------------|----------------------------|
| Web admin nội bộ (AdminLTE + Blade) | Mobile app native iOS/Android |
| Portal khách hàng cơ bản (đặt phòng, xem lịch sử) | Tích hợp OTA production (Booking.com API thật) |
| RBAC 6 vai trò, multi-branch | Machine Learning dự báo giá |
| Mock payment gateway (VNPay, Momo) | Hệ thống kế toán tổng hợp ERP |
| Báo cáo Excel/PDF | BI dashboard phức tạp (Power BI) |

### 1.3. Đối tượng sử dụng (Actors)

| Actor | Mô tả | Phạm vi dữ liệu |
|-------|--------|-----------------|
| **Super Admin** | Quản trị toàn hệ thống, cấu hình nền tảng | Toàn bộ companies/branches |
| **Admin Công ty** | Quản lý doanh nghiệp, chi nhánh thuộc công ty | Theo `company_id` |
| **Quản lý khách sạn** | Vận hành chi nhánh, giá, kho, báo cáo | Theo `branch_id` |
| **Lễ tân** | Booking, check-in/out, thanh toán hàng ngày | Theo `branch_id` |
| **Nhân viên** | Dịch vụ, hành lý, bảo trì (theo module được gán) | Theo `branch_id` + permission |
| **Khách hàng** | Portal: đặt phòng online, xem lịch sử | Chỉ dữ liệu cá nhân |

### 1.4. Giả định hệ thống

1. Một **Company** sở hữu nhiều **Branch** (chi nhánh khách sạn).
2. Mỗi Branch có nhiều **Floor** (tầng), mỗi Floor có nhiều **Room** (phòng).
3. User nội bộ đăng nhập qua session; API AJAX dùng Laravel Sanctum.
4. Tích hợp bên ngoài (payment, SMS, OTA) dùng **Adapter Pattern** với driver mock mặc định.
5. Múi giờ mặc định: `Asia/Ho_Chi_Minh`.
6. Tiền tệ mặc định: VND.

---

## 2. Kiến trúc hệ thống

### 2.1. Kiến trúc phần mềm — Clean Architecture

```
Presentation (Blade/AdminLTE, AJAX)
    ↓
Controller + Form Request + Policy
    ↓
Service (Business Logic)
    ↓
Repository Interface → Eloquent Repository
    ↓
Model (Eloquent ORM)
    ↓
MySQL Database
```

**Nguyên tắc:**
- Controller chỉ điều phối, không chứa business logic.
- Service xử lý nghiệp vụ, transaction, validation rule phức tạp.
- Repository trừu tượng hóa truy vấn database.
- Dependency Injection qua Laravel Service Container.

### 2.2. Multi-tenant theo chi nhánh

- Dữ liệu nghiệp vụ gắn `branch_id` (phòng, booking, invoice...).
- Middleware `BranchScope` lọc query theo chi nhánh đang chọn.
- Super Admin bypass scope; Admin Công ty filter theo `company_id`.

### 2.3. API Response chuẩn (AJAX)

```json
{
  "success": true,
  "message": "Thao tác thành công",
  "data": {},
  "pagination": { "current_page": 1, "per_page": 15, "total": 100, "last_page": 7 },
  "errors": null,
  "statusCode": 200
}
```

---

## 3. Phân tích 15 Module

### Module 1 — Quản lý Hệ thống

| Chức năng | Mô tả | Actor chính |
|-----------|--------|-------------|
| Cấu hình hệ thống | Tên app, logo, SMTP, timezone, currency | Super Admin |
| Nhật ký hệ thống | Activity log: ai làm gì, khi nào, IP | Super Admin, Admin |
| Người dùng | CRUD user, gán role, gán branch | Super Admin, Admin Công ty |
| Vai trò | CRUD role | Super Admin |
| Phân quyền | Gán permission theo module × action | Super Admin |
| Backup | Export DB/file backup thủ công | Super Admin |
| Restore | Khôi phục từ backup (demo) | Super Admin |
| Notification | Thông báo hệ thống, đánh dấu đã đọc | Tất cả |

### Module 2 — Quản lý Doanh nghiệp

| Chức năng | Mô tả |
|-----------|--------|
| Khách sạn (Company) | Thông tin công ty quản lý khách sạn |
| Chi nhánh (Branch) | Địa chỉ, liên hệ, giờ check-in/out mặc định |
| Phòng ban | HR nội bộ: Lễ tân, Buồng phòng, Kế toán... |
| Nhân viên | Liên kết user ↔ employee ↔ department |
| Nhà cung cấp | Supplier cho kho, hợp đồng |
| Ngân hàng | Tài khoản nhận thanh toán |
| Thuế | Cấu hình VAT (%), loại thuế |
| Phí dịch vụ | Service charge, resort fee |

### Module 3 — Bảo mật

| Chức năng | Mô tả |
|-----------|--------|
| Login / Logout | Session-based auth, AdminLTE login page |
| Forgot / Reset Password | Email reset link |
| 2FA | TOTP (Google Authenticator) — optional per user |
| Session Management | Xem phiên đang active, force logout |
| Lock Account | Khóa sau N lần đăng nhập sai |
| Login History | IP, user agent, thời gian, thành công/thất bại |

### Module 4 — Quản lý Phòng

| Chức năng | Mô tả |
|-----------|--------|
| Danh mục phòng | CRUD phòng theo tầng/chi nhánh |
| Loại phòng | Standard, Deluxe, Suite... + sức chứa |
| Tiện nghi | WiFi, TV, Minibar... (many-to-many với phòng) |
| Giá phòng | Giá cơ bản theo loại phòng |
| Trạng thái | available, occupied, reserved, maintenance, cleaning |
| Sơ đồ phòng | Grid visual theo tầng, màu theo trạng thái |
| Ảnh phòng | Upload nhiều ảnh, ảnh đại diện |

### Module 5 — Khách hàng

| Chức năng | Mô tả |
|-----------|--------|
| Thông tin khách | Họ tên, email, phone, địa chỉ |
| CCCD / Passport | Upload + số giấy tờ |
| Lịch sử lưu trú | Danh sách booking đã qua |
| Thành viên | Hạng: Standard, Silver, Gold, Platinum |
| Điểm tích lũy | Tích/trừ điểm theo chi tiêu |

### Module 6 — Đặt phòng (Booking)

| Chức năng | Mô tả |
|-----------|--------|
| Đặt phòng offline | Lễ tân tạo booking tại quầy |
| Booking online | Khách đặt qua portal |
| Check availability | Kiểm tra phòng trống theo ngày |
| Check-in | Gán phòng, cập nhật trạng thái, transaction |
| Check-out | Tính tiền, validate thanh toán, transaction |
| Gia hạn | Extend checkout date |
| Hủy phòng | Cancel với lý do, policy hoàn tiền |
| Đổi phòng | Change room mid-stay |
| Đặt nhiều phòng | Multi-room trong 1 booking |
| Đặt theo đoàn | Group booking, leader contact |

### Module 7 — Dịch vụ

| Loại dịch vụ | Mô tả |
|--------------|--------|
| Spa, Giặt ủi, Ăn uống | Gán vào booking hoặc phòng |
| Minibar, Room Service | Charge vào invoice |
| Đưa đón, Thuê xe | Dịch vụ có lịch hẹn |

### Module 8 — Thanh toán

| Chức năng | Mô tả |
|-----------|--------|
| Invoice | Hóa đơn tổng hợp phòng + dịch vụ + thuế - giảm giá |
| Receipt | Phiếu thu sau thanh toán |
| Discount / Coupon | Mã giảm giá % hoặc số tiền |
| Refund | Hoàn tiền một phần/toàn phần |
| VAT | Tính thuế theo cấu hình |
| Thanh toán | Cash, Banking, QR (mock), Momo, VNPay (mock) |

### Module 9 — Hành lý

| Chức năng | Mô tả |
|-----------|--------|
| Ký gửi | Tạo mã, gán thẻ, lưu vị trí |
| Theo dõi | Danh sách hành lý đang gửi |
| Nhận / Trả | Quy trình trả hành lý |
| QR Code | In mã QR tra cứu |

### Module 10 — Báo cáo

| Báo cáo | Mô tả |
|---------|--------|
| Doanh thu | Theo ngày/tuần/tháng/năm |
| Khách hàng | Top khách, công nợ |
| Phòng | Công suất, tỷ lệ lấp đầy |
| Top dịch vụ | Dịch vụ bán chạy |
| Export | Excel (Laravel Excel), PDF (DomPDF) |

### Module 11 — Giá phòng động (Dynamic Pricing)

| Quy tắc | Mô tả |
|---------|--------|
| Theo mùa | Summer, Tet holiday... |
| Theo ngày | Thứ 2-5 vs cuối tuần |
| Theo lễ | Public holidays |
| Theo sự kiện | Event local |
| Theo công suất | Tăng giá khi occupancy > 80% |
| Theo hạng khách | Loyalty tier discount |

### Module 12 — Quản lý Kho

| Chức năng | Mô tả |
|-----------|--------|
| Hàng hóa | Danh mục sản phẩm (minibar, vật tư) |
| Nhập / Xuất | Stock movement |
| Kiểm kê | Stocktake, điều chỉnh tồn |
| Nhà cung cấp | Liên kết supplier |

### Module 13 — Bảo trì

| Chức năng | Mô tả |
|-----------|--------|
| Yêu cầu sửa chữa | Tạo ticket, phân công |
| Lịch bảo trì | Preventive maintenance schedule |
| Chi phí | Ghi nhận chi phí sửa chữa |

### Module 14 — Hợp đồng

| Loại | Mô tả |
|------|--------|
| Nhà cung cấp | Hợp đồng mua hàng/dịch vụ |
| Khách đoàn | Hợp đồng giá đoàn, theo dõi hiệu lực |

### Module 15 — Tích hợp

| Tích hợp | Triển khai |
|----------|------------|
| Email | Laravel Mail + Queue (Mailtrap demo) |
| SMS | MockSmsAdapter |
| OTA | MockOtaAdapter (sync booking demo) |
| Door Lock | MockDoorLockAdapter (mã phòng demo) |
| QR | Generate QR cho hành lý, thanh toán |

---

## 4. Ràng buộc nghiệp vụ (Business Rules)

| ID | Ràng buộc | Mức xử lý |
|----|-----------|-----------|
| BR-01 | Một khách sạn có nhiều chi nhánh | FK `branches.company_id` |
| BR-02 | Một chi nhánh có nhiều tầng | FK `floors.branch_id` |
| BR-03 | Một tầng có nhiều phòng | FK `rooms.floor_id` |
| BR-04 | Một phòng có nhiều tiện nghi | Pivot `room_amenity` |
| BR-05 | Một booking có nhiều phòng | Pivot `booking_rooms` |
| BR-06 | Một booking có nhiều dịch vụ | Pivot `booking_services` |
| BR-07 | Một booking có nhiều hóa đơn | FK `invoices.booking_id` |
| BR-08 | Một khách có thể đặt nhiều lần | FK `bookings.customer_id` |
| BR-09 | Một khách có nhiều hành lý | FK `luggage.customer_id` |
| BR-10 | Giá phòng thay đổi theo thời gian | `pricing_rules` + snapshot |
| BR-11 | **Không cho đặt trùng phòng** | Overlap check trong BookingService |
| BR-12 | **Không check-in phòng bảo trì** | Validate `room.status != maintenance` |
| BR-13 | **Không check-out chưa thanh toán** | Validate `invoice.balance <= 0` |
| BR-14 | **Transaction khi Check-in** | `DB::transaction()` |
| BR-15 | **Transaction khi Payment** | `DB::transaction()` |
| BR-16 | **Transaction khi Check-out** | `DB::transaction()` |

---

## 5. Phân quyền RBAC

### 5.1. Roles

1. Super Admin  
2. Admin Công ty  
3. Quản lý khách sạn  
4. Lễ tân  
5. Nhân viên  
6. Khách hàng  

### 5.2. Permission format

`{module}.{action}` — Actions: `view`, `create`, `update`, `delete`, `approve`, `export`, `import`, `print`

Ví dụ: `bookings.create`, `invoices.print`, `reports.export`

### 5.3. Ràng buộc bảo mật dữ liệu

- Không xem dữ liệu ngoài phạm vi branch/company.
- Không xóa dữ liệu quan trọng (invoice đã thanh toán) — soft delete + policy.
- Không can thiệp chi nhánh khác (BranchScope middleware).

---

## 6. Yêu cầu phi chức năng

| ID | Yêu cầu | Tiêu chí |
|----|---------|----------|
| NFR-01 | Hiệu năng | Trang danh sách < 2s với 1000 records (pagination) |
| NFR-02 | Bảo mật | HTTPS, CSRF, XSS escape, SQL injection prevention (Eloquent) |
| NFR-03 | Khả dụng | Responsive AdminLTE trên tablet (lễ tân) |
| NFR-04 | Audit | Mọi thao tác CRUD quan trọng ghi activity log |
| NFR-05 | Backup | Export SQL/file thủ công |
| NFR-06 | Code quality | PSR-12, SOLID, Feature test cho luồng core |
| NFR-07 | UI | AdminLTE 4, dark mode, DataTables, SweetAlert2 |

---

## 7. Quan hệ thực thể cốt lõi (tóm tắt)

```
Company 1──N Branch 1──N Floor 1──N Room
Room N──N Amenity (via room_amenity)
RoomType 1──N Room
Customer 1──N Booking 1──N BookingRoom N──1 Room
Booking 1──N BookingService N──1 Service
Booking 1──N Invoice 1──N Payment
Customer 1──N Luggage
User N──N Role N──N Permission
```

---

## 8. Giai đoạn triển khai

| Giai đoạn | Nội dung | Trạng thái |
|-----------|----------|------------|
| 1 | Phân tích, Use Case, Functional Spec, UML | **Hoàn thành** |
| 2 | ERD, Migration, Seeder | **Hoàn thành** |
| 3 | Auth + RBAC | **Hoàn thành** |
| 4 | AdminLTE Layout + Dashboard | Chờ |
| 5 | CRUD modules | Chờ |
| 6 | Booking | Chờ |
| 7 | Payment | Chờ |
| 8 | Dynamic Pricing | Chờ |
| 9 | Report | Chờ |
| 10 | Test + Documentation | Chờ |

---

## 9. Tài liệu liên quan

- [02-use-case-specification.md](./02-use-case-specification.md)
- [03-functional-specification.md](./03-functional-specification.md)
- [../uml/use-case-diagram.md](../uml/use-case-diagram.md)
