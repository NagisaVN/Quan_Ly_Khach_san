# Đặc tả Use Case — Hệ thống Quản lý Khách sạn

**Phiên bản:** 1.0 | **Tổng số Use Case:** 92

---

## Quy ước

| Ký hiệu | Ý nghĩa |
|---------|---------|
| SA | Super Admin |
| AC | Admin Công ty |
| QL | Quản lý khách sạn |
| LT | Lễ tân |
| NV | Nhân viên |
| KH | Khách hàng |

**Mức độ ưu tiên:** C (Cao — demo bảo vệ), T (Trung bình), L (Thấp)

---

## Module 1 — Quản lý Hệ thống

### UC-SYS-01: Cấu hình hệ thống
| Thuộc tính | Nội dung |
|------------|----------|
| Actor | SA |
| Priority | T |
| Precondition | Đã đăng nhập với quyền `system_config.update` |
| Postcondition | Cấu hình được lưu vào `system_configs` |
| Main Flow | 1. Vào menu Cấu hình → 2. Sửa các trường (tên app, logo, SMTP...) → 3. Lưu → 4. Hệ thống validate → 5. Ghi audit log |
| Alt Flow | A1: Validation fail → hiển thị lỗi, không lưu |

### UC-SYS-02: Xem nhật ký hệ thống
| Actor | SA, AC | Priority | T |
| Main Flow | 1. Vào Activity Logs → 2. Filter theo user/module/date → 3. Xem chi tiết log entry |

### UC-SYS-03: Quản lý người dùng (CRUD)
| Actor | SA, AC | Priority | C |
| Main Flow | 1. Danh sách users → 2. Tạo/Sửa/Xóa → 3. Gán role + branch → 4. Lưu |
| Alt Flow | A1: Email trùng → báo lỗi. A2: Xóa user đang có booking active → từ chối |

### UC-SYS-04: Quản lý vai trò
| Actor | SA | Priority | C |
| Main Flow | 1. CRUD role → 2. Gán permissions → 3. Lưu |

### UC-SYS-05: Quản lý phân quyền
| Actor | SA | Priority | C |
| Main Flow | 1. Chọn role → 2. Tick permissions theo module → 3. Lưu → Cache permission clear |

### UC-SYS-06: Sao lưu dữ liệu
| Actor | SA | Priority | L |
| Main Flow | 1. Chọn Backup → 2. Hệ thống export DB/file → 3. Lưu vào storage → 4. Ghi log |

### UC-SYS-07: Khôi phục dữ liệu
| Actor | SA | Priority | L |
| Main Flow | 1. Chọn file backup → 2. Xác nhận SweetAlert → 3. Restore (demo env) |

### UC-SYS-08: Quản lý thông báo
| Actor | Tất cả | Priority | T |
| Main Flow | 1. Xem danh sách notification → 2. Đánh dấu đã đọc → 3. Click xem chi tiết |

---

## Module 2 — Quản lý Doanh nghiệp

### UC-ENT-01: Quản lý công ty/khách sạn
| Actor | SA, AC | Priority | C |
| Main Flow | CRUD company: tên, MST, địa chỉ, logo, liên hệ |

### UC-ENT-02: Quản lý chi nhánh
| Actor | SA, AC | Priority | C |
| Main Flow | CRUD branch thuộc company; cấu hình giờ check-in/out mặc định |

### UC-ENT-03: Quản lý phòng ban
| Actor | AC, QL | Priority | T |
| Main Flow | CRUD department theo branch |

### UC-ENT-04: Quản lý nhân viên
| Actor | AC, QL | Priority | T |
| Main Flow | CRUD employee, liên kết user account, gán department |

### UC-ENT-05: Quản lý nhà cung cấp
| Actor | QL | Priority | T |
| Main Flow | CRUD supplier: tên, liên hệ, loại hàng/dịch vụ |

