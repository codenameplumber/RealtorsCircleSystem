<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Welcome to Realtors Circle</title>
<style>
  body{margin:0;padding:0;background:#f3f4f6;font-family:Inter,Arial,sans-serif;}
  .wrap{max-width:580px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.08);}
  .header{background:linear-gradient(135deg,#059669 0%,#047857 100%);padding:40px 40px 32px;text-align:center;}
  .header-icon{width:56px;height:56px;background:rgba(255,255,255,.15);border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;}
  .header h1{color:#fff;font-size:24px;font-weight:800;margin:0 0 6px;}
  .header p{color:#a7f3d0;font-size:14px;margin:0;}
  .body{padding:36px 40px;}
  .greeting{font-size:18px;font-weight:700;color:#111827;margin:0 0 16px;}
  .intro{font-size:14px;color:#4b5563;line-height:1.75;margin:0 0 28px;}
  .credentials-box{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:24px 28px;margin-bottom:28px;}
  .credentials-box h3{font-size:12px;font-weight:700;color:#065f46;text-transform:uppercase;letter-spacing:.08em;margin:0 0 16px;}
  .cred-row{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #d1fae5;}
  .cred-row:last-child{border-bottom:none;}
  .cred-label{font-size:12px;font-weight:600;color:#6b7280;width:90px;flex-shrink:0;}
  .cred-value{font-size:14px;font-weight:700;color:#111827;word-break:break-all;}
  .cta{display:block;width:fit-content;margin:0 auto 28px;padding:14px 40px;background:#059669;color:#fff;text-decoration:none;border-radius:12px;font-size:15px;font-weight:700;text-align:center;letter-spacing:.01em;}
  .warning-box{background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:18px 22px;margin-bottom:24px;}
  .warning-box p{margin:0;font-size:13px;color:#92400e;line-height:1.65;}
  .info-box{background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:18px 22px;margin-bottom:24px;}
  .info-box p{margin:0;font-size:13px;color:#1e40af;line-height:1.65;}
  .divider{height:1px;background:#f3f4f6;margin:24px 0;}
  .tips-title{font-size:13px;font-weight:700;color:#374151;margin:0 0 12px;}
  .tips-list{margin:0;padding:0 0 0 20px;}
  .tips-list li{font-size:13px;color:#6b7280;line-height:1.65;margin-bottom:6px;}
  .sign-off{font-size:14px;color:#374151;line-height:1.75;margin:24px 0 0;}
  .footer{background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #f3f4f6;}
  .footer p{margin:0;font-size:12px;color:#9ca3af;}
  .footer a{color:#059669;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <div class="header-icon">
      <svg width="28" height="28" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
      </svg>
    </div>
    <h1>Welcome to the Team!</h1>
    <p>Your Realtors Circle agent account is ready</p>
  </div>

  <div class="body">

    <p class="greeting">Dear {{ $agent->First_name }} {{ $agent->Last_name }},</p>

    <p class="intro">
      We are absolutely delighted to welcome you as an esteemed member of the <strong>Realtors Circle</strong> field agent team.
      Your expertise and dedication are exactly what we need, and we cannot wait to see the incredible impact you will make
      in your community. Below are your account credentials — please keep them safe and confidential.
    </p>

    {{-- Credentials --}}
    <div class="credentials-box">
      <h3>Your Login Credentials</h3>
      <div class="cred-row">
        <span class="cred-label">Email</span>
        <span class="cred-value">{{ $agent->Email }}</span>
      </div>
      <div class="cred-row">
        <span class="cred-label">Password</span>
        <span class="cred-value">{{ $plainPassword }}</span>
      </div>
      <div class="cred-row">
        <span class="cred-label">State</span>
        <span class="cred-value">{{ $agent->State }}</span>
      </div>
      <div class="cred-row">
        <span class="cred-label">Area (LGA)</span>
        <span class="cred-value">{{ $agent->Assigned_area }}</span>
      </div>
      <div class="cred-row">
        <span class="cred-label">Specialization</span>
        <span class="cred-value">{{ $agent->Specialization }}</span>
      </div>
    </div>

    {{-- Login CTA --}}
    <a href="{{ $loginUrl }}" class="cta">Access Your Dashboard →</a>

    {{-- Security Warning --}}
    <div class="warning-box">
      <p>
        <strong>🔒 Important — Reset Your Password Immediately</strong><br>
        For the security of your account, please log in and <strong>change your password</strong> right away.
        Your temporary password above should not be kept as your permanent one. Choose something strong and unique.
      </p>
    </div>

    {{-- Info --}}
    <div class="info-box">
      <p>
        <strong>What happens next?</strong><br>
        Once you log in, you will have access to your agent dashboard where you can view realtors and properties
        in your area, track deals, and manage your appointments. Your team is here to support you every step of the way.
      </p>
    </div>

    <div class="divider"></div>

    <p class="tips-title">Tips to get started:</p>
    <ul class="tips-list">
      <li>Log in and update your profile with a clear headshot and bio</li>
      <li>Familiarise yourself with the realtors and properties in your assigned area</li>
      <li>Reach out to your team lead if you have any questions or need onboarding support</li>
      <li>Always use the platform to log your deals — this is how your commissions are tracked</li>
    </ul>

    <p class="sign-off">
      We have great confidence in you and believe you will be an outstanding addition to the Realtors Circle family.
      Welcome aboard — let's build something remarkable together.<br><br>
      With warm regards,<br>
      <strong>The Realtors Circle Admin Team</strong>
    </p>

  </div>

  <div class="footer">
    <p>© {{ date('Y') }} Realtors Circle &nbsp;·&nbsp; Nigeria's #1 P2P Real Estate Platform</p>
    <p style="margin-top:6px;">
      <a href="{{ url('/privacy') }}">Privacy Policy</a> &nbsp;·&nbsp;
      <a href="{{ url('/terms') }}">Terms of Service</a>
    </p>
    <p style="margin-top:8px;font-size:11px;color:#d1d5db;">
      This email was sent to {{ $agent->Email }} because you were registered as an agent on Realtors Circle.
    </p>
  </div>

</div>
</body>
</html>
