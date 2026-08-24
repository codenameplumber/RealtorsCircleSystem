<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Welcome to Realtors Circle</title>
<style>
  body{margin:0;padding:0;background:#f3f4f6;font-family:Inter,Arial,sans-serif;}
  .wrap{max-width:580px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.08);}
  .header{background:linear-gradient(135deg,#059669 0%,#047857 100%);padding:36px 40px;text-align:center;}
  .header h1{color:#fff;font-size:22px;font-weight:800;margin:0 0 6px;}
  .header p{color:#a7f3d0;font-size:14px;margin:0;}
  .body{padding:36px 40px;}
  .alert{background:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;padding:18px 20px;margin-bottom:24px;}
  .alert p{margin:0;color:#065f46;font-size:14px;line-height:1.6;}
  .prop-card{background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:20px;margin-bottom:24px;}
  .prop-card h2{margin:0 0 4px;font-size:16px;color:#111827;font-weight:700;}
  .prop-card p{margin:0;font-size:13px;color:#6b7280;}
  .details{border-collapse:collapse;width:100%;margin-bottom:24px;}
  .details td{padding:10px 0;font-size:14px;border-bottom:1px solid #f3f4f6;color:#374151;}
  .details td:first-child{color:#6b7280;width:40%;}
  .badge{display:inline-block;background:#d1fae5;color:#065f46;border-radius:999px;padding:3px 12px;font-size:12px;font-weight:600;}
  .badge-amber{background:#fef3c7;color:#92400e;}
  .note{background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:18px 20px;margin-bottom:24px;}
  .note p{margin:0;font-size:13px;color:#92400e;line-height:1.6;}
  .steps{margin:20px 0 24px;}
  .step{display:flex;align-items:flex-start;gap:14px;margin-bottom:16px;}
  .step-num{width:28px;height:28px;background:#059669;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;margin-top:2px;}
  .step-title{font-weight:600;font-size:13px;color:#111827;margin-bottom:2px;}
  .step-desc{font-size:12px;color:#6b7280;line-height:1.5;}
  .cta{display:block;width:fit-content;margin:28px auto;padding:13px 36px;background:#059669;color:#fff;text-decoration:none;border-radius:12px;font-size:14px;font-weight:700;text-align:center;}
  .footer{background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #f3f4f6;}
  .footer p{margin:0;font-size:12px;color:#9ca3af;}
  .footer a{color:#059669;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>Welcome to the Network!</h1>
    <p>Nigeria's #1 P2P Real Estate Platform</p>
  </div>

  <div class="body">
    <p style="font-size:16px;font-weight:600;color:#111827;margin:0 0 14px;">Hello, {{ $user->First_name }} {{ $user->Last_name }}!</p>

    <p style="font-size:14px;color:#4b5563;line-height:1.7;margin:0 0 20px;">
      Thank you for registering on <strong>Realtors Circle</strong>. We're excited to have you join
      our growing network of professional realtors across all 36 states of Nigeria.
    </p>

    <div class="note">
      <p>
        <strong>⏳ Account Status: Pending Approval</strong><br>
        Our team typically reviews accounts within <strong>24–48 hours</strong>.
        You will receive another email as soon as your account is approved and you can start listing.
      </p>
    </div>

    <p style="font-size:14px;font-weight:700;color:#111827;margin:0 0 16px;">What happens next?</p>
    <div class="steps">
      <div class="step">
        <div class="step-num">1</div>
        <div>
          <div class="step-title">Admin Review</div>
          <div class="step-desc">Our team reviews your profile and credentials to verify your identity.</div>
        </div>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <div>
          <div class="step-title">Account Approved</div>
          <div class="step-desc">You receive a confirmation email and gain full access to your Realtor Dashboard.</div>
        </div>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <div>
          <div class="step-title">Start Earning</div>
          <div class="step-desc">List properties, receive referrals, and close deals with our P2P network.</div>
        </div>
      </div>
    </div>

    <div class="alert">
      <p><strong>Your Registration Details</strong></p>
    </div>

    <table class="details">
      <tr><td>Name</td><td><strong>{{ $user->First_name }} {{ $user->Last_name }}</strong></td></tr>
      <tr><td>Email</td><td><strong>{{ $user->Email }}</strong></td></tr>
      <tr><td>Phone</td><td><strong>{{ $user->Phone }}</strong></td></tr>
      <tr><td>Specialization</td><td><strong>{{ $user->Specialization ?? 'Not specified' }}</strong></td></tr>
      <tr><td>Status</td><td><span class="badge badge-amber">Pending Approval</span></td></tr>
    </table>

    <a href="{{ url('/') }}" class="cta">Visit Realtors Circle</a>

    <p style="font-size:13px;color:#6b7280;margin:0;">
      Questions? Email us at <a href="mailto:support@realtorscircle.com" style="color:#059669;font-weight:600;">support@realtorscircle.com</a>
    </p>
  </div>

  <div class="footer">
    <p>© {{ date('Y') }} Realtors Circle &nbsp;·&nbsp; Nigeria's #1 P2P Real Estate Platform</p>
    <p style="margin-top:6px;"><a href="{{ url('/privacy') }}">Privacy Policy</a> &nbsp;·&nbsp; <a href="{{ url('/terms') }}">Terms of Service</a></p>
  </div>
</div>
</body>
</html>