### UC-ENT-06: Quản lý tài khoản ngân hàng
| Actor | AC, QL | Priority | T |
| Main Flow | CRUD bank account cho branch |

### UC-ENT-07: Cấu hình thuế
| Actor | AC, QL | Priority | C |
| Main Flow | CRUD tax rate (VAT %), áp dụng theo branch |

### UC-ENT-08: Cấu hình phí dịch vụ
| Actor | QL | Priority | T |
| Main Flow | CRUD service fee (resort fee, service charge %) |

---

## Module 3 — Bảo mật

### UC-SEC-01: Đăng nhập
| Actor | Tất cả (trừ KH portal riêng) | Priority | **C** |
| Precondition | Có tài khoản active, chưa bị khóa |
| Main Flow | 1. Nhập email/password → 2. Validate → 3. Check lock → 4. Tạo session → 5. Ghi login_log → 6. Redirect dashboard |
| Alt Flow | A1: Sai password → tăng failed_attempts, lock nếu >= 5. A2: Bật 2FA → chuyển màn OTP |

### UC-SEC-02: Đăng xuất
| Actor | Tất cả | Priority | C |
| Main Flow | 1. Click Logout → 2. Destroy session → 3. Redirect login |

### UC-SEC-03: Quên mật khẩu
| Actor | Tất cả | Priority | T |
| Main Flow | 1. Nhập email → 2. Gửi reset link → 3. User click link → 4. Nhập password mới |

### UC-SEC-04: Đặt lại mật khẩu
| Actor | Tất cả | Priority | T |
| Main Flow | 1. Token validate → 2. Nhập password + confirm → 3. Hash lưu DB |

### UC-SEC-05: Xác thực 2 lớp (2FA)
| Actor | Tất cả | Priority | T |
| Main Flow | 1. Sau login → nhập TOTP 6 số → 2. Verify → 3. Hoàn tất session |

### UC-SEC-06: Quản lý phiên đăng nhập
| Actor | SA, User | Priority | L |
| Main Flow | 1. Xem sessions active → 2. Force logout session khác |

### UC-SEC-07: Khóa tài khoản
| Actor | Hệ thống (auto), SA | Priority | T |
| Main Flow | Auto: 5 lần sai → locked_until + 30 phút. SA: manual lock/unlock |

### UC-SEC-08: Xem lịch sử đăng nhập
| Actor | SA, AC, User | Priority | T |
| Main Flow | Filter login_logs theo user, IP, date, status |

---

## Module 4 — Quản lý Phòng

### UC-ROOM-01: Quản lý danh mục phòng
| Actor | QL, LT | Priority | C |
| Main Flow | CRUD room: số phòng, floor, room_type, branch |

### UC-ROOM-02: Quản lý loại phòng
| Actor | QL | Priority | C |
| Main Flow | CRUD room_type: tên, mô tả, capacity, base_price |

### UC-ROOM-03: Quản lý tiện nghi
| Actor | QL | Priority | T |
| Main Flow | CRUD amenity + gán/bỏ gán cho phòng |

### UC-ROOM-04: Quản lý giá phòng cơ bản
| Actor | QL | Priority | C |
| Main Flow | CRUD room_rate theo room_type + date range |

### UC-ROOM-05: Cập nhật trạng thái phòng
| Actor | LT, NV | Priority | C |
| Main Flow | 1. Chọn phòng → 2. Đổi status (available/cleaning/maintenance...) → 3. Ghi log |
| Business Rule | BR-12: maintenance → không cho check-in |

### UC-ROOM-06: Xem sơ đồ phòng
| Actor | LT, QL | Priority | C |
| Main Flow | 1. Chọn tầng → 2. Grid màu theo status → 3. Click phòng xem chi tiết/quick action |

### UC-ROOM-07: Upload ảnh phòng
| Actor | QL | Priority | T |
| Main Flow | Upload multi image, set primary, delete |

---

