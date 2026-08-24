<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>You're on the Waitlist — Realtors Circle</title>
<style>
  body{margin:0;padding:0;background:#f3f4f6;font-family:Inter,Arial,sans-serif;}
  .wrap{max-width:580px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.08);}
  .header{background:linear-gradient(135deg,#059669 0%,#047857 100%);padding:36px 40px;text-align:center;}
  .header h1{color:#fff;font-size:22px;font-weight:800;margin:0 0 6px;}
  .header p{color:#a7f3d0;font-size:14px;margin:0;}
  .body{padding:36px 40px;}
  .alert{background:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;padding:18px 20px;margin-bottom:24px;}
  .alert p{margin:0;color:#065f46;font-size:14px;line-height:1.6;}
  .note{background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:18px 20px;margin-bottom:24px;}
  .note p{margin:0;font-size:13px;color:#92400e;line-height:1.6;}
  .cta{display:block;width:fit-content;margin:28px auto;padding:13px 36px;background:#059669;color:#fff;text-decoration:none;border-radius:12px;font-size:14px;font-weight:700;text-align:center;}
  .footer{background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #f3f4f6;}
  .footer p{margin:0;font-size:12px;color:#9ca3af;}
  .footer a{color:#059669;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>You're on the Waitlist! 🎉</h1>
    <p>Nigeria's #1 P2P Real Estate Platform — Coming Soon</p>
  </div>

  <div class="body">
    <p style="font-size:16px;font-weight:600;color:#111827;margin:0 0 14px;">
      Hello, {{ $user->full_name ?? ($user->First_name ?? 'Friend') }}!
    </p>

    <p style="font-size:14px;color:#4b5563;line-height:1.7;margin:0 0 20px;">
      Thank you for joining the <strong>Realtors Circle</strong> exclusive early access list.
      You're now officially part of our inner circle before we go live.
    </p>

    <div class="alert">
      <p>
        <strong>You're in!</strong><br>
        We'll notify you the moment the platform launches. Early members get priority access
        and special benefits not available to regular sign-ups.
      </p>
    </div>

    <p style="font-size:14px;color:#4b5563;line-height:1.7;margin:0 0 20px;">
      Realtors Circle is Nigeria's first <strong>Peer-to-Peer (P2P) real estate network</strong> — where
      realtors collaborate to close deals faster, earn commission on referrals, and connect
      with verified buyers and sellers across the country.
    </p>

    <div class="note">
      <p>
        <strong>Know other realtors?</strong><br>
        Share Realtors Circle with your colleagues. The more realtors in the network,
        the faster deals close — and the more commission opportunities for everyone.
      </p>
    </div>

    <a href="{{ url('/') }}" class="cta">Visit Realtors Circle</a>

    <p style="font-size:13px;color:#6b7280;margin:0;">
      Questions? Reach us at <a href="mailto:support@realtorscircle.com" style="color:#059669;font-weight:600;">support@realtorscircle.com</a>
    </p>
  </div>

  <div class="footer">
    <p>© {{ date('Y') }} Realtors Circle &nbsp;·&nbsp; Nigeria's #1 P2P Real Estate Platform</p>
    <p style="margin-top:6px;"><a href="{{ url('/privacy') }}">Privacy Policy</a> &nbsp;·&nbsp; <a href="{{ url('/terms') }}">Terms of Service</a></p>
  </div>
</div>
</body>
</html>
