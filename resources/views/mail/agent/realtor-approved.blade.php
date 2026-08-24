<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Account Approved</title>
<style>body{font-family:'Inter',Arial,sans-serif;background:#f3f4f6;margin:0;padding:32px 16px}.card{max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)}.header{background:linear-gradient(135deg,#059669,#047857);padding:32px;text-align:center}.header h1{color:#fff;font-size:20px;font-weight:700;margin:12px 0 4px}.header p{color:#a7f3d0;font-size:13px;margin:0}.badge{display:inline-flex;align-items:center;gap:6px;background:#d1fae5;color:#065f46;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:600;margin-bottom:20px}.body{padding:32px;text-align:center}.greeting{font-size:16px;font-weight:700;color:#111827;margin:0 0 8px}.text{font-size:14px;color:#6b7280;line-height:1.6;margin:0 0 24px}.btn{display:inline-block;padding:13px 28px;background:#059669;color:#fff;text-decoration:none;border-radius:10px;font-size:14px;font-weight:600}.footer{padding:0 32px 32px;text-align:center;font-size:11px;color:#9ca3af}</style>
</head>
<body>
<div class="card">
    <div class="header">
        <h1>Account Approved!</h1>
        <p>Realtors Circle</p>
    </div>
    <div class="body">
        <div class="badge">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            Verified & Active
        </div>
        <p class="greeting">Congratulations, {{ $realtor->First_name }}!</p>
        <p class="text">Your Realtors Circle account has been <strong>verified and approved</strong> by your area agent. You can now list properties, connect with clients, and grow your real estate business on our platform.</p>
        <a href="{{ url('/login') }}" class="btn">Go to Your Dashboard</a>
        <p style="font-size:12px;color:#9ca3af;margin:24px 0 0">Questions? Contact your area agent or our support team.</p>
    </div>
    <div class="footer">© {{ date('Y') }} Realtors Circle. All rights reserved.</div>
</div>
</body>
</html>