## Module 5 — Khách hàng

### UC-CUS-01: Quản lý thông tin khách
| Actor | LT, QL | Priority | C |
| Main Flow | CRUD customer: name, email, phone, address, nationality |

### UC-CUS-02: Quản lý CCCD/Passport
| Actor | LT | Priority | C |
| Main Flow | Upload scan, lưu số giấy tờ, expiry date |

### UC-CUS-03: Xem lịch sử lưu trú
| Actor | LT, QL, KH | Priority | C |
| Main Flow | Danh sách booking theo customer_id |

### UC-CUS-04: Quản lý hạng thành viên
| Actor | QL | Priority | T |
| Main Flow | CRUD loyalty_tier + điều kiện nâng hạng |

### UC-CUS-05: Quản lý điểm tích lũy
| Actor | Hệ thống, LT | Priority | T |
| Main Flow | Auto tích điểm sau payment; manual adjust bởi QL |

---

## Module 6 — Đặt phòng

### UC-BOOK-01: Tạo booking offline
| Actor | LT | Priority | **C** |
| Precondition | Customer tồn tại hoặc tạo mới; phòng available |
| Main Flow | 1. Chọn customer → 2. Chọn dates → 3. Check availability → 4. Chọn phòng → 5. PricingService tính giá → 6. Confirm → 7. Tạo booking + booking_rooms (snapshot rate) |
| Alt Flow | A1: Phòng trùng lịch (BR-11) → báo lỗi. A2: Multi-room → lặp bước 3-4 |

### UC-BOOK-02: Tạo booking online
| Actor | KH | Priority | C |
| Main Flow | Portal: search → chọn phòng → nhập info → confirm → email notification |

### UC-BOOK-03: Kiểm tra phòng trống
| Actor | LT, KH | Priority | **C** |
| Main Flow | Input: branch, check_in, check_out, room_type → Output: danh sách phòng available |

### UC-BOOK-04: Check-in
| Actor | LT | Priority | **C** |
| Precondition | Booking status = confirmed; phòng không maintenance |
| Main Flow | 1. Mở booking → 2. Verify guest docs → 3. **DB::transaction**: update booking status, room → occupied, ghi booking_history → 4. In phiếu đăng ký |
| Business Rule | BR-12, BR-14 |

### UC-BOOK-05: Check-out
| Actor | LT | Priority | **C** |
| Precondition | Invoice balance = 0 (BR-13) |
| Main Flow | 1. Mở booking → 2. Validate payment → 3. **DB::transaction**: checkout, room → cleaning, loyalty points → 4. In hóa đơn |
| Alt Flow | A1: Chưa thanh toán → redirect payment |

### UC-BOOK-06: Gia hạn lưu trú
| Actor | LT | Priority | T |
| Main Flow | 1. Chọn new checkout date → 2. Check availability extension → 3. Recalculate price → 4. Update booking |

### UC-BOOK-07: Hủy booking
| Actor | LT, KH | Priority | C |
| Main Flow | 1. Nhập lý do → 2. Apply cancel policy → 3. Update status cancelled → 4. Release rooms |

### UC-BOOK-08: Đổi phòng
| Actor | LT | Priority | T |
| Main Flow | 1. Chọn phòng mới → 2. Check available → 3. Update booking_rooms → 4. Adjust price diff |

### UC-BOOK-09: Đặt nhiều phòng
| Actor | LT, KH | Priority | C |
| Main Flow | 1 booking, N booking_rooms; validate tất cả phòng available |

### UC-BOOK-10: Đặt theo đoàn
| Actor | LT, QL | Priority | T |
| Main Flow | Group booking: leader, số người, nhiều phòng, group discount |

---

## Module 7 — Dịch vụ

### UC-SVC-01: Quản lý danh mục dịch vụ
| Actor | QL | Priority | C |
| Main Flow | CRUD service + category (Spa, Giặt ủi, F&B...) |

