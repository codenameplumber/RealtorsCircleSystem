<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Verify Your Email — Realtors Circle</title>
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
  .url-box{background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:14px 16px;margin:16px 0;word-break:break-all;font-size:12px;color:#6b7280;}
  .footer{background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #f3f4f6;}
  .footer p{margin:0;font-size:12px;color:#9ca3af;}
  .footer a{color:#059669;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>Verify Your Email Address</h1>
    <p>One quick step to activate your account</p>
  </div>

  <div class="body">
    <p style="font-size:16px;font-weight:600;color:#111827;margin:0 0 14px;">
      Hello, {{ $user->First_name }} {{ $user->Last_name }}!
    </p>

    @if(isset($bodyMessage) && $bodyMessage)
      <p style="font-size:14px;color:#4b5563;line-height:1.7;margin:0 0 20px;">{{ $bodyMessage }}</p>
    @else
      <p style="font-size:14px;color:#4b5563;line-height:1.7;margin:0 0 20px;">
        Thank you for registering with <strong>Realtors Circle</strong>. To complete your registration
        and secure your account, please verify your email address by clicking the button below.
      </p>
    @endif

    <div class="alert">
      <p>
        <strong>Why verify?</strong><br>
        Email verification helps us keep your account secure and ensures you receive
        important notifications about your listings, leads, and commission payouts.
      </p>
    </div>

    @if(isset($verificationUrl))
      <a href="{{ $verificationUrl }}" class="cta">Verify Email Address</a>

      <p style="text-align:center;font-size:12px;color:#9ca3af;margin:0 0 8px;">
        Button not working? Copy and paste this link into your browser:
      </p>
      <div class="url-box">{{ $verificationUrl }}</div>
    @endif

    <div class="note">
      <p>
        <strong>Didn't register?</strong><br>
        If you did not create an account on Realtors Circle, you can safely ignore this email.
        No action is required.
      </p>
    </div>

    <p style="font-size:13px;color:#6b7280;margin:0;">
      Questions? Contact us at <a href="mailto:support@realtorscircle.com" style="color:#059669;font-weight:600;">support@realtorscircle.com</a>
    </p>
  </div>

  <div class="footer">
    <p>© {{ date('Y') }} Realtors Circle &nbsp;·&nbsp; Nigeria's #1 P2P Real Estate Platform</p>
    <p style="margin-top:6px;"><a href="{{ url('/privacy') }}">Privacy Policy</a> &nbsp;·&nbsp; <a href="{{ url('/terms') }}">Terms of Service</a></p>
  </div>
</div>
</body>
</html>
