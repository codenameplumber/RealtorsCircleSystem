# Realtors Circle — WhatsApp API (for n8n)

This is the backend API n8n calls to power the WhatsApp flows for Realtors,
In-House Agents, and Agency Partners. n8n owns the WhatsApp Business API
connection, conversation state, and message formatting — this API is a pure
backend: given a phone number and an action, it does the thing and returns
JSON. It never talks to WhatsApp directly.

## Base URL & Auth

```
Base URL (local dev):  http://127.0.0.1:8000/api/wa/v1
Base URL (production):  https://<your-domain>/api/wa/v1
```

Every request must include:

```
X-API-Key: <WHATSAPP_API_KEY from .env>
Content-Type: application/json
```

This is a single shared secret between your n8n instance and the Laravel
app — it proves the request came from n8n, not the open internet. It is
**not** per-user auth. Get the current key from `RealtorsBackend/.env` →
`WHATSAPP_API_KEY`. Rotate it by changing that value and updating n8n's
credential store; do not commit the real key anywhere.

## Identity model — how "login" works

There is no password login over WhatsApp. Every write/read endpoint (except
`register`) takes a `phone` field — the WhatsApp number the message came
from. The API looks up which realtor/agent/agency owns that number and acts
as them. WhatsApp itself is the auth factor: n8n only ever relays messages
from the number that actually sent them, so proving "this request is really
from this phone" is n8n's job, not something the API re-checks with a
password.

Phone numbers are accepted in **any** format — international
(`2348011112222`), with a leading `+`, or local Nigerian format
(`08011112222`) — the API normalizes internally by comparing the last 10
digits. Send whatever number WhatsApp gives you in the webhook payload;
don't reformat it in n8n first.

## Response envelope

Every response follows the same shape:

```json
{ "status": "success", "message": "...", "data": { ... } }
```

or on failure:

```json
{ "status": "error", "message": "...", "errors": { "field": ["..."] } }
```

HTTP status codes: `200`/`201` success, `401` bad/missing API key, `403`
account exists but isn't allowed to do this yet (pending approval,
suspended), `404` no account/record found for that phone or ID, `422`
validation failed.

## Encrypted links (KYC, property photos)

Two flows hand the user a link instead of doing the whole thing over chat,
because photo uploads are painful to do reliably message-by-message in
WhatsApp:

- **KYC completion** — returned by `POST /realtor/register` and
  `POST /realtor/kyc-link`. Opens a mobile web page where the realtor
  uploads their ID (front + back) — no login needed, the link itself is the
  credential (Laravel signed URL, HMAC-verified, expires after 48 hours).
- **Property photos** — returned by `POST /realtor/properties` and
  `POST /realtor/properties/{id}/photo-link`. Same pattern, scoped to one
  property.

Send these links to the user as-is over WhatsApp; don't try to shorten or
rewrite them, since the signature is tied to the exact URL.

## Endpoint reference

### Realtor (`/realtor/*`)

| Method | Path | Body / Query | Notes |
|---|---|---|---|
| POST | `/register` | `first_name, last_name, email, phone, specialization?, referral_code?` | Creates a `pending` account, emails welcome + verification, returns `kyc_link` |
| POST | `/kyc-link` | `phone` | Regenerate the KYC link if the old one expired |
| POST | `/identify` | `phone` | Profile + `can_transact` flag (active AND email-verified) |
| POST | `/properties` | `phone, title, type, description, listing_type, street, neigborhood, city, state, localarea, price, amenities?` | Creates the listing as `pending`, returns `photo_upload_link` |
| POST | `/properties/{id}/photo-link` | `phone` | Regenerate the photo link for an existing property |
| PATCH | `/properties/{id}/status` | `phone, status` (`active\|sold\|rented\|inactive`) | Must be the property's owner |
| GET | `/appointments` | `?phone=` | Pending/upcoming appointments on the realtor's listings |
| POST | `/appointments/{id}/approve` | `phone` | → status `confirmed` |
| POST | `/appointments/{id}/reject` | `phone, reason?` | → status `cancelled` |
| GET | `/search` | `?q=&state=&type=&max_price=` | Public catalog search — no phone needed |
| GET | `/stats` | `?phone=` | `total_assets, active_listings, total_earnings` |
| GET | `/weekly-report` | `?phone=` | Properties sold/rented in the realtor's state, last 7 days |

### In-House Agent (`/agent/*`) — no register, admin-provisioned only

| Method | Path | Body / Query | Notes |
|---|---|---|---|
| POST | `/identify` | `phone` | Profile + `assigned_area` |
| GET | `/realtors/pending` | `?phone=` | Realtors awaiting KYC review, scoped to the agent's LGA |
| POST | `/realtors/{id}/approve` | `phone` | Only works if the realtor is in the agent's LGA |
| POST | `/realtors/{id}/reject` | `phone, reason` (**required**, min 10 chars) | |
| GET | `/properties/pending` | `?phone=` | Properties awaiting approval in the agent's LGA |
| POST | `/properties/{id}/approve` | `phone` | |
| POST | `/properties/{id}/reject` | `phone, reason` (**required**) | |
| PATCH | `/properties/{id}/status` | `phone, status` | Agents can also mark sold/rented/inactive |
| GET | `/appointments` | `?phone=` | Notification feed — every open appointment in the agent's LGA |
| GET | `/stats/weekly` | `?phone=` | New realtors, new properties, approvals — last 7 days |

### Agency (`/agency/*`) — no register, admin-provisioned only, mostly read-only

| Method | Path | Body / Query | Notes |
|---|---|---|---|
| POST | `/identify` | `phone` | Profile + `referral_code` |
| GET | `/network` | `?phone=` | `total_realtors, active_realtors` |
| GET | `/earnings` | `?phone=` | Override commission `total_earned`, `pending`, `deals_count` |
| GET | `/stats/weekly` | `?phone=` | New realtors, deals closed, override earned — last 7 days |

## Example: registering a realtor end-to-end

```
POST /api/wa/v1/realtor/register
{
  "first_name": "Ada", "last_name": "Obi",
  "email": "ada.obi@example.com", "phone": "2348011112222",
  "specialization": "Residential"
}
→ 201
{
  "status": "success",
  "data": {
    "realtor_id": 1113,
    "status": "pending",
    "kyc_link": "http://.../wa/kyc/1113?expires=...&signature=...",
    "kyc_expires": "in 48 hours"
  }
}
```

Send `data.kyc_link` to the user over WhatsApp. Once they submit it, their
`kyc_status` becomes `pending` (awaiting agent review) — poll or notify via
`POST /realtor/identify` to know when an admin/agent has approved them
(`status` flips to `active`).

## Postman collection

`RealtorsCircle-WhatsApp-API.postman_collection.json` + the matching
`.postman_environment.json` in this folder cover every endpoint above,
including the three error-case examples (missing API key, unknown phone,
missing reject reason). Import both into Postman, set `api_key` in the
environment to your real `WHATSAPP_API_KEY`, and run against
`http://127.0.0.1:8000` for local testing.

## What this API does *not* do

- No conversation state / multi-turn memory — that's n8n's job entirely.
- No WhatsApp message sending — this API only returns JSON; n8n formats and
  sends the actual WhatsApp reply.
- No template management — Meta's message-template approval process is
  handled on the n8n/WhatsApp Business side, not here.
