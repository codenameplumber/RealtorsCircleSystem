# KYC document storage

## What changed

KYC scans are government-issued ID documents. They were being written to S3 and
recorded as **permanent public URLs**, which were then rendered directly into
`<img src>` in the admin, agent and realtor views. Anyone holding or guessing
one of those URLs could read the document, with no session and no expiry.

As of the August 2026 audit:

- The `kycaws` disk declares `'visibility' => 'private'` and `'scheme' => 'https'`
  (`config/filesystems.php`).
- Upload paths store the **object key**, not a URL —
  `WhatsappUploadController::submitKyc` and `Livewire\Realtor\ProfileSettings`.
- Views read `$user->kyc_front_link` / `$user->kyc_back_link`, accessors on
  `UsersTable` that mint a 15-minute presigned URL at render time. They accept
  both the new bare keys and the legacy full URLs already in the database, so no
  data backfill is required.
- `kyc_front_url` / `kyc_back_url` are still used for *presence* checks
  (`@if($user->kyc_front_url)`) — that is intentional, those read the raw column.

## What you still have to do on AWS

Config only governs objects uploaded from now on. Everything already in
`s3://charsac-realtorscircle/kyc/` keeps the ACL it was created with.

**1. Check whether the bucket is currently public.**

```
aws s3api get-public-access-block --bucket charsac-realtorscircle
aws s3api get-bucket-policy --bucket charsac-realtorscircle
aws s3api get-object-acl --bucket charsac-realtorscircle --key kyc/front/<some-file>
```

If `get-object-acl` lists a grant to `http://acs.amazonaws.com/groups/global/AllUsers`,
those documents are readable by the entire internet and this is a live data
exposure, not a theoretical one.

**2. Make the existing KYC objects private.**

```
aws s3 cp s3://charsac-realtorscircle/kyc/ s3://charsac-realtorscircle/kyc/ \
    --recursive --acl private --metadata-directive REPLACE
```

**3. Block public access at the bucket level.**

Property photos live in the same bucket under `properties/` and *are* meant to
be public, so a blanket public-access block will break listing images. Either:

- keep the bucket's public-access block off and rely on per-object ACLs (what
  the code now does — private KYC, public property media); or
- better, move KYC to a separate bucket with `BlockPublicAcls=true` and point
  `kycaws` at it via its own `AWS_KYC_BUCKET` variable. This is the more robust
  arrangement, because it stops a future mistake in the property-upload path
  from ever affecting ID documents.

**4. Consider whether this was a reportable exposure.**

If step 1 shows the documents were world-readable, Nigeria's NDPR obligates an
assessment and, depending on scope, notification. That is a decision for you,
not something the code can settle — but note that S3 access logs (if enabled)
are the only way to tell whether anything was actually fetched.
