# Entity-Relationship Diagram (ERD) — Hotel Management System (Phase 2)

**Phiên bản:** 2.0 | **Framework:** Laravel 12 + MySQL  
**Tổng số bảng:** ~50 tables | **Foreign Keys:** ~80+ | **Audit Columns:** Applied to ~40 tables

**Quy ước:** Mọi bảng nghiệp vụ có `id` (primary key), `created_at`, `updated_at`, `deleted_at` (soft delete), `created_by`, `updated_by` (audit columns via `HasAuditColumns` trait).

---

## 1. Tổng quan kiến trúc dữ liệu

```mermaid
erDiagram
    companies ||--o{ branches : has
    branches ||--o{ floors : has
    floors ||--o{ rooms : has
    companies ||--o{ room_types : defines
    room_types ||--o{ rooms : categorizes
    customers ||--o{ bookings : makes
    bookings ||--o{ booking_rooms : contains
    rooms ||--o{ booking_rooms : assigned
    bookings ||--o{ invoices : generates
    invoices ||--o{ payments : paid_by
```

---

## 2. System & Auth

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email UK
        string phone
        bigint company_id FK
        bigint current_branch_id FK
        boolean is_active
        tinyint failed_login_attempts
        timestamp locked_until
        text two_factor_secret
        boolean two_factor_enabled
        bigint created_by FK
        bigint updated_by FK
        timestamp deleted_at
    }

    system_configs {
        bigint id PK
        string key UK
        text value
        string group
        string type
    }

    activity_logs {
        bigint id PK
        bigint user_id FK
        string module
        string action
        string subject_type
        bigint subject_id
        json properties
    }

    notifications {
        bigint id PK
        bigint user_id FK
        string type
        string title
        text message
        timestamp read_at
    }

    login_logs {
        bigint id PK
        bigint user_id FK
        string email
        string ip_address
        boolean success
    }

    backups {
        bigint id PK
        string filename
        string path
        bigint size
        string type
        string status
    }

    branch_user {
        bigint id PK
        bigint branch_id FK
        bigint user_id FK
        boolean is_default
    }

    users ||--o{ activity_logs : generates
    users ||--o{ notifications : receives
    users ||--o{ login_logs : attempts
    users }o--o{ branches : "branch_user"
    companies ||--o{ users : employs
    branches ||--o{ users : "current_branch"
```

---

## 3. Enterprise

```mermaid
erDiagram
    companies {
        bigint id PK
        string name
        string code UK
        string tax_code
        string email
        string phone
        boolean is_active
    }

    branches {
        bigint id PK
        bigint company_id FK
        string name
        string code
        time check_in_time
        time check_out_time
        boolean is_active
    }

    departments {
        bigint id PK
        bigint branch_id FK
        string name
        string code
        boolean is_active
    }

    employees {
        bigint id PK
        bigint user_id FK
        bigint branch_id FK
        bigint department_id FK
        string employee_code
        string position
        date hire_date
    }

    suppliers {
        bigint id PK
        bigint company_id FK
        string name
        string code
        string contact_person
        boolean is_active
    }

    bank_accounts {
        bigint id PK
        bigint company_id FK
        bigint branch_id FK
        string bank_name
        string account_number
        boolean is_default
    }

    taxes {
        bigint id PK
        bigint company_id FK
        string name
        string code
        decimal rate
        boolean is_default
    }

    service_fees {
        bigint id PK
        bigint company_id FK
        bigint branch_id FK
        string name
        string type
        decimal value
    }

    companies ||--o{ branches : has
    companies ||--o{ suppliers : uses
    companies ||--o{ taxes : configures
    companies ||--o{ service_fees : defines
    branches ||--o{ departments : has
    branches ||--o{ employees : employs
    branches ||--o{ bank_accounts : owns
    users ||--o| employees : linked
    departments ||--o{ employees : contains
```

---

## 4. Room Management

```mermaid
erDiagram
    room_types {
        bigint id PK
        bigint company_id FK
        string name
        string code
        tinyint max_occupancy
        decimal base_price
        boolean is_active
    }

    amenities {
        bigint id PK
        bigint company_id FK
        string name
        string icon
        boolean is_active
    }

    floors {
        bigint id PK
        bigint branch_id FK
        string name
        int floor_number
        boolean is_active
    }

    rooms {
        bigint id PK
        bigint branch_id FK
        bigint floor_id FK
        bigint room_type_id FK
        string room_number
        string status
        boolean is_active
    }

    room_amenity {
        bigint id PK
        bigint room_id FK
        bigint amenity_id FK
    }

    room_images {
        bigint id PK
        bigint room_id FK
        string path
        boolean is_primary
        smallint sort_order
    }

    room_rates {
        bigint id PK
        bigint branch_id FK
        bigint room_type_id FK
        date effective_from
        date effective_to
        decimal rate
    }

    companies ||--o{ room_types : defines
    companies ||--o{ amenities : defines
    branches ||--o{ floors : has
    branches ||--o{ rooms : contains
    floors ||--o{ rooms : on
    room_types ||--o{ rooms : type
    rooms }o--o{ amenities : room_amenity
    rooms ||--o{ room_images : has
    branches ||--o{ room_rates : prices
    room_types ||--o{ room_rates : for
```

**Room status enum:** `available`, `occupied`, `reserved`, `maintenance`, `cleaning`

---

## 5. Customer & Loyalty

```mermaid
erDiagram
    loyalty_tiers {
        bigint id PK
        bigint company_id FK
        string name
        string code
        int min_points
        decimal discount_percent
        boolean is_active
    }

    customers {
        bigint id PK
        bigint company_id FK
        bigint branch_id FK
        bigint user_id FK
        bigint loyalty_tier_id FK
        string code
        string first_name
        string last_name
        string email
        string phone
        int loyalty_points
        boolean is_active
    }

    customer_documents {
        bigint id PK
        bigint customer_id FK
        string type
        string document_number
        string path
        date expiry_date
    }

    loyalty_transactions {
        bigint id PK
        bigint customer_id FK
        bigint loyalty_tier_id FK
        int points
        string type
        string reference_type
        bigint reference_id
    }

    companies ||--o{ loyalty_tiers : defines
    companies ||--o{ customers : registers
    loyalty_tiers ||--o{ customers : tier
    customers ||--o{ customer_documents : has
    customers ||--o{ loyalty_transactions : earns
    users ||--o| customers : portal
```

---

## 6. Services

```mermaid
erDiagram
    service_categories {
        bigint id PK
        bigint branch_id FK
        string name
        string code
        smallint sort_order
        boolean is_active
    }

    services {
        bigint id PK
        bigint branch_id FK
        bigint service_category_id FK
        string name
        string code
        decimal unit_price
        string unit
        boolean is_active
    }

    branches ||--o{ service_categories : has
    service_categories ||--o{ services : contains
    branches ||--o{ services : offers
```

---

## 7. Booking (Core)

```mermaid
erDiagram
    bookings {
        bigint id PK
        bigint branch_id FK
        bigint customer_id FK
        string booking_code UK
        string status
        date check_in_date
        date check_out_date
        string source
        decimal total_amount
        int version
    }

    booking_rooms {
        bigint id PK
        bigint booking_id FK
        bigint room_id FK
        bigint room_type_id FK
        date check_in_date
        date check_out_date
        decimal rate_snapshot
        int nights
    }

    booking_guests {
        bigint id PK
        bigint booking_id FK
        bigint customer_id FK
        string first_name
        string last_name
        boolean is_primary
    }

    booking_services {
        bigint id PK
        bigint booking_id FK
        bigint service_id FK
        int quantity
        decimal unit_price
        decimal total_amount
    }

    booking_histories {
        bigint id PK
        bigint booking_id FK
        bigint user_id FK
        string action
        string from_status
        string to_status
        json changes
    }

    branches ||--o{ bookings : receives
    customers ||--o{ bookings : makes
    bookings ||--o{ booking_rooms : contains
    rooms ||--o{ booking_rooms : assigned
    bookings ||--o{ booking_guests : has
    bookings ||--o{ booking_services : uses
    services ||--o{ booking_services : provides
    bookings ||--o{ booking_histories : tracks
    users ||--o{ booking_histories : performs
```

**Booking status enum:** `pending`, `confirmed`, `checked_in`, `checked_out`, `cancelled`, `no_show`

**Indexes:** `bookings(branch_id, status)`, `booking_rooms(room_id)`, `booking_rooms(room_id, check_in_date, check_out_date)`

---

## 8. Payment & Invoice

```mermaid
erDiagram
    coupons {
        bigint id PK
        bigint company_id FK
        bigint branch_id FK
        string code UK
        string type
        decimal value
        int usage_limit
        date valid_from
        date valid_to
    }

    invoices {
        bigint id PK
        bigint branch_id FK
        bigint booking_id FK
        bigint customer_id FK
        string invoice_number UK
        string status
        decimal subtotal
        decimal tax_amount
        decimal total_amount
        decimal paid_amount
        decimal balance
        bigint coupon_id FK
    }

    invoice_items {
        bigint id PK
        bigint invoice_id FK
        string item_type
        bigint reference_id
        string description
        int quantity
        decimal unit_price
        decimal total_amount
    }

    payments {
        bigint id PK
        bigint invoice_id FK
        bigint branch_id FK
        string payment_number UK
        decimal amount
        string payment_method
        string status
        timestamp paid_at
    }

    refunds {
        bigint id PK
        bigint payment_id FK
        bigint invoice_id FK
        decimal amount
        string reason
        string status
        bigint approved_by FK
    }

    receipts {
        bigint id PK
        bigint payment_id FK
        string receipt_number UK
        decimal amount
        timestamp issued_at
    }

    bookings ||--o{ invoices : generates
    customers ||--o{ invoices : billed
    invoices ||--o{ invoice_items : contains
    invoices ||--o{ payments : receives
    payments ||--o{ refunds : refunded
    payments ||--o{ receipts : issued
    coupons ||--o{ invoices : applied
```

**Invoice status enum:** `draft`, `issued`, `partial`, `paid`, `cancelled`, `refunded`  
**Payment method enum:** `cash`, `bank`, `momo`, `vnpay`, `qr`

---

## 9. Pricing & Luggage

```mermaid
erDiagram
    pricing_rules {
        bigint id PK
        bigint branch_id FK
        bigint room_type_id FK
        string name
        string type
        json conditions
        string adjustment_type
        decimal value
        smallint priority
    }

    seasonal_rates {
        bigint id PK
        bigint branch_id FK
        bigint room_type_id FK
        date start_date
        date end_date
        decimal rate
    }

    event_rates {
        bigint id PK
        bigint branch_id FK
        bigint room_type_id FK
        date event_date
        decimal rate
    }

    holidays {
        bigint id PK
        bigint company_id FK
        string name
        date date
        boolean is_recurring
        decimal rate_multiplier
    }

    luggage {
        bigint id PK
        bigint branch_id FK
        bigint customer_id FK
        bigint booking_id FK
        string tag_code UK
        string storage_location
        string status
    }

    branches ||--o{ pricing_rules : has
    branches ||--o{ seasonal_rates : has
    branches ||--o{ event_rates : has
    companies ||--o{ holidays : defines
    branches ||--o{ luggage : stores
    customers ||--o{ luggage : owns
    bookings ||--o{ luggage : linked
```

---

## 10. Inventory, Maintenance & Contracts

```mermaid
erDiagram
    products {
        bigint id PK
        bigint branch_id FK
        bigint supplier_id FK
        string name
        string sku
        int stock_quantity
        int min_stock_level
    }

    stock_movements {
        bigint id PK
        bigint branch_id FK
        bigint product_id FK
        string type
        int quantity
        int stock_before
        int stock_after
    }

    stocktakes {
        bigint id PK
        bigint branch_id FK
        string reference_code UK
        string status
    }

    maintenance_requests {
        bigint id PK
        bigint branch_id FK
        bigint room_id FK
        string title
        string priority
        string status
    }

    maintenance_schedules {
        bigint id PK
        bigint branch_id FK
        bigint room_id FK
        string frequency
        date next_due_date
    }

    maintenance_costs {
        bigint id PK
        bigint maintenance_request_id FK
        bigint supplier_id FK
        decimal amount
    }

    contracts {
        bigint id PK
        bigint company_id FK
        bigint branch_id FK
        bigint supplier_id FK
        string contract_number UK
        date start_date
        date end_date
        decimal total_value
        string status
    }

    contract_payments {
        bigint id PK
        bigint contract_id FK
        decimal amount
        date due_date
        string status
    }

    branches ||--o{ products : stocks
    suppliers ||--o{ products : supplies
    products ||--o{ stock_movements : tracks
    branches ||--o{ stocktakes : performs
    rooms ||--o{ maintenance_requests : needs
    branches ||--o{ maintenance_schedules : plans
    maintenance_requests ||--o{ maintenance_costs : costs
    companies ||--o{ contracts : signs
    contracts ||--o{ contract_payments : schedules
```

---

## 11. Integration & RBAC (Spatie)

```mermaid
erDiagram
    integration_logs {
        bigint id PK
        bigint branch_id FK
        string provider
        string action
        string direction
        string status
        json request_payload
        json response_payload
    }

    roles {
        bigint id PK
        string name
        string guard_name
    }

    permissions {
        bigint id PK
        string name
        string guard_name
    }

    model_has_roles {
        bigint role_id FK
        string model_type
        bigint model_id
    }

    role_has_permissions {
        bigint permission_id FK
        bigint role_id FK
    }

    roles }o--o{ permissions : role_has_permissions
    users }o--o{ roles : model_has_roles
    branches ||--o{ integration_logs : logs
```

**6 Roles:** `super_admin`, `company_admin`, `hotel_manager`, `receptionist`, `staff`, `customer`  
**15 Modules × 8 Actions:** `{module}.{view|create|update|delete|approve|export|import|print}`

---

## 12. Migration Order

| # | File | Tables |
|---|------|--------|
| 100000 | extend_users_and_create_system_tables | users*, system_configs, activity_logs, notifications, login_logs, backups |
| 100001 | create_enterprise_tables | companies, branches, departments, employees, suppliers, bank_accounts, taxes, service_fees |
| 100002 | create_room_tables | room_types, amenities, floors, rooms, room_amenity, room_images, room_rates |
| 100003 | create_customer_tables | loyalty_tiers, customers, customer_documents, loyalty_transactions |
| 100004 | create_service_tables | service_categories, services |
| 100005 | create_booking_tables | bookings, booking_rooms, booking_guests, booking_services, booking_histories |
| 100006 | create_payment_tables | coupons, invoices, invoice_items, payments, refunds, receipts |
| 100007 | create_luggage_and_pricing_tables | luggage, pricing_rules, seasonal_rates, event_rates, holidays |
| 100008 | create_inventory_maintenance_contract_tables | products, stock_movements, stocktakes, maintenance_*, contracts, contract_payments |
| 100009 | create_integration_and_branch_user_tables | integration_logs, branch_user |

---

**Tài liệu liên quan:** [01-requirements-analysis.md](../phase-1/01-requirements-analysis.md) | [03-functional-specification.md](../phase-1/03-functional-specification.md)
