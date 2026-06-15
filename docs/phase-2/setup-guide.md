# Phase 2 Setup Guide — Database & Bootstrap Layout

## 📋 Tóm tắt Phase 2

**Hoàn thành:**
- ✅ ERD Documentation (45+ tables, ~80 FK relationships)
- ✅ Base Bootstrap 5 Layout (`layouts/app.blade.php`, partials, components)
- ✅ Dynamic Sidebar Navigation (permission-based menu)
- ✅ RolePermissionSeeder (6 roles, 120+ permissions)
- ✅ DemoDataSeeder (1 company, 3 branches, 30 rooms, 8 test users, demo customers)
- ✅ Reusable Blade Components (card, alert, form-group)

**Remaining:**
- ⏳ Run `php artisan migrate:fresh --seed` to populate database
- ⏳ Verify database integrity
- ⏳ Test auth flow in browser

---

## 🗄️ Database Setup

### Prerequisites

```
✓ MySQL 8.0+ (via Laragon)
✓ PHP 8.3+ (via Laragon)
✓ Laravel 12 installed
✓ Composer dependencies installed (run: composer install)
✓ .env configured with MySQL connection
```

### .env Configuration

**Key settings (check if already correct):**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quan_ly_khach_san
DB_USERNAME=root
DB_PASSWORD=

APP_NAME="Quản lý Khách sạn"
APP_ENV=local
APP_DEBUG=true
```

### Fresh Migration & Seeding

**Option 1: Using Laravel Artisan (Recommended)**

```bash
# Navigate to project
cd d:\laragon\www\Quan_Ly_Khach_San

# Generate app key (if needed)
php artisan key:generate

# Fresh migrate + seed
php artisan migrate:fresh --seed

# Expected output:
# ✓ Migrating: 2024_01_01_000000_create_users_table
# ✓ Migrating: 2024_01_01_000001_create_cache_table
# ... (all 15 migrations)
# ✓ Seeded: RolePermissionSeeder
# ✓ Seeded: DemoDataSeeder
```

**Option 2: Using Laragon PHP directly**

```powershell
# In PowerShell
$php = "d:\laragon\bin\php\php-8.3-nts\php.exe"
& $php artisan migrate:fresh --seed
```

**Option 3: Via Laragon Terminal**

1. Open Laragon
2. Click "Terminal" button
3. Run: `php artisan migrate:fresh --seed`

### Database Verification

After migration completes, verify tables were created:

```sql
-- Connect to MySQL (via Laragon Terminal or MySQL Workbench)
USE quan_ly_khach_san;

-- Check table count (should be ~50 tables)
SELECT COUNT(*) as table_count FROM information_schema.tables 
WHERE table_schema = 'quan_ly_khach_san';

-- Sample queries to verify data
SELECT * FROM companies;              -- Should have 1 record
SELECT * FROM branches;               -- Should have 2 records (HCM, ĐN)
SELECT * FROM rooms;                  -- Should have 16 records (8 per branch)
SELECT * FROM users;                  -- Should have 8 records
SELECT * FROM roles;                  -- Should have 6 records

-- Verify audit columns
SELECT id, name, created_by, updated_by, deleted_at 
FROM users LIMIT 1;

