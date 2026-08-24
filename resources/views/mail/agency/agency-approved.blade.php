<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Agency Partner Approved</title>
<style>body{font-family:'Inter',Arial,sans-serif;background:#f3f4f6;margin:0;padding:32px 16px}.card{max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)}.header{background:linear-gradient(135deg,#059669,#047857);padding:32px;text-align:center}.header h1{color:#fff;font-size:20px;font-weight:700;margin:12px 0 4px}.header p{color:#a7f3d0;font-size:13px;margin:0}.body{padding:32px}.greeting{font-size:15px;font-weight:600;color:#111827;margin:0 0 12px}.text{font-size:14px;color:#6b7280;line-height:1.6;margin:0 0 16px}.info-box{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:16px;margin:0 0 20px}.info-title{font-size:13px;font-weight:700;color:#064e3b;margin:0 0 4px}.info-sub{font-size:12px;color:#6b7280;margin:0}.code-box{background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px 16px;font-family:monospace;font-size:13px;color:#374151;margin:0 0 20px;word-break:break-all}.btn{display:inline-block;padding:12px 24px;background:#059669;color:#fff;text-decoration:none;border-radius:10px;font-size:13px;font-weight:600}.footer{padding:0 32px 32px;text-align:center;font-size:11px;color:#9ca3af}</style>
</head>
<body>
<div class="card">
    <div class="header">
        <h1>Agency Partner Approved!</h1>
        <p>Realtors Circle</p>
    </div>
    <div class="body">
        <p class="greeting">Welcome, {{ $agency->company_name }}!</p>
        <p class="text">Your Agency Partner application has been <strong>reviewed and approved</strong>. You can now log in to your Agency Portal and start growing your realtor network.</p>
        <div class="info-box">
            <p class="info-title">Your Referral Link</p>
            <p class="info-sub">Share this link with realtors — anyone who registers via it will be automatically linked to your network.</p>
        </div>
        <div class="code-box">{{ url('/register?ref=' . $agency->referral_code) }}</div>
        <p class="text">Every time a realtor in your network closes a deal, you earn an override commission from the platform's share. Track your earnings and network performance from your dashboard.</p>
        <a href="{{ url('/agency/dashboard') }}" class="btn">Go to My Dashboard</a>
    </div>
    <div class="footer">© {{ date('Y') }} Realtors Circle. All rights reserved.</div>
</div>
</body>
</html>
