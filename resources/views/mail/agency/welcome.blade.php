<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Welcome — Agency Partner</title>
<style>body{font-family:'Inter',Arial,sans-serif;background:#f3f4f6;margin:0;padding:32px 16px}.card{max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)}.header{background:linear-gradient(135deg,#059669,#047857);padding:36px 32px;text-align:center}.header svg{width:48px;height:48px;color:#fff}.header h1{color:#fff;font-size:22px;font-weight:700;margin:14px 0 4px}.header p{color:#a7f3d0;font-size:13px;margin:0}.body{padding:32px}.greeting{font-size:15px;font-weight:600;color:#111827;margin:0 0 12px}.text{font-size:14px;color:#6b7280;line-height:1.6;margin:0 0 20px}.creds-box{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:20px;margin:0 0 20px}.creds-title{font-size:12px;font-weight:700;color:#064e3b;text-transform:uppercase;letter-spacing:.05em;margin:0 0 12px}.cred-row{display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid #d1fae5;font-size:13px}.cred-row:last-child{border-bottom:none}.cred-label{color:#6b7280;flex-shrink:0;margin-right:16px}.cred-value{font-family:monospace;font-weight:700;color:#065f46;word-break:break-all;text-align:right}.warn-box{background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;margin:0 0 20px;display:flex;gap:10px}.warn-box svg{flex-shrink:0;width:18px;height:18px;color:#d97706;margin-top:1px}.warn-text{font-size:13px;color:#92400e;line-height:1.5}.btn{display:inline-block;padding:13px 28px;background:#059669;color:#fff;text-decoration:none;border-radius:10px;font-size:14px;font-weight:600}.btn-outline{display:inline-block;padding:11px 24px;background:transparent;color:#059669;text-decoration:none;border-radius:10px;font-size:13px;font-weight:600;border:1.5px solid #059669}.divider{border:none;border-top:1px solid #f3f4f6;margin:24px 0}.note{font-size:12px;color:#9ca3af;margin:0;line-height:1.6}.footer{padding:0 32px 32px;text-align:center;font-size:11px;color:#9ca3af}</style>
</head>
<body>
<div class="card">
    <div class="header">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        <h1>You're In!</h1>
        <p>Realtors Circle — Agency Partner Portal</p>
    </div>
    <div class="body">
        <p class="greeting">Welcome, {{ $agency->company_name }}!</p>
        <p class="text">Your Agency Partner account on Realtors Circle has been created. Below are your login credentials — <strong>please sign in and change your password immediately.</strong></p>

        <div class="creds-box">
            <p class="creds-title">Your Login Credentials</p>
            <div class="cred-row">
                <span class="cred-label">Portal URL</span>
                <span class="cred-value">{{ url('/agency/login') }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Email</span>
                <span class="cred-value">{{ $agency->email }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Password</span>
                <span class="cred-value">{{ $plainPassword }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Referral Code</span>
                <span class="cred-value">{{ $agency->referral_code }}</span>
            </div>
        </div>

        <div class="warn-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <p class="warn-text">For your security, please reset your password as soon as you log in. Go to <strong>Settings → Change Password</strong> inside your portal.</p>
        </div>

        <div style="text-align:center;margin:28px 0 20px">
            <a href="{{ $loginUrl }}" class="btn">Log In to My Portal</a>
        </div>

        <hr class="divider">

        <p class="text" style="margin-bottom:12px">Once logged in, you'll find your <strong>referral link</strong> on the Overview page. Share it with realtors — anyone who registers using your link is automatically added to your network.</p>
        <p class="text">Every time a realtor in your network closes a deal, you earn an override commission from the platform's share. Track everything from <strong>My Earnings</strong>.</p>

        <hr class="divider">
        <p class="note">If you didn't expect this email or believe it was sent in error, please contact support immediately. Do not share your password with anyone.</p>
    </div>
    <div class="footer">© {{ date('Y') }} Realtors Circle. All rights reserved.</div>
</div>
</body>
</html>