### UC-SVC-02: Gán dịch vụ cho booking
| Actor | LT, NV | Priority | C |
| Main Flow | 1. Chọn booking → 2. Add service + qty → 3. Tính tiền → 4. Cập nhật invoice draft |

### UC-SVC-03: Hủy/điều chỉnh dịch vụ
| Actor | LT, QL | Priority | T |
| Main Flow | Cancel hoặc adjust qty với lý do, QL approve nếu cần |

### UC-SVC-04: Room Service / Minibar
| Actor | NV, LT | Priority | T |
| Main Flow | Charge minibar consumption vào booking |

---

## Module 8 — Thanh toán

### UC-PAY-01: Tạo hóa đơn
| Actor | LT, Hệ thống | Priority | **C** |
| Main Flow | Auto generate: room charges + services + tax - discount = total |

### UC-PAY-02: Thanh toán tiền mặt
| Actor | LT | Priority | **C** |
| Main Flow | 1. Nhập số tiền → 2. **DB::transaction**: payment record, update invoice balance → 3. In receipt |

### UC-PAY-03: Thanh toán chuyển khoản
| Actor | LT | Priority | C |
| Main Flow | Ghi nhận transfer + reference number |

### UC-PAY-04: Thanh toán QR / Momo / VNPay
| Actor | LT, KH | Priority | C |
| Main Flow | 1. Chọn gateway → 2. MockAdapter redirect/simulate → 3. Callback → 4. Update payment |

### UC-PAY-05: Áp dụng giảm giá / Coupon
| Actor | LT | Priority | T |
| Main Flow | Validate coupon code, apply % hoặc fixed amount |

### UC-PAY-06: Hoàn tiền
| Actor | QL, LT | Priority | T |
| Main Flow | 1. QL approve → 2. Partial/full refund → 3. Update invoice |

### UC-PAY-07: In hóa đơn / Receipt
| Actor | LT | Priority | C |
| Main Flow | DomPDF generate PDF, print view |

---

## Module 9 — Hành lý

### UC-LUG-01: Ký gửi hành lý
| Actor | LT, NV | Priority | T |
| Main Flow | 1. Tạo record → 2. Generate QR/tag → 3. Lưu vị trí |

### UC-LUG-02: Theo dõi hành lý
| Actor | LT, NV | Priority | T |
| Main Flow | Danh sách luggage in_storage, filter theo customer |

### UC-LUG-03: Trả hành lý
| Actor | LT, NV | Priority | T |
| Main Flow | Scan QR → verify → update status returned |

---

## Module 10 — Báo cáo

### UC-RPT-01: Báo cáo doanh thu
| Actor | QL, AC | Priority | **C** |
| Main Flow | Filter date/branch → Chart + table → Export Excel/PDF |

### UC-RPT-02: Báo cáo khách hàng
| Actor | QL | Priority | T |
| Main Flow | Top customers, công nợ, frequency |

### UC-RPT-03: Báo cáo phòng / Occupancy
| Actor | QL | Priority | **C** |
| Main Flow | Tỷ lệ lấp đầy theo ngày/tháng, Chart.js |

### UC-RPT-04: Top dịch vụ
| Actor | QL | Priority | T |
| Main Flow | Ranking services by revenue/qty |

### UC-RPT-05: Export Excel
| Actor | QL, AC | Priority | C |
| Main Flow | Laravel Excel export filtered data |

### UC-RPT-06: Export PDF
| Actor | QL, AC | Priority | C |
| Main Flow | DomPDF report template |

---

## Module 11 — Giá phòng động

### UC-PRC-01: Cấu hình giá theo mùa
| Actor | QL | Priority | C |
| Main Flow | CRUD seasonal_rates với date range + multiplier |

### UC-PRC-02: Cấu hình giá cuối tuần / ngày lễ
| Actor | QL | Priority | C |
| Main Flow | pricing_rules: day_of_week, holiday calendar |

