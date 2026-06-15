# Sequence Diagram — Login & RBAC

**Use Case:** UC-SEC-01

```mermaid
sequenceDiagram
    actor User
    participant View as AdminLTE Login
    participant AuthCtrl as AuthController
    participant AuthSvc as AuthService
    participant UserRepo as UserRepository
    participant DB as Database
    participant Session as Session Store

    User->>View: Nhap email password
    View->>AuthCtrl: POST /login
    AuthCtrl->>AuthSvc: authenticate(credentials)
    AuthSvc->>UserRepo: findByEmail(email)
    UserRepo->>DB: SELECT users
    DB-->>UserRepo: user record
    UserRepo-->>AuthSvc: User

    alt Account locked
        AuthSvc-->>AuthCtrl: AccountLockedException
        AuthCtrl-->>View: Error message
    else Invalid password
        AuthSvc->>DB: increment failed_attempts
        AuthSvc-->>AuthCtrl: InvalidCredentials
    else Valid credentials
        AuthSvc->>DB: reset failed_attempts
        AuthSvc->>DB: INSERT login_logs
        alt 2FA enabled
            AuthSvc-->>AuthCtrl: require2FA
            AuthCtrl-->>View: Redirect 2FA page
        else No 2FA
            AuthSvc->>Session: create session
            AuthSvc->>DB: load roles permissions
            AuthCtrl-->>View: Redirect dashboard
        end
    end
```

---

# Sequence Diagram — Tạo Booking

**Use Case:** UC-BOOK-01, UC-BOOK-03

```mermaid
sequenceDiagram
    actor LT as Le tan
    participant Ctrl as BookingController
    participant Req as StoreBookingRequest
    participant Svc as BookingService
    participant PriceSvc as PricingService
    participant RoomRepo as RoomRepository
    participant DB as Database
    participant Notif as NotificationService

    LT->>Ctrl: POST /api/bookings
    Ctrl->>Req: validate()
    Req-->>Ctrl: validated data

    Ctrl->>Svc: createBooking(dto)
    Svc->>RoomRepo: checkAvailability(rooms, dates)
    RoomRepo->>DB: query overlap bookings
    DB-->>RoomRepo: result

    alt Room conflict BR-11
        RoomRepo-->>Svc: conflict list
        Svc-->>Ctrl: RoomNotAvailableException
        Ctrl-->>LT: 422 Error
    else Available
        Svc->>PriceSvc: calculateRates(rooms, dates, customer)
        PriceSvc-->>Svc: rate snapshots

        Svc->>DB: BEGIN TRANSACTION
        Svc->>DB: INSERT bookings
        Svc->>DB: INSERT booking_rooms with rate_snapshot
        Svc->>DB: INSERT booking_history
        Svc->>DB: COMMIT

        Svc->>Notif: sendBookingConfirmation()
        Svc-->>Ctrl: BookingResource
        Ctrl-->>LT: 201 Created JSON
    end
```

---

# Sequence Diagram — Check-in (Transaction)

**Use Case:** UC-BOOK-04 | **Rules:** BR-12, BR-14

```mermaid
sequenceDiagram
    actor LT as Le tan
    participant Ctrl as BookingController
    participant Svc as BookingService
    participant Policy as BookingPolicy
    participant DB as Database

    LT->>Ctrl: POST /bookings/{id}/check-in
    Ctrl->>Policy: checkIn(user, booking)
    Policy-->>Ctrl: authorized

    Ctrl->>Svc: checkIn(bookingId)
    Svc->>DB: SELECT booking FOR UPDATE

    loop Each room
        Svc->>Svc: validate status != maintenance
        alt Room in maintenance BR-12
            Svc-->>Ctrl: RoomMaintenanceException
            Ctrl-->>LT: 422 Error
        end
    end

    Svc->>DB: BEGIN TRANSACTION
    Svc->>DB: UPDATE bookings SET checked_in
    Svc->>DB: UPDATE rooms SET occupied
    Svc->>DB: INSERT booking_history
    Svc->>DB: COMMIT

    Svc-->>Ctrl: BookingResource
    Ctrl-->>LT: 200 Success + print URL
```

---

# Sequence Diagram — Thanh toán (Transaction)

**Use Case:** UC-PAY-02, UC-PAY-04 | **Rule:** BR-15

```mermaid
sequenceDiagram
    actor LT as Le tan
    participant Ctrl as PaymentController
    participant Svc as PaymentService
    participant Adapter as MockPaymentAdapter
    participant DB as Database

    LT->>Ctrl: POST /invoices/{id}/payments
    Ctrl->>Svc: processPayment(invoiceId, dto)

    Svc->>DB: SELECT invoice FOR UPDATE
    Svc->>Svc: validate amount <= balance

    alt Mock gateway Momo VNPay
        Svc->>Adapter: createPaymentUrl(amount)
        Adapter-->>Svc: payment_url + transaction_id
        Svc-->>Ctrl: pending payment
        Ctrl-->>LT: Redirect mock payment page

        Note over LT,Adapter: User simulates payment

        Adapter->>Ctrl: GET /payment/callback
        Ctrl->>Svc: confirmPayment(transaction_id)
    end

    Svc->>DB: BEGIN TRANSACTION
    Svc->>DB: INSERT payments
    Svc->>DB: UPDATE invoices SET paid balance
    Svc->>DB: INSERT receipts
    Svc->>DB: COMMIT

    Svc-->>Ctrl: PaymentResource
    Ctrl-->>LT: 200 Success receipt PDF
```

---

**Tài liệu liên quan:**
- [core-flows.md](../activity-diagrams/core-flows.md)
- [03-functional-specification.md](../../phase-1/03-functional-specification.md)