-- Verify FK constraints are working
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'quan_ly_khach_san' 
AND REFERENCED_TABLE_NAME IS NOT NULL
LIMIT 10;
```

---

## 🔐 Test Data — Credentials for Login

**6 test user accounts created (all password: `password`):**

| Email | Role | Branch | Permission | Use Case |
|-------|------|--------|-----------|----------|
| superadmin@demo.vn | Super Admin | HCM | All modules | Full system access |
| admin@demo.vn | Company Admin | HCM (default), ĐN | Enterprise + operations | Company-level management |
| manager.hcm@demo.vn | Hotel Manager | HCM | All operations | Branch operations |
| manager.dn@demo.vn | Hotel Manager | ĐN | All operations | Branch operations (ĐN) |
| letan.hcm@demo.vn | Receptionist | HCM | Bookings, payments, rooms | Front desk operations |
| letan.dn@demo.vn | Receptionist | ĐN | Bookings, payments, rooms | Front desk (ĐN) |
| staff@demo.vn | Staff | HCM | Rooms, services, inventory | Housekeeping/maintenance |
| customer@demo.vn | Customer | HCM | Own bookings/payments | Guest portal access |

**Test customer account:**
- Email: customer@demo.vn | Password: password
- Name: Nguyễn Văn A
- Phone: 0912345678
- Loyalty Tier: Gold (1500 points)

---

## 🎨 Layout & Views

### Bootstrap 5 Layout Structure

**File locations:**

```
resources/views/
├── layouts/
│   ├── app.blade.php           ← Main layout (logged-in users)
│   ├── auth.blade.php          ← Auth pages (login, forgot password)
│   ├── guest.blade.php         ← Guest/portal layout
│   └── partials/
│       ├── navbar.blade.php    ← Top navigation bar
│       ├── sidebar.blade.php   ← Left sidebar menu (permission-based)
│       ├── breadcrumb.blade.php ← Breadcrumb navigation
│       └── footer.blade.php    ← Footer
├── components/                  ← Reusable Blade components
│   ├── card.blade.php          ← Bootstrap card wrapper
│   ├── alert.blade.php         ← Alert component
│   ├── form-group.blade.php    ← Form field component
│   └── ...
└── modules/                     ← Feature-specific views
    ├── rooms/
    ├── bookings/
    ├── customers/
    └── ...
```

### CDN Links (Bootstrap 5)

```html
<!-- Bootstrap CSS -->
<link href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<!-- Bootstrap JS Bundle (includes Popper.js) -->
<script src="https://unpkg.com/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Optional: SweetAlert2 for confirmations -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Optional: DataTables for tables -->
<link href="https://cdn.datatables.net/2.1.0/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/2.1.0/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.0/js/dataTables.bootstrap5.min.js"></script>
```

### Reusable Components Usage

**Card Component:**
```blade
<x-card title="Danh sách phòng" icon="door-open">
    <!-- Card content -->
</x-card>
```

**Alert Component:**
```blade
<x-alert type="success" title="Thành công">
    Phòng được tạo thành công!
</x-alert>
```

**Form Group Component:**
```blade
<x-form-group 
    name="room_number" 
    label="Số phòng" 
    type="text"
    placeholder="101, 102, ..."
    required="true"
    help="Ví dụ: 101, 102, ..."
/>
```

---

## 🔒 Authentication & Permissions

### Permission System (Spatie)

**6 Roles defined:**
- `super_admin` — All permissions
- `company_admin` — Company + system (view), enterprise, operations
- `hotel_manager` — Hotel manager + operations
- `receptionist` — Bookings, payments, rooms, customers
- `staff` — Services, inventory, maintenance, luggage
- `customer` — Bookings (own), payments (own), profile

**Permission pattern: `module.action`**

Examples:
- `rooms.view`, `rooms.create`, `rooms.update`, `rooms.delete`
- `bookings.view`, `bookings.create`, `bookings.approve`, `bookings.export`
- `payments.view`, `payments.create`, `payments.approve`, `payments.export`
- `system.view`, `enterprise.view`, `security.view`

**Total: 120+ permissions (15 modules × 8 actions)**

### Authorization in Blade Templates

```blade
<!-- Check single permission -->
@can('rooms.view')
    <a href="{{ route('rooms.index') }}">Phòng</a>
@endcan

<!-- Check multiple permissions (any) -->
@canany(['bookings.create', 'bookings.update'])
    <button>Sửa</button>
@endcanany

<!-- Check role -->
@role('hotel_manager')
    <div>Manager-only content</div>
@endrole
```

### Authorization in Controllers

```php
// In controller
$this->authorize('rooms.view');  // Check permission
$this->authorize('rooms.delete', $room);  // Check resource permission

