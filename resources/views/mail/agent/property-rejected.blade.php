<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Property Listing Update</title>
<style>body{font-family:'Inter',Arial,sans-serif;background:#f3f4f6;margin:0;padding:32px 16px}.card{max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)}.header{background:linear-gradient(135deg,#374151,#1f2937);padding:32px;text-align:center}.header h1{color:#fff;font-size:20px;font-weight:700;margin:12px 0 4px}.header p{color:#9ca3af;font-size:13px;margin:0}.body{padding:32px}.greeting{font-size:15px;font-weight:600;color:#111827;margin:0 0 12px}.text{font-size:14px;color:#6b7280;line-height:1.6;margin:0 0 16px}.prop-box{background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:14px;margin:0 0 16px}.prop-title{font-size:14px;font-weight:700;color:#111827;margin:0 0 4px}.prop-sub{font-size:12px;color:#6b7280;margin:0}.reason-box{background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:16px;margin:0 0 20px}.reason-label{font-size:11px;font-weight:600;color:#dc2626;text-transform:uppercase;letter-spacing:.05em;margin:0 0 6px}.reason-text{font-size:13px;color:#7f1d1d;line-height:1.5;margin:0}.btn{display:inline-block;padding:12px 24px;background:#059669;color:#fff;text-decoration:none;border-radius:10px;font-size:13px;font-weight:600}.footer{padding:0 32px 32px;text-align:center;font-size:11px;color:#9ca3af}</style>
</head>
<body>
<div class="card">
    <div class="header">
        <h1>Property Listing Update</h1>
        <p>Realtors Circle</p>
    </div>
    <div class="body">
        <p class="greeting">Hello, {{ $property->user?->First_name }},</p>
        <p class="text">Your area agent has reviewed your property listing and it could not be approved at this time.</p>
        <div class="prop-box">
            <p class="prop-title">{{ $property->title }}</p>
            <p class="prop-sub">{{ $property->neigborhood }}, {{ $property->city }}</p>
        </div>
        <div class="reason-box">
            <p class="reason-label">Reason for rejection</p>
            <p class="reason-text">{{ $reason }}</p>
        </div>
        <p class="text">Please update your listing details to address the issues mentioned and resubmit for review.</p>
        <a href="{{ url('/realtor/properties') }}" class="btn">Update My Listing</a>
    </div>
    <div class="footer">© {{ date('Y') }} Realtors Circle. All rights reserved.</div>
</div>
</body>
</html>
