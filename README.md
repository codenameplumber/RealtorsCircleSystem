# Realtors Circle — Project Status

Nigerian P2P real estate platform built on **Laravel 12 + Livewire 3**.  
Four portals share one dashboard layout: **Realtor Portal** (public "users"), **Inhouse Realtor / Agent Portal**, **Agency Partner Portal**, and **Admin Portal**, alongside the **Public Website**.

---

## 📌 Latest updates — August 2026

A round of security, data, and infrastructure work. Full write-ups are in
[`RealtorsBackend/docs/`](RealtorsBackend/docs/).

**🔗 Shareable references (published Artifacts):**

| Artifact | What it is |
|---|---|
| **[Access Directory](https://claude.ai/code/artifact/6e7c89e9-4515-4397-9602-ee3881f7e7de)** | Verified demo logins for all four portals — 10 realtors, 10 inhouse realtors, 10 agencies, and admin — with copy-to-clipboard and filtering. |
| **[Engineering Brief](https://claude.ai/code/artifact/303eeb96-08c5-499a-90c5-5a0a7100eec7)** | Technical account: security-audit findings, demo-data seeding, the Resend mail switch, and scheduled jobs. |

**What changed:**

- **Security audit** — added rate limiting across every auth/mail/public surface (there was none), fixed an email-verification bypass, non-expiring reset tokens, a leaked password hash, account enumeration, email bombing, mass-assignment on credential columns, session fixation, and made KYC documents private (served via presigned URLs). Test suite **73 → 108 passing (317 assertions)**. Details: [`docs/TECHNICAL_OVERVIEW.md`](RealtorsBackend/docs/TECHNICAL_OVERVIEW.md), [`docs/security/`](RealtorsBackend/docs/security/).
- **Demo data** — `php artisan demo:seed-rich` (idempotent). Live DB now ~**10,114 realtors**, **29,976 properties**, **12,005 commissions**; 120 showcase realtors at 100 listings (with video) + 100 commissions each; 20 agencies at 500 connections.
- **Mail** — switched Mailtrap → **Resend** (native API transport). Set `RESEND_KEY` in `.env`.
- **Scheduled tasks** — first cron jobs added (`tokens:prune-expired`, `auth:clear-resets`) in `routes/console.php`.
- **Demo logins** — see the Access Directory above, or [`docs/USER_GUIDE_AND_LOGINS.md`](RealtorsBackend/docs/USER_GUIDE_AND_LOGINS.md). All demo passwords are `Password@123` except admin (`Admin@1234`).

> ⚠️ Before production: flip `APP_ENV`/`APP_DEBUG` on the server, sweep existing S3 KYC objects to private, and add a supervised queue worker. See [`docs/security/deployment-checklist.md`](RealtorsBackend/docs/security/deployment-checklist.md).

---

## Tech Stack

| Layer | Detail |
|---|---|
| Backend | Laravel 12 |
| Frontend reactivity | Livewire 3 (Alpine.js bundled) |
| Auth — Realtors | Laravel `auth()` guard (`UsersTable` model) |
| Auth — Agents | Session-based (`session('agent_id')`) |
| Auth — Agencies | Session-based (`session('agency_id')`) |
| Auth — Admin | Session-based (`session('admin_id')`) |
| Mail | Resend (native API transport) |
| File storage | AWS S3 — `kycaws` (private, KYC) · `photoaws`/`videoaws` (property media) |
| Column naming | PascalCase (e.g. `First_name`, `Email`, `Password`) |
| Notifications | Mailable classes + Livewire `dispatch('*-notify')` toasts |

---

## 1. Public Website

**Routes prefix:** `/`  
**Layout:** `layouts/public.blade.php`

| Page | Route | Status |
|---|---|---|
| Home | `GET /` | ✅ Done |
| About | `GET /about` | ✅ Done |
| Properties listing | `GET /properties` | ✅ Done |
| Property detail | `GET /properties/{hash}` | ✅ Done |
| Realtors listing | `GET /realtors` | ✅ Done |
| Realtor profile | `GET /realtors/{hash}` | ✅ Done |
| Contact | `GET /contact` | ✅ Done |
| Terms & Privacy | `GET /terms` · `/privacy` | ✅ Done |
| Waitlist | `GET /waitlist` | ✅ Done |

**Livewire components on public pages:**

| Component | Purpose | Status |
|---|---|---|
| `PropertySearch` | Search/filter properties | ✅ Done |
| `RealtorSearch` | Search realtors | ✅ Done |
| `PropertyBooking` | Book a property viewing | ✅ Done |
| `ContactForm` | Contact page form | ✅ Done |
| `WaitingList` | Waitlist signup | ✅ Done |

**What's left — Public:**
- [ ] Realtor rating / review submission (route exists as `POST /api/rate-realtor` inline closure, no Livewire component yet)
- [ ] SEO meta tags (Open Graph, structured data) for property and realtor detail pages

---

## 2. Realtor Portal

**Routes prefix:** `/realtor` · Middleware: `auth` + `RealtorMiddleware`  
**Layout:** `layouts/dashboard.blade.php` (shared)  
**Auth table:** `users_tables` — Model: `UsersTable`

### Auth (guest routes, no prefix)

| Page | Route | Livewire | Status |
|---|---|---|---|
| Login | `GET /login` | `Auth\LoginForm` | ✅ Done |
| Register | `GET /register` | `Auth\RegisterForm` | ✅ Done |
| Forgot Password | `GET /forgot-password` | `Auth\ForgotPassword` | ✅ Done |
| Reset Password | `GET /reset-password/{token}` | — (inline controller) | ✅ Done |
| Logout | `POST /logout` | — | ✅ Done |

### Dashboard Pages

| Page | Route name | Livewire component | Status |
|---|---|---|---|
| Overview | `realtor.dashboard` | — (controller passes vars) | ✅ Done |
| My Properties | `realtor.properties` | — | ✅ Done |
| Add Property | `realtor.properties.create` | `Realtor\PropertyForm` | ✅ Done |
| Property Detail | `realtor.properties.show` | — | ✅ Done |
| Edit Property | `realtor.properties.edit` | `Realtor\PropertyForm` | ✅ Done |
| Leads | `realtor.leads` | — | ✅ Done |
| Appointments | `realtor.appointments` | `Realtor\Appointments` | ✅ Done |
| Commission | `realtor.commission` | — | ✅ Done |
| Referrals | `realtor.referrals` | — | ✅ Done |
| Settings | `realtor.settings` | `Realtor\ProfileSettings` | ✅ Done |

**Settings component covers:**
- Profile update (name, phone, bio, specialization, state/LGA, address)
- Password change (Hash::check on current)
- KYC document upload (id type + front/back image → S3)
- Bank information (separate `bank_information` table)

**Mail classes used by Realtor portal:**

| Mail | Trigger |
|---|---|
| `WelcomeRealtor` | On registration |
| `Users\EmailVerificationMail` | Email verification |
| `Users\PasswordResetMail` | Forgot password |
| `BookingAcknowledgementMail` | Client books a viewing |
| `AppointmentStatusMail` | Appointment confirmed / cancelled |
| `PropertyInquiryNotification` | New inquiry on a property |

**What's left — Realtor:**
- [ ] Eye icon (show/hide) on password fields in auth pages (login, register, reset-password)
- [ ] `wire:loading` spinner on auth form submit buttons
- [ ] Email verification flow — verification link in `EmailVerificationMail` needs to be wired to a verification route and controller
- [ ] Realtor appointment page currently uses plain controller actions (`acceptAppointment`, `rescheduleAppointment`, `cancelAppointment`) — **not Livewire yet** (component `Realtor\Appointments` exists but the blade may still use form POST actions)
- [ ] Notification count badge on bell for realtor fully wired in layout ✅ but bell links go to full pages, not a slide-over panel
- [ ] Profile photo upload (field exists in DB but no upload UI in `ProfileSettings`)
- [ ] Referral code generation / sharing UI

---

## 3. Agent Portal

**Routes prefix:** `/agent` · Middleware: `AgentMiddleware`  
**Layout:** `layouts/dashboard.blade.php` (shared, context-aware via `session('agent_id')`)  
**Auth table:** `agents_tables` — Model: `AgentsTable`  
**Password reset:** Token stored as hashed column on agent row (`password_reset_token` + `password_reset_expires_at`) — no separate table

### Auth

| Page | Route name | Livewire component | Status |
|---|---|---|---|
| Login | `agent.login` | `Agent\Auth\Login` | ✅ Done · eye icon + wire:loading |
| Forgot Password | `agent.forgot-password` | `Agent\Auth\ForgotPassword` | ✅ Done · success card on send |
| Reset Password | `agent.reset-password.show` | `Agent\Auth\ResetPassword` | ✅ Done · eye icons on both fields |
| Logout | `agent.logout` | — | ✅ Done |

### Dashboard Pages

| Page | Route name | Livewire component | Status |
|---|---|---|---|
| Overview | `agent.dashboard` | `Agent\Overview` | ✅ Done |
| Realtor Approvals | `agent.realtors` | `Agent\RealtorApprovals` | ✅ Done |
| Property Approvals | `agent.properties` | `Agent\PropertyApprovals` | ✅ Done |
| Appointments | `agent.appointments` | `Agent\Appointments` | ✅ Done |
| Clients | `agent.clients` | `Agent\Clients` | ✅ Done |
| Commission | `agent.commission` | `Agent\Commission` | ✅ Done |
| Reports | `agent.reports` | `Agent\Reports` | ✅ Done |
| Settings | `agent.settings` | `Agent\Settings` | ✅ Done |

### Feature Detail

| Feature | Detail | Status |
|---|---|---|
| Seeder | 774 agents — 1 per LGA across all 37 Nigerian states + FCT | ✅ Done |
| Email format | `agent{stateIdx}x{lgaIdx}@realtorscircle.test` — no dots before @ | ✅ Done |
| LGA scoping | All queries scoped to `Assigned_area` (LGA) | ✅ Done |
| Realtor approval | Approve → `Status=active, kyc_status=approved`; Reject with reason | ✅ Done |
| Property approval | Approve → `approval_status=approved`; Reject with reason | ✅ Done |
| Appointment emails | Both client AND realtor emailed on confirm/complete/cancel | ✅ Done |
| Reports icons | SVG icons (no number badges) — period filter today/week/month | ✅ Done |
| Settings eye icons | All 3 password fields (current, new, confirm) have toggle | ✅ Done |
| Notifications bell | Live counts — pending realtors + properties + appointments in LGA | ✅ Done |
| Layout header | Search hidden for agents; profile dropdown → `agent.settings` | ✅ Done |
| Bank information | Stored on `agents_tables` columns (`bank_name`, `account_number`, `account_name`) | ✅ Done |

**Mail classes for Agent portal:**

| Mail | Trigger |
|---|---|
| `Agent\AgentPasswordResetMail` | Forgot password |
| `Agent\RealtorApprovedMail` | Realtor approved by agent |
| `Agent\RealtorRejectedMail` | Realtor rejected by agent |
| `Agent\PropertyApprovedMail` | Property approved by agent |
| `Agent\PropertyRejectedMail` | Property rejected by agent |
| `AppointmentStatusMail` | Appointment status change (shared with Realtor portal) |

**What's left — Agent:**
- [ ] Agent profile photo upload
- [ ] Pagination on Clients page (currently grouped query, no paginate)
- [ ] Commission page — track which specific deal earned the commission (link to property)
- [ ] Agent management by Admin (create/suspend/reassign LGA from Admin panel)

---

## 4. Admin Portal

**Routes prefix:** `/admin` · Middleware: `AdminMiddleware`  
**Layout:** `layouts/dashboard.blade.php` (shared, context-aware via `session('admin_id')`)  
**Auth table:** `admins_tables` — Model: `AdminsTable`

### Auth

| Page | Route name | Method | Status |
|---|---|---|---|
| Login | `admin.login` | Controller (plain form) | ✅ Done |
| Logout | `admin.logout` | Controller | ✅ Done |
| Forgot Password | — | — | ❌ Not built |
| Reset Password | — | — | ❌ Not built |

### Dashboard Pages

| Page | Route name | Method | Status |
|---|---|---|---|
| Overview | `admin.dashboard` | Controller — stats + recent | ✅ Done |
| Realtors list | `admin.realtors` | Controller — search + filter | ✅ Done |
| Approvals | `admin.approvals` | Controller — pending list | ✅ Done |
| Properties list | `admin.properties` | Controller — filter | ✅ Done |
| Commission | `admin.commission` | Controller — paginated | ✅ Done |
| Reports | `admin.reports` | Controller — monthly data | ⚠️ Partial |
| Settings | `admin.settings` | Controller — static view | ⚠️ Partial |

### What's left — Admin (Priority)

**Not built at all:**
- [ ] Forgot password / reset password for admin
- [ ] Admin profile management (change name, password)
- [ ] Agent management page — create agents, suspend/activate, reassign LGA
- [ ] View individual realtor full profile (KYC docs, bank info, all listings)
- [ ] View individual property detail from admin
- [ ] Commission management — mark commission as paid, add manual commission entry
- [ ] Admin roles — super_admin vs manager different permission levels (column exists, logic not enforced)
- [ ] Audit log — track admin actions (approvals, suspensions)

**Partially built (needs completion):**
- [ ] Reports page — currently reuses `getMonthlyRegistrations()` data; needs dedicated stats: total deals closed, revenue per month, top-performing realtors/LGAs, agent activity
- [ ] Settings page — form is static HTML, nothing saves to DB; needs Livewire component to persist `platform_name`, commission split percentages
- [ ] Approvals — `approveRealtor()` updates `Status` but **does not send email** to the realtor; same for `approveProperty()`
- [ ] Realtors list — no ability to view full KYC detail, no suspend UI beyond the approval flow
- [ ] No Livewire on any admin page — all interactions use full-page GET/POST; convert to Livewire for inline approve/reject without page reload

---

## Database Migrations Summary

| Migration | Purpose |
|---|---|
| `create_users_tables_table` | Realtors table |
| `create_agents_tables_table` | Agents table |
| `create_admins_tables_table` | Admins table |
| `create_properties_tables_table` | Properties |
| `create_commission_tables_table` | Commission records |
| `create_reports_tables_table` | Reports (unused directly) |
| `create_lead_tables_table` | Leads |
| `create_referal_tables_table` | Referrals |
| `create_reviews_tables_table` | Reviews |
| `create_bank_information_table` | Realtor bank info (separate table) |
| `create_payment_histories_table` | Payment history |
| `create_userwait_lists_table` | Waitlist |
| `create_contact_messages_table` | Contact form submissions |
| `create_property_inquiries_table` | Viewing requests / appointments |
| `update_admins_tables_add_fields` | Added Role, Status, Password to admins |
| `update_agents_tables_add_fields` | Added Assigned_area, Specialization |
| `update_commission_tables_add_fields` | Added split columns |
| `add_kyc_and_bank_to_users_tables` | KYC fields on realtors |
| `make_bvn_nullable...` | BVN optional in bank_information |
| `make_inquiry_contact_nullable...` + `create_realtor_ratings` | Nullable contact + ratings table |
| `add_booking_fields_to_property_inquiries` | preferred_date, booker_id, cancel_reason |
| `update_agents_tables_add_reset_bank_state_fields` | password_reset_token, bank columns, State on agents |

---

## Models

| Model | Table | Key relations |
|---|---|---|
| `UsersTable` | `users_tables` | hasMany PropertiesTable, hasMany PropertyInquiry (as booker) |
| `AgentsTable` | `agents_tables` | areaRealtors(), areaProperties(), areaAppointments() helpers |
| `AdminsTable` | `admins_tables` | — |
| `PropertiesTable` | `properties_tables` | belongsTo UsersTable (user), hasMany PropertyInquiry |
| `PropertyInquiry` | `property_inquiries` | belongsTo PropertiesTable (property), belongsTo UsersTable (booker) |
| `CommissionTable` | `commission_tables` | belongsTo PropertiesTable, listingRealtor, referringRealtor |
| `BankInformation` | `bank_information` | belongsTo UsersTable |
| `RealtorRating` | `realtor_ratings` | belongsTo UsersTable |
| `ContactMessage` | `contact_messages` | — |

---

## Middleware

| Middleware | Guard | Check |
|---|---|---|
| `RealtorMiddleware` | Laravel auth | `auth()->check()` |
| `AgentMiddleware` | Session | `session()->has('agent_id')` |
| `AdminMiddleware` | Session | `session()->has('admin_id')` |

---

## Helpers / Data

| File | Purpose |
|---|---|
| `app/Data/NigeriaLocations.php` | Static list of 37 states + 774 LGAs — `states()`, `lgasFor($state)`, `statesWithLgas()` |
| `app/Helpers/IdHasher.php` | Encode/decode integer IDs to URL-safe hashes for public property/realtor URLs |

---

## Priority Build Order (What to do next)

### Immediate (blocking real usage)
1. **Admin: Email on approve/reject** — `approveRealtor()` and `approveProperty()` should send `WelcomeRealtor` / `RealtorRejectedMail` and `PropertyApprovedMail` / `PropertyRejectedMail`
2. **Admin: Forgot/Reset password** — admin is locked out permanently if password is lost
3. **Realtor auth: Eye icons + wire:loading** — parity with agent auth pages

### High value
4. **Admin: Agent management page** — create agents, suspend, reassign LGA (agents currently only exist via seeder)
5. **Admin: Settings page** — wire the commission split form to actually save
6. **Admin: Individual realtor KYC view** — admins need to see documents before decisions
7. **Admin: Convert approvals to Livewire** — inline approve/reject without full page reload
8. **Realtor: Profile photo upload** — KYC has upload, but avatar is missing

### Nice to have
9. **Admin: Reports** — dedicated stats (top LGAs, top realtors, revenue trend)
10. **Admin: Audit log** — track who approved/rejected what and when
11. **Agent: Commission detail** — link each commission row to its property deal
12. **Public: Realtor rating component** — the `POST /api/rate-realtor` route exists but has no UI
13. **Realtor: Referral code sharing** — referral table exists, no UI to generate/share a code



### FUTURE DEVELOPER (IGNORE)
What to replicate on WhatsApp (highest to lowest value)
Tier 1 — Notifications (easiest, highest ROI)
Replace email for everything time-sensitive. People ignore emails; they read WhatsApp.

Appointment booked → realtor gets WhatsApp instantly
Appointment confirmed/cancelled → client gets WhatsApp
Property approved/rejected → realtor gets WhatsApp
KYC approved → realtor gets WhatsApp
New realtor in your LGA → agent gets WhatsApp
Commission paid → realtor gets WhatsApp
This alone removes the biggest friction point — realtors and agents missing emails.

Tier 2 — Property Search Bot (medium complexity)
User texts the bot a natural query:

"2 bedroom flat Lekki under 2M"

Bot returns 3–5 matching listings with image, price, and a link to the full detail page. No account needed. This captures leads who would never register but will WhatsApp.

Tier 3 — Inspection Booking Without Account
Instead of forcing a user to register to book a viewing:

User clicks "Book Inspection" on property page → WhatsApp opens with a pre-filled message to the agent/realtor → booking gets logged on the platform via the API

This removes the single biggest drop-off point in the funnel.

What NOT to move to WhatsApp
Keep these on the platform — they need UI:

KYC document upload
Property listing creation
Commission history
Agent/Admin dashboards
How to build it (Nigeria-specific)
The best provider for Nigeria is Termii — Nigerian company, supports WhatsApp Business API + SMS fallback, Naira pricing, no Meta Business approval headache. Alternative is Twilio (more expensive, USD).

In Laravel, it's essentially:


// Send a WhatsApp message via Termii
Http::post('https://api.ng.termii.com/api/sms/send', [
    'to'      => $phone,
    'from'    => 'RealtorsCircle',
    'sms'     => "Your appointment has been confirmed for {$date}.",
    'type'    => 'whatsapp',
    'channel' => 'whatsapp',
    'api_key' => config('services.termii.key'),
]);
You'd build a WhatsappService class and call it alongside (or instead of) Mail::send() in the same places notifications already fire.

My recommendation
Start with Tier 1 only — swap notifications from email to WhatsApp. It takes 1–2 days, reuses all the triggers you already have, and will immediately make agents and realtors more responsive. Then evaluate whether the search bot is worth the added complexity for your user base.

Want me to build the WhatsappService and wire it into the existing notification points?