// In policy
public function update(User $user, Room $room)
{
    return $user->can('rooms.update') && $room->branch_id === $user->current_branch_id;
}
```

---

## 🧪 Manual Testing Checklist

### Test 1: Login as SuperAdmin
1. Go to http://localhost:8000/login
2. Email: `superadmin@demo.vn`
3. Password: `password`
4. Expected: Redirect to dashboard, sidebar shows all menu items
5. ✓ Can access all modules

### Test 2: Login as Receptionist
1. Email: `letan.hcm@demo.vn`
2. Password: `password`
3. Expected: Dashboard + limited sidebar (rooms, bookings, customers, payments, luggage)
4. ✓ Cannot see System or Enterprise menus
5. ✓ Try accessing `/system/users` → should get 403 Forbidden

### Test 3: Permission Check
1. Login as `staff@demo.vn`
2. Expected: Can only see Rooms, Services, Inventory, Maintenance, Luggage
3. ✓ Cannot see Bookings or Payments

### Test 4: Multi-Branch
1. Login as `admin@demo.vn` (Company Admin)
2. Expected: Branch dropdown shows "HCM" and "Đà Nẵng"
3. Switch branch via dropdown
4. ✓ Dashboard filters data by branch

### Test 5: Logout
1. Click user menu → "Đăng xuất"
2. Expected: Redirect to login, session destroyed
3. ✓ Cannot access dashboard without login

### Test 6: Data Verification
1. Login as any user
2. Go to Dashboard → check KPI boxes show data
3. Expected: Room count = 16, Booking count = 0 (no bookings seeded yet), Customers = 1

---

## 📊 Database Statistics

**After `migrate:fresh --seed`:**

```sql
-- Expected row counts
companies:        1
branches:         2
departments:      1
employees:        7
users:            8
roles:            6
permissions:      120
room_types:       2
amenities:        2
floors:           4 (2 per branch)
rooms:            16 (8 per branch)
room_rates:       2 (1 per room_type per branch)
customers:        1
loyalty_tiers:    2
```

---

## 🚀 Next Steps (Phase 3)

After Phase 2 verification:

1. **Phase 3:** Authentication Detail & 2FA Testing
   - Test 2FA flow (Google Authenticator)
   - Session management
   - Account locking (5 failed attempts)
   - Password reset flow

2. **Phase 4:** Dashboard & KPI Widgets
   - Implement dashboard with Chart.js
   - KPI cards: revenue, occupancy, bookings, check-ins
   - DataTables for recent transactions

3. **Phase 5:** CRUD Module Implementation
   - Start with Rooms module (full CRUD)
   - Customers module
   - Services module
   - Then Booking (complex) and Payment (complex)

4. **Phase 6-10:** Advanced features
   - Booking engine with availability checking
   - Payment processing & invoice generation
   - Dynamic pricing rules
   - Reports & export
   - Tests & documentation

---

## 🆘 Troubleshooting

### Issue: Migrations fail with "column does not exist"

**Solution:** Ensure all migrations exist in `database/migrations/` and are in correct order.

```bash
# Check migration files
dir database\migrations
```

### Issue: 403 Forbidden on routes

**Solution:** User may not have permission. Check:
1. User has role assigned
2. Role has permission
3. Route is protected by `@can` or middleware

```blade
@can('rooms.view')
    <-- content visible only if user has this permission -->
@else
    <p>You don't have permission to view this</p>
@endcan
```

### Issue: "Table already exists" error

**Solution:** Database already migrated. Use `migrate` instead of `migrate:fresh`, or drop/recreate database:

```bash
# Option A: Fresh start (deletes all data)
php artisan migrate:fresh --seed

# Option B: Only new migrations
php artisan migrate
```

### Issue: Sideba menu doesn't appear

**Solution:**
1. Check `resources/views/layouts/partials/sidebar.blade.php` exists
2. Verify layout includes it: `@include('layouts.partials.sidebar')`
3. Check CSS for `.sidebar` class in `resources/css/app.css`

---

## 📝 Verification Checklist

After running `php artisan migrate:fresh --seed`:

- [ ] No migration errors
- [ ] No seeder errors
- [ ] Database has ~50 tables
- [ ] Can login with test accounts
- [ ] Dashboard displays without errors
- [ ] Sidebar menu is visible and permission-based
- [ ] Navigation links work
- [ ] Can see test data (rooms, customers, etc.)
- [ ] Bootstrap 5 styling applied correctly
- [ ] No console JavaScript errors

---

**Phase 2 Status: ✅ Complete**

All database structures, seeders, layouts, and components are ready. 
Run `php artisan migrate:fresh --seed` to initialize database and proceed to Phase 3.
