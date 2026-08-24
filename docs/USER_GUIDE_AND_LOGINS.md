# Realtors Circle — User Types, Functions & Demo Logins

Four portals share one dashboard layout. Each has its own login and its own
session. All seeded demo passwords are **`Password@123`** except the admin.

---

## 1. Realtor (public "users") — `/login`
Individual listing realtors. Sign up publicly, verify email, await admin approval.

**Dashboard functions:** overview stats · manage own property listings (create/edit, photos + videos) · leads · appointments/bookings · commission history & earnings · payment links · profile & KYC settings.

**5 demo logins** (each has 100 properties + 100 commissions — full dashboards):

| Email | Password |
|-------|----------|
| amaka.s0u1@realtorscircle.test | Password@123 |
| chidi.s0u2@realtorscircle.test | Password@123 |
| femi.s1u0@realtorscircle.test | Password@123 |
| funke.s1u1@realtorscircle.test | Password@123 |
| ibrahim.s1u2@realtorscircle.test | Password@123 |

---

## 2. Inhouse Realtors (Agents) — `/agent/login`
In-house staff scoped to a Local Government Area (LGA). No public signup (admin-created).

**Dashboard functions:** overview · approve/reject realtors in their LGA · approve/reject property listings · appointments · clients · log deals & commission splits · payments · reports · settings.

**5 demo logins:**

| Email | Password | Area |
|-------|----------|------|
| agent0x1@realtorscircle.test | Password@123 | Aba South, Abia |
| agent0x2@realtorscircle.test | Password@123 | Arochukwu, Abia |
| agent0x3@realtorscircle.test | Password@123 | Bende, Abia |
| agent0x4@realtorscircle.test | Password@123 | Ikwuano, Abia |
| agent0x5@realtorscircle.test | Password@123 | Isiala Ngwa North, Abia |

---

## 3. Agency Partners — `/agency/login`
External agencies with a referral code; realtors who sign up under that code join the agency network and earn the agency an override commission.

**Dashboard functions:** overview · network (their connected realtors) · performance · earnings (override commissions) · payments · settings.

**5 demo logins** (each now has **500 connected realtors**):

| Email | Password | Company (referral code) |
|-------|----------|-------------------------|
| premierpropertygroup@realtorscircle.test | Password@123 | Premier Property Group (AGY-WWQLBKQN) |
| cityscaperealtyhub@realtorscircle.test | Password@123 | Cityscape Realty Hub (AGY-CKLXXSAR) |
| coastalhomesnetwork@realtorscircle.test | Password@123 | Coastal Homes Network (AGY-3LRBVVDY) |
| heritagealliancerealty@realtorscircle.test | Password@123 | Heritage Alliance Realty (AGY-U0HEQVTQ) |
| prestigepropertieshub@realtorscircle.test | Password@123 | Prestige Properties Hub (AGY-DQONEFXY) |

---

## 4. Admin — `/admin/login`

**Dashboard functions:** overview · realtors · approvals (realtor + property, with KYC viewing) · properties · agents · agencies · commission & commission settings · payments · reports · audit log · settings.

**1 admin login:**

| Email | Password | Role |
|-------|----------|------|
| admin@realtorscircle.test | Admin@1234 | super_admin |

> The admin password was reset to `Admin@1234` during this work (it had been
> changed since seeding). All admins currently share full access — `Role`
> (`super_admin`/`manager`/`support`) exists but is not yet enforced.

---

### Login gates to be aware of
A realtor account must be **email-verified _and_ status `active`** to sign in
(web and API both enforce this now). All demo accounts above satisfy that.
