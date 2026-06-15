# Use Case Diagram — Hotel Management System

**Notation:** Mermaid (có thể render trong GitHub, VS Code, hoặc export PNG)

---

## 1. Tổng quan hệ thống

```mermaid
flowchart TB
    subgraph actors [Actors]
        SA((Super Admin))
        AC((Admin Cong ty))
        QL((Quan ly KS))
        LT((Le tan))
        NV((Nhan vien))
        KH((Khach hang))
    end

    subgraph systemAdmin [Quan ly He thong]
        UC_SYS[Cau hinh he thong]
        UC_LOG[Nhat ky he thong]
        UC_USER[Quan ly nguoi dung]
        UC_ROLE[Vai tro va phan quyen]
        UC_BACKUP[Backup Restore]
    end

    subgraph enterprise [Quan ly Doanh nghiep]
        UC_ENT[Khach san Chi nhanh]
        UC_DEPT[Phong ban Nhan vien]
        UC_SUP[Nha cung cap Ngan hang]
        UC_TAX[Thue Phi]
    end

    subgraph security [Bao mat]
        UC_AUTH[Dang nhap Dang xuat]
        UC_2FA[2FA Session Lock]
    end

    subgraph dailyOps [Van hanh Hang ngay]
        UC_ROOM[Quan ly Phong]
        UC_CUS[Khach hang]
        UC_BOOK[Dat phong Check in out]
        UC_SVC[Dich vu]
        UC_PAY[Thanh toan]
        UC_LUG[Hanh ly]
        UC_RPT[Bao cao]
    end

    subgraph advanced [Quan ly Nang cao]
        UC_PRC[Gia phong dong]
        UC_INV[Kho]
        UC_MNT[Bao tri]
        UC_CTR[Hop dong]
        UC_INT[Tich hop]
    end

    SA --> systemAdmin
    SA --> enterprise
    SA --> security
    AC --> enterprise
    AC --> UC_RPT
    QL --> advanced
    QL --> dailyOps
    LT --> dailyOps
    NV --> UC_SVC
    NV --> UC_LUG
    NV --> UC_MNT
    KH --> UC_BOOK
    KH --> UC_AUTH
    LT --> UC_AUTH
    QL --> UC_AUTH
```

---

## 2. Module Booking — Use Cases

```mermaid
flowchart LR
    LT((Le tan))
    KH((Khach hang))

    subgraph bookingModule [Module Dat phong]
        UC1[UC-BOOK-01 Tao booking offline]
        UC2[UC-BOOK-02 Booking online]
        UC3[UC-BOOK-03 Check availability]
        UC4[UC-BOOK-04 Check-in]
        UC5[UC-BOOK-05 Check-out]
        UC6[UC-BOOK-06 Gia han]
        UC7[UC-BOOK-07 Huy booking]
        UC8[UC-BOOK-08 Doi phong]
        UC9[UC-BOOK-09 Dat nhieu phong]
        UC10[UC-BOOK-10 Dat doan]
    end

    LT --> UC1
    LT --> UC3
    LT --> UC4
    LT --> UC5
    LT --> UC6
    LT --> UC7
    LT --> UC8
    LT --> UC9
    LT --> UC10
    KH --> UC2
    KH --> UC3

    UC1 -.->|include| UC3
    UC2 -.->|include| UC3
    UC4 -.->|extend| UC1
    UC5 -.->|extend| UC4
```

---

## 3. Module Payment — Use Cases

```mermaid
flowchart TB
    LT((Le tan))
    QL((Quan ly))
    KH((Khach hang))

    subgraph paymentModule [Module Thanh toan]
        UC_P1[UC-PAY-01 Tao hoa don]
        UC_P2[UC-PAY-02 Thanh toan tien mat]
        UC_P3[UC-PAY-03 Chuyen khoan]
        UC_P4[UC-PAY-04 Momo VNPay QR]
        UC_P5[UC-PAY-05 Coupon giam gia]
        UC_P6[UC-PAY-06 Hoan tien]
        UC_P7[UC-PAY-07 In hoa don PDF]
    end

    LT --> UC_P1
    LT --> UC_P2
    LT --> UC_P3
    LT --> UC_P4
    LT --> UC_P7
    KH --> UC_P4
    QL --> UC_P6
    UC_P6 -.->|require approve| QL
    UC_P1 -.->|include| UC_P2
```

---

## 4. Phân quyền Actor — Ma trận

| Module | SA | AC | QL | LT | NV | KH |
|--------|:--:|:--:|:--:|:--:|:--:|:--:|
| Hệ thống | ✓ | ◐ | ✗ | ✗ | ✗ | ✗ |
| Doanh nghiệp | ✓ | ✓ | ◐ | ✗ | ✗ | ✗ |
| Phòng | ✓ | ◐ | ✓ | ✓ | ◐ | ✗ |
| Khách hàng | ✓ | ◐ | ✓ | ✓ | ✗ | ◐ |
| Booking | ✓ | ◐ | ✓ | ✓ | ✗ | ✓ |
| Thanh toán | ✓ | ◐ | ✓ | ✓ | ✗ | ◐ |
| Báo cáo | ✓ | ✓ | ✓ | ◐ | ✗ | ✗ |
| Giá động/Kho | ✓ | ◐ | ✓ | ✗ | ◐ | ✗ |

✓ Full | ◐ Partial | ✗ None

---

**Tài liệu liên quan:** [02-use-case-specification.md](../phase-1/02-use-case-specification.md)
