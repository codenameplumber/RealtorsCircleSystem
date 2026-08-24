# Realtors Circle — Technical Overview (Aug 2026 work)

This document covers the security hardening, the demo-data seeding, the mail
switch to Resend, and the scheduled tasks added in this round of work.

---

## 1. Security audit (fully implemented, test-covered)

The application had **no rate limiting of any kind** and several critical auth
flaws. All of the below are fixed and covered by tests in
`tests/Feature/Security/`. Test suite went **73 → 108 passing (317 assertions)**.

### Critical
| # | Issue | Fix |
|---|-------|-----|
| 1 | **Email-verification bypass** — the API verify endpoint ignored its token and trusted `?email=`, so anyone could verify any account | Token now stored as a hash (`email_verification_token`), single-use, 48h expiry, checked on every verify |
| 2 | **Reset tokens never expired** — Carbon 3 returns a *signed* diff, so `now()->diffInMinutes($past) > 60` was never true | Compare against an absolute deadline instead |
| 3 | **Zero rate limiting** | 8 named limiters + `throttle:` on every auth/mail/public route; Livewire forms throttle in-component (see §1.1); `/livewire/update` given a global ceiling |
| 4 | **`APP_DEBUG=true` in production env** | `.env.example` hardened; deployment checklist written (`docs/security/deployment-checklist.md`) — your live `.env` is left for you to flip |

### High
- **Bcrypt hash leaked** into API responses (`$hidden` said `password`, column is `Password`) — both spellings now hidden.
- **Exception text returned to clients** (SMTP creds, paths) — removed from 3 spots.
- **Email bombing** — per-recipient mail cap (`App\Support\EmailThrottle`) independent of IP.
- **Account enumeration** on forgot-password — `exists:` rule removed; all outcomes identical.
- **KYC documents public** — `kycaws` disk now private; documents stored as object keys and served via 15-min presigned URLs (`UsersTable::getKycFrontLinkAttribute`). *Existing S3 objects still need an ACL sweep — see `docs/security/kyc-storage.md`.*
- **Mass assignment** — credential columns (`api_token`, `email_verified_at`, `password_reset_token`, `payment_token`, `Role`) guarded across 5 models; written via `forceFill`.
- **Rating fraud** — clearing cookies bought unlimited ratings; added salted `ip_hash` fingerprint.

### Medium
- `svg` removed from the photo mime whitelist (stored-XSS vector).
- `env('AWS_URL')` at runtime → `config(...)` (returned null after `config:cache`).
- Session fixation on all three session-based portals — `session()->regenerate()` on login, full `invalidate()` on logout.

### Also found & fixed while auditing
- **`Profile::step3` self-activated the account** (`Status => active`), skipping admin approval — removed.
- **`AgencyDashboardController::resetPassword`** used a non-constant-time token compare — now `hash_equals`.
- **`getAuthPassword()` threw on the live DB** — the live `users_tables` column is lowercase `password` while the migration declares `Password`, so `$this->Password` was null and **API login (`Auth::attempt`) was broken on the real database.** Now reads the raw attributes and accepts either spelling. (Root cause: the live DB was imported from an older SQL dump whose column casing differs from the migrations.)

### 1.1 Why Livewire needed special handling
Every Livewire action POSTs to the single `/livewire/update` route, so route
`throttle:` middleware can only ever throttle *all* Livewire traffic in
aggregate. Sensitive components therefore self-limit via the
`App\Livewire\Concerns\ThrottlesAttempts` trait, keyed on action + identifier +
IP. Anything that sends mail also passes a per-IP ceiling, or an attacker
defeats the per-address budget just by rotating the email field.

### 1.2 New migrations (applied to the live DB)
- `2026_08_20_090000_add_ip_hash_to_realtor_ratings`
- `2026_08_20_090100_add_email_verification_token_to_users_tables`

> Note: the live DB was built from a SQL dump, so the base Laravel migrations
> aren't recorded in the `migrations` table. Run new migrations with
> `--path=` to avoid `migrate` trying to recreate existing base tables.

---

## 2. Demo data (`php artisan demo:seed-rich`)

An **idempotent** command (`app/Console/Commands/SeedRichDemoData.php`) that
tops *up* to targets rather than inserting fixed batches — safe to re-run.

Seeded state now:
- **10,114 realtors**, **29,976 properties**, **12,005 commissions**
- **120 showcase realtors** each with exactly **100 properties (photos + real sample video URLs)** and **100 commissions** (realistic status spread: ~50% paid, ~20% processing, ~20% pending, ~10% disputed; ~⅓ are Model-1 referral splits)
- **20 agencies each grown to 500 connections**; agency override earnings populate (e.g. agency #1 ≈ ₦386M paid overrides)

Re-run or adjust with options, e.g.:
```
php artisan demo:seed-rich --showcase=200 --connections=500
```

---

## 3. Mail: Mailtrap → Resend

- Installed `resend/resend-php`; `MAIL_MAILER=resend` (native API transport, verified booting).
- Mailtrap block **commented out** in `.env` (kept for local testing).
- **You must set `RESEND_KEY=re_...` in `.env`** and ensure `MAIL_FROM_ADDRESS` is on a domain verified in Resend. Nothing sends until the key is set.
- Mail is still sent **synchronously** (no queue worker yet) — see §4.

---

## 4. Scheduled tasks

There were **none** before. Added (`routes/console.php`):

| Schedule | Command | Purpose |
|----------|---------|---------|
| Daily 03:00 | `tokens:prune-expired` | Clears expired verification + password-reset tokens across all portals |
| Daily 00:00 | `auth:clear-resets` | Laravel's built-in reset-token cleanup |

Activate with one system cron entry:
```
* * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1
```

### Not scheduled, but recommended next
- **Queue worker** (`php artisan queue:work`, supervised) — prerequisite for moving the ~40 synchronous `Mail::send` calls to `ShouldQueue`. Don't queue mailables until this exists or mail silently stops.
- Payment-gateway reconciliation / payout processing — once Paystack is integrated (`PaymentLinkController` is still a stub).
