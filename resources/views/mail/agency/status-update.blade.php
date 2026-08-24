<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Account Status Update</title>
<style>body{font-family:'Inter',Arial,sans-serif;background:#f3f4f6;margin:0;padding:32px 16px}.card{max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)}.header-active{background:linear-gradient(135deg,#059669,#047857);padding:32px;text-align:center}.header-suspended{background:linear-gradient(135deg,#dc2626,#b91c1c);padding:32px;text-align:center}.header-active h1,.header-suspended h1{color:#fff;font-size:20px;font-weight:700;margin:12px 0 4px}.header-active p{color:#a7f3d0;font-size:13px;margin:0}.header-suspended p{color:#fecaca;font-size:13px;margin:0}.header-active svg,.header-suspended svg{width:44px;height:44px;color:#fff}.body{padding:32px}.greeting{font-size:15px;font-weight:600;color:#111827;margin:0 0 12px}.text{font-size:14px;color:#6b7280;line-height:1.6;margin:0 0 16px}.alert-green{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 16px;margin:0 0 20px}.alert-green p{color:#065f46;font-size:13px;line-height:1.5;margin:0}.alert-red{background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 16px;margin:0 0 20px}.alert-red p{color:#991b1b;font-size:13px;line-height:1.5;margin:0}.btn-green{display:inline-block;padding:13px 28px;background:#059669;color:#fff;text-decoration:none;border-radius:10px;font-size:14px;font-weight:600}.divider{border:none;border-top:1px solid #f3f4f6;margin:24px 0}.note{font-size:12px;color:#9ca3af;margin:0;line-height:1.6}.footer{padding:0 32px 32px;text-align:center;font-size:11px;color:#9ca3af}</style>
</head>
<body>
<div class="card">
    @if($status === 'active')
    <div class="header-active">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <h1>Account Reactivated!</h1>
        <p>Realtors Circle — Agency Partner Portal</p>
    </div>
    @else
    <div class="header-suspended">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        <h1>Account Suspended</h1>
        <p>Realtors Circle — Agency Partner Portal</p>
    </div>
    @endif

    <div class="body">
        <p class="greeting">Hello, {{ $agency->company_name }}!</p>

        @if($status === 'active')
        <p class="text">Great news — your Agency Partner account on Realtors Circle has been <strong>reactivated</strong>. You now have full access to your portal, your network, and your earnings.</p>
        <div class="alert-green">
            <p>Your referral link is active again. Realtors who register via your link will be linked to your network and you will continue earning override commissions on closed deals.</p>
        </div>
        <div style="text-align:center;margin:28px 0">
            <a href="{{ $loginUrl }}" class="btn-green">Go to My Portal</a>
        </div>
        @else
        <p class="text">We're writing to inform you that your Agency Partner account on Realtors Circle has been <strong>temporarily suspended</strong>.</p>
        <div class="alert-red">
            <p>While your account is suspended, you will not be able to log in to the portal and your referral link will not accept new registrations. Existing realtors in your network are not affected.</p>
        </div>
        <p class="text">If you believe this is an error or would like to understand the reason, please contact the Realtors Circle admin team directly.</p>
        @endif

        <hr class="divider">
        <p class="note">This is an automated notification from Realtors Circle. Please do not reply to this email.</p>
    </div>
    <div class="footer">© {{ date('Y') }} Realtors Circle. All rights reserved.</div>
</div>
</body>
</html>
