# Production security checklist

Everything below is **server-side configuration**. The application-level fixes
from the August 2026 audit are in the codebase; these are the items code cannot
enforce for you.

## 1. Environment flags (critical)

The working `.env` currently carries `APP_ENV=local` and `APP_DEBUG=true` while
`APP_URL` points at `https://realtorscircle.com`. If that file is what runs on
the server, every uncaught exception renders Laravel's debug page, which prints
the entire environment: `DB_PASSWORD`, `AWS_SECRET_ACCESS_KEY`, `MAIL_PASSWORD`
and `WHATSAPP_API_KEY` included.

On the server:

```
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=warning
SESSION_SECURE_COOKIE=true
```

Then rebuild the caches — `env()` outside a config file returns `null` once
config is cached, so this must be done as a pair:

```
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 2. Verify `.env` is not web-reachable

The project root contains `.htaccess` and `cgi-bin/`, which suggests shared
hosting where the document root may be the project directory rather than
`public/`. Confirm from outside:

```
curl -I https://realtorscircle.com/.env          # must be 403 or 404
curl -I https://realtorscircle.com/storage/logs/laravel.log
curl -I https://realtorscircle.com/composer.json
```

If any of those return 200, point the vhost's document root at `public/`.

## 3. Rotate the credentials that were in a debug-enabled environment

Assume anything reachable through the debug page is compromised, and rotate:

- `DB_PASSWORD`
- `AWS_ACCESS_KEY_ID` / `AWS_SECRET_ACCESS_KEY` (the live key `AKIA…QAFWT`)
- `MAIL_PASSWORD`
- `WHATSAPP_API_KEY` (and update the value held by n8n)

`APP_KEY` should **not** be rotated casually — it decrypts existing password
reset links and any encrypted column. Rotate it only during a maintenance
window, accepting that outstanding reset links break.

## 4. S3 bucket: KYC documents

See `kyc-storage.md`. The `kycaws` disk is now private in config, but that
governs **new** uploads only. Existing objects under `s3://<bucket>/kyc/` keep
whatever ACL they were written with and must be fixed out-of-band.

## 5. Rate limiting depends on a shared cache

The throttles added in this audit are stored in `CACHE_STORE`, currently
`database`. That is correct and works, but it is comparatively slow on a
credential-stuffing burst. If Redis is available, `CACHE_STORE=redis` is
preferable. Do **not** use `array` or `file` behind more than one PHP worker —
`array` loses state between requests, which silently disables every limit.

## 6. Still outstanding (not part of this audit)

- Mail is sent synchronously on the request thread. Moving it to the queue needs
  a supervised `queue:work` process; until that exists, switching mailables to
  `ShouldQueue` would mean mail silently never sends.
- No error monitoring and no automated database backup.
- Admin `Role` (`super_admin` / `manager` / `support`) exists on `admins_tables`
  but nothing gates on it — every admin can do everything. Out of scope by
  instruction, but worth revisiting.
