# Cashback Portal MVP

Bu yerda **PHP backend + React frontend** asosidagi cashback portal MVP mavjud.

## Muhim eslatma

Bu loyiha **ishga tushadigan MVP/starter kit** ko‘rinishida tayyorlangan:
- public actions sahifalari
- register/login
- user dashboard
- wallet/transactions/clicks/payouts/tickets/referrals
- admin panel uchun API endpointlar
- affiliate click redirect
- Awin-style postback endpoint
- voucher payout request logikasi
- fraud/self-referral uchun basic qoidalar

Bu production darajadagi yakuniy versiya emas. Real launch uchun quyidagilarni kuchaytirish kerak:
- kuchli auth/session hardening
- email verification / reset email real provider bilan
- full GDPR flow
- real Tremendous API credentials
- full admin UI
- queue/cron
- advanced fraud detection
- partner-specific postback mapping

## Tuzilma

- `backend/` — PHP 8.2+ API
- `frontend/` — React + Vite + Tailwind UI

## Backend ishga tushirish

1. PHP 8.2+ o‘rnating
2. `backend/config/config.php` ichida DB sozlang
3. MySQL yoki SQLite tayyorlang
4. `backend/database/schema.sql` ni import qiling
5. Local server:

```bash
cd backend/public
php -S localhost:8080
```

API bazasi:
- `http://localhost:8080`

## Frontend ishga tushirish

```bash
cd frontend
npm install
npm run dev
```

`.env` ichiga:

```env
VITE_API_URL=http://localhost:8080/api
```

## Demo loginlar

Schema import qilgandan keyin seed ma'lumotlardan foydalaning:
- admin: `admin@example.com` / `password123`
- user: `user@example.com` / `password123`

## Asosiy endpointlar

- `POST /api/auth/register`
- `POST /api/auth/login`
- `GET /api/auth/me`
- `GET /api/actions`
- `GET /api/actions/{slug}`
- `POST /api/actions/{id}/click`
- `GET /api/dashboard/summary`
- `GET /api/dashboard/transactions`
- `GET /api/dashboard/clicks`
- `GET /api/dashboard/payouts`
- `GET /api/dashboard/referrals`
- `GET /api/dashboard/tickets`
- `POST /api/dashboard/tickets`
- `POST /api/dashboard/payouts/request`
- `GET /postback/awin`
- `GET /api/admin/actions`
- `POST /api/admin/actions`
- `PATCH /api/admin/transactions/{id}/status`

## Tavsiya

Keyingi bosqichda buni Laravel 11 + Filament ga ko‘chirish eng yaxshi variant bo‘ladi.