### UC-PRC-03: Cấu hình giá theo sự kiện
| Actor | QL | Priority | T |
| Main Flow | event_rates linked to date/event name |

### UC-PRC-04: Cấu hình giá theo công suất
| Actor | QL | Priority | T |
| Main Flow | Rule: occupancy > X% → +Y% price |

### UC-PRC-05: Giảm giá theo hạng khách
| Actor | QL | Priority | T |
| Main Flow | loyalty_tier → discount % in PricingService |

### UC-PRC-06: Preview tính giá
| Actor | LT, QL | Priority | C |
| Main Flow | Calculator: input dates + room → output breakdown |

---

## Module 12 — Quản lý Kho

### UC-INV-01: Quản lý hàng hóa | Actor: QL, NV | Priority: T  
### UC-INV-02: Nhập kho | Actor: QL, NV | Priority: T  
### UC-INV-03: Xuất kho | Actor: QL, NV | Priority: T  
### UC-INV-04: Kiểm kê | Actor: QL | Priority: L  
### UC-INV-05: Báo cáo tồn kho | Actor: QL | Priority: T  

---

## Module 13 — Bảo trì

### UC-MNT-01: Tạo yêu cầu sửa chữa | Actor: LT, NV | Priority: T  
| Main Flow | 1. Tạo ticket gắn room → 2. Room status → maintenance → 3. Assign handler |

### UC-MNT-02: Theo dõi tiến độ | Actor: QL, NV | Priority: T  
### UC-MNT-03: Lịch bảo trì định kỳ | Actor: QL | Priority: L  
### UC-MNT-04: Ghi chi phí bảo trì | Actor: QL | Priority: L  

---

## Module 14 — Hợp đồng

### UC-CTR-01: Hợp đồng nhà cung cấp | Actor: QL, AC | Priority: L  
### UC-CTR-02: Hợp đồng khách đoàn | Actor: QL, AC | Priority: T  
### UC-CTR-03: Theo dõi hiệu lực / gia hạn | Actor: QL | Priority: L  

---

## Module 15 — Tích hợp

### UC-INT-01: Gửi email xác nhận booking | Actor: Hệ thống | Priority: C  
### UC-INT-02: Gửi SMS (mock) | Actor: Hệ thống | Priority: L  
### UC-INT-03: Sync OTA (mock) | Actor: QL | Priority: L  
### UC-INT-04: Cấp mã cửa (Door Lock mock) | Actor: Hệ thống | Priority: L  
### UC-INT-05: Tạo QR thanh toán / hành lý | Actor: LT, Hệ thống | Priority: T  

---

## Use Case Diagram — Tóm tắt theo Actor

| Actor | Số UC | UC ưu tiên demo |
|-------|-------|-----------------|
| Super Admin | 12 | SYS-03, SYS-04, SYS-05 |
| Admin Công ty | 15 | ENT-01, ENT-02 |
| Quản lý KS | 28 | RPT-01, PRC-01, ROOM-02 |
| Lễ tân | 22 | **BOOK-01, BOOK-04, BOOK-05, PAY-02** |
| Nhân viên | 10 | SVC-02, LUG-01 |
| Khách hàng | 5 | BOOK-02, BOOK-03 |

---

## Luồng Use Case liên kết (Core Demo)

```
UC-BOOK-03 (Check Availability)
    → UC-BOOK-01 (Create Booking)
    → UC-BOOK-04 (Check-in) [Transaction]
    → UC-SVC-02 (Add Services)
    → UC-PAY-01 (Generate Invoice)
    → UC-PAY-02/04 (Payment) [Transaction]
    → UC-BOOK-05 (Check-out) [Transaction]
    → UC-RPT-01 (Revenue Report)
```

---

**Tài liệu liên quan:** [use-case-diagram.md](../uml/use-case-diagram.md)
