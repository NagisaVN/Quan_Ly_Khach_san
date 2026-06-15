# Activity Diagram — Tạo Booking & Check Availability

**Use Case:** UC-BOOK-01, UC-BOOK-03

```mermaid
flowchart TD
    Start([Bat dau]) --> Login{Da dang nhap?}
    Login -->|Khong| Denied[Tu choi truy cap]
    Login -->|Co| SelectCustomer[Chon hoac tao khach hang]
    SelectCustomer --> InputDates[Nhap check-in check-out]
    InputDates --> ValidateDates{check_out > check_in?}
    ValidateDates -->|Khong| ErrorDate[Hien thi loi ngay]
    ErrorDate --> InputDates
    ValidateDates -->|Co| CheckAvail[Goi BookingService.checkAvailability]
    CheckAvail --> HasRoom{Co phong trong?}
    HasRoom -->|Khong| ErrorRoom[Thong bao het phong BR-11]
    ErrorRoom --> InputDates
    HasRoom -->|Co| SelectRooms[Chon phong loai phong]
    SelectRooms --> CalcPrice[PricingService tinh gia snapshot]
    CalcPrice --> Confirm{Xac nhan dat?}
    Confirm -->|Khong| SelectRooms
    Confirm -->|Co| SaveBooking[Luu booking + booking_rooms]
    SaveBooking --> SendNotif[Gui email xac nhan]
    SendNotif --> End([Ket thuc])
    Denied --> End
```

---

# Activity Diagram — Check-in

**Use Case:** UC-BOOK-04 | **Business Rules:** BR-12, BR-14

```mermaid
flowchart TD
    Start([Bat dau Check-in]) --> OpenBooking[Mo booking confirmed]
    OpenBooking --> CheckStatus{Status = confirmed?}
    CheckStatus -->|Khong| ErrorStatus[Loi trang thai]
    CheckStatus -->|Co| CheckDate{Hom nay >= check_in?}
    CheckDate -->|Khong| ErrorDate[Chua den ngay nhan]
    CheckDate -->|Co| LoopRooms{Duyet tung phong}
    LoopRooms --> CheckMaint{Phong bao tri? BR-12}
    CheckMaint -->|Co| ErrorMaint[Tu choi check-in]
    CheckMaint -->|Khong| NextRoom{Con phong?}
    NextRoom -->|Co| LoopRooms
    NextRoom -->|Khong| BeginTx[DB Transaction BEGIN]
    BeginTx --> UpdateBooking[booking.status = checked_in]
    UpdateBooking --> UpdateRooms[rooms.status = occupied]
    UpdateRooms --> WriteHistory[Ghi booking_history]
    WriteHistory --> CommitTx[COMMIT]
    CommitTx --> PrintReg[In phieu dang ky]
    PrintReg --> End([Ket thuc])
    ErrorStatus --> End
    ErrorDate --> End
    ErrorMaint --> End
```

---

# Activity Diagram — Thanh toán

**Use Case:** UC-PAY-02, UC-PAY-04 | **Business Rule:** BR-15

```mermaid
flowchart TD
    Start([Bat dau thanh toan]) --> OpenInvoice[Mo hoa don booking]
    OpenInvoice --> CheckBalance{balance > 0?}
    CheckBalance -->|Khong| AlreadyPaid[Da thanh toan du]
    CheckBalance -->|Co| SelectMethod[Chon phuong thuc]
    SelectMethod --> MethodType{Loai?}
    MethodType -->|Tien mat| InputCash[Nhap so tien]
    MethodType -->|Momo VNPay| MockGateway[MockAdapter tao URL]
    MockGateway --> SimulatePay[Mo phong thanh toan]
    SimulatePay --> Callback[Nhan callback]
    InputCash --> ValidateAmount{amount hợp lệ?}
    Callback --> ValidateAmount
    ValidateAmount -->|Khong| ErrorAmount[Loi so tien]
    ValidateAmount -->|Co| BeginTx[DB Transaction BEGIN]
    BeginTx --> CreatePayment[Tao payment record]
    CreatePayment --> UpdateInvoice[Cap nhat invoice balance]
    UpdateInvoice --> CreateReceipt[Tao receipt]
    CreateReceipt --> CommitTx[COMMIT]
    CommitTx --> PrintReceipt[In phieu thu PDF]
    PrintReceipt --> End([Ket thuc])
    AlreadyPaid --> End
    ErrorAmount --> SelectMethod
```

---

# Activity Diagram — Check-out

**Use Case:** UC-BOOK-05 | **Business Rules:** BR-13, BR-16

```mermaid
flowchart TD
    Start([Bat dau Check-out]) --> OpenBooking[Mo booking checked_in]
    OpenBooking --> CheckStatus{Status = checked_in?}
    CheckStatus -->|Khong| ErrorStatus[Loi trang thai]
    CheckStatus -->|Co| GetInvoice[Lay hoa don booking]
    GetInvoice --> CheckPaid{balance <= 0? BR-13}
    CheckPaid -->|Khong| RedirectPay[Chuyen man hinh thanh toan]
    RedirectPay --> EndFail([Tam dung])
    CheckPaid -->|Co| ConfirmCheckOut{Xac nhan tra phong?}
    ConfirmCheckOut -->|Khong| EndCancel([Huy])
    ConfirmCheckOut -->|Co| BeginTx[DB Transaction BEGIN]
    BeginTx --> UpdateBooking[status = checked_out checked_out_at]
    UpdateBooking --> UpdateRooms[rooms.status = cleaning]
    UpdateRooms --> LoyaltyPoints[Cong diem loyalty]
    LoyaltyPoints --> WriteHistory[Ghi booking_history]
    WriteHistory --> CommitTx[COMMIT]
    CommitTx --> PrintInvoice[In hoa don final]
    PrintInvoice --> End([Ket thuc])
    ErrorStatus --> EndFail
```

---

**Tài liệu liên quan:**
- [02-use-case-specification.md](../../phase-1/02-use-case-specification.md)
- [03-functional-specification.md](../../phase-1/03-functional-specification.md)
