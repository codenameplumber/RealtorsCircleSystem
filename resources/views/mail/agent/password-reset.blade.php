<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Reset Your Password</title>
<style>body{font-family:'Inter',Arial,sans-serif;background:#f3f4f6;margin:0;padding:32px 16px}.card{max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)}.header{background:linear-gradient(135deg,#059669,#047857);padding:32px;text-align:center}.header svg{width:44px;height:44px;color:#fff}.header h1{color:#fff;font-size:20px;font-weight:700;margin:12px 0 4px}.header p{color:#a7f3d0;font-size:13px;margin:0}.body{padding:32px}.greeting{font-size:15px;font-weight:600;color:#111827;margin:0 0 12px}.text{font-size:14px;color:#6b7280;line-height:1.6;margin:0 0 24px}.btn{display:inline-block;padding:13px 28px;background:#059669;color:#fff;text-decoration:none;border-radius:10px;font-size:14px;font-weight:600}.note{font-size:12px;color:#9ca3af;margin:24px 0 0;line-height:1.6}.divider{border:none;border-top:1px solid #f3f4f6;margin:24px 0}.footer{padding:0 32px 32px;text-align:center;font-size:11px;color:#9ca3af}</style>
</head>
<body>
<div class="card">
    <div class="header">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <h1>Password Reset Request</h1>
        <p>Realtors Circle — Agent Portal</p>
    </div>
    <div class="body">
        <p class="greeting">Hello, {{ $agent->First_name }}!</p>
        <p class="text">We received a request to reset the password for your agent account. Click the button below to set a new password. This link will expire in <strong>60 minutes</strong>.</p>
        <div style="text-align:center;margin:28px 0">
            <a href="{{ $resetUrl }}" class="btn">Reset My Password</a>
        </div>
        <hr class="divider">
        <p class="note">If you did not request a password reset, please ignore this email — your password will remain unchanged.<br><br>For security, do not share this link with anyone.</p>
    </div>
    <div class="footer">© {{ date('Y') }} Realtors Circle. All rights reserved.</div>
</div>
</body>
</html>
