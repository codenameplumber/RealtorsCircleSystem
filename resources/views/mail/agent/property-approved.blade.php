<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Property Listing Approved</title>
<style>body{font-family:'Inter',Arial,sans-serif;background:#f3f4f6;margin:0;padding:32px 16px}.card{max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)}.header{background:linear-gradient(135deg,#059669,#047857);padding:32px;text-align:center}.header h1{color:#fff;font-size:20px;font-weight:700;margin:12px 0 4px}.header p{color:#a7f3d0;font-size:13px;margin:0}.body{padding:32px}.greeting{font-size:15px;font-weight:600;color:#111827;margin:0 0 12px}.text{font-size:14px;color:#6b7280;line-height:1.6;margin:0 0 16px}.prop-box{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:16px;margin:0 0 24px}.prop-title{font-size:14px;font-weight:700;color:#064e3b;margin:0 0 4px}.prop-sub{font-size:12px;color:#6b7280;margin:0}.btn{display:inline-block;padding:12px 24px;background:#059669;color:#fff;text-decoration:none;border-radius:10px;font-size:13px;font-weight:600}.footer{padding:0 32px 32px;text-align:center;font-size:11px;color:#9ca3af}</style>
</head>
<body>
<div class="card">
    <div class="header">
        <h1>Property Listing Approved!</h1>
        <p>Realtors Circle</p>
    </div>
    <div class="body">
        <p class="greeting">Great news, {{ $property->user?->First_name }}!</p>
        <p class="text">Your property listing has been <strong>reviewed and approved</strong> by your area agent. It is now live and visible to potential clients on Realtors Circle.</p>
        <div class="prop-box">
            <p class="prop-title">{{ $property->title }}</p>
            <p class="prop-sub">{{ $property->neigborhood }}, {{ $property->city }} · ₦{{ $property->price_display }}</p>
        </div>
        <p class="text">Clients can now find and book viewings for your property. Ensure your contact details are up to date to receive inquiries promptly.</p>
        <a href="{{ url('/realtor/properties') }}" class="btn">View My Listings</a>
    </div>
    <div class="footer">© {{ date('Y') }} Realtors Circle. All rights reserved.</div>
</div>
</body>
</html>
