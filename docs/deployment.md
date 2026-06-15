# Deployment Checklist

## Laragon (Development)

1. Clone/copy project to `d:\laragon\www\Quan_Ly_Khach_San`
2. `composer install`
3. `npm install && npm run build`
4. Copy `.env.example` → `.env`, set `DB_*`, run `php artisan key:generate`
5. `php artisan migrate:fresh --seed`
6. Laragon → Stop All → Start All
7. Truy cập: http://quan-ly-khach-san.test/login

## Environment Variables

| Variable | Mô tả | Default |
|----------|--------|---------|
| `PAYMENT_DRIVER` | mock / vnpay / momo | mock |
| `SMS_DRIVER` | mock | mock |
| `OTA_DRIVER` | mock | mock |
| `DOOR_LOCK_DRIVER` | mock | mock |

## Production

1. Set `APP_ENV=production`, `APP_DEBUG=false`
2. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
3. Cấu hình web server document root → `public/`
4. Queue worker: `php artisan queue:work` (nếu dùng queue)
5. Cron: `* * * * * php artisan schedule:run`
6. Backup DB định kỳ (module System → Backup mock hoặc mysqldump thực)

## Post-deploy Verification

- [ ] Login superadmin + lễ tân demo
- [ ] Flow: booking → check-in → payment → check-out
- [ ] Reports export Excel/PDF
- [ ] Portal khách: `customer@demo.vn` / `password` → `/portal`
- [ ] `php artisan test`

## Demo Accounts

| Email | Role |
|-------|------|
| superadmin@demo.vn | Super Admin |
| letan.hcm@demo.vn | Lễ tân |
| customer@demo.vn | Khách hàng (Portal) |

Password: `password`
