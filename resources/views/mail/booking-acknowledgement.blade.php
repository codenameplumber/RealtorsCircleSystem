<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Viewing Request Received — Realtors Circle</title>
<style>
  body{margin:0;padding:0;background:#f3f4f6;font-family:Inter,Arial,sans-serif;}
  .wrap{max-width:580px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.08);}
  .header{background:linear-gradient(135deg,#059669 0%,#047857 100%);padding:36px 40px;text-align:center;}
  .header h1{color:#fff;font-size:22px;font-weight:800;margin:0 0 6px;}
  .header p{color:#a7f3d0;font-size:14px;margin:0;}
  .body{padding:36px 40px;}
  .alert{background:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;padding:18px 20px;margin-bottom:24px;}
  .alert p{margin:0;color:#065f46;font-size:14px;line-height:1.6;}
  .details{border-collapse:collapse;width:100%;margin-bottom:24px;}
  .details td{padding:10px 0;font-size:14px;border-bottom:1px solid #f3f4f6;color:#374151;}
  .details td:first-child{color:#6b7280;width:40%;}
  .badge{display:inline-block;background:#d1fae5;color:#065f46;border-radius:999px;padding:3px 12px;font-size:12px;font-weight:600;}
  .note{background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:18px 20px;margin-bottom:24px;}
  .note p{margin:0;font-size:13px;color:#92400e;line-height:1.6;}
  .footer{background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #f3f4f6;}
  .footer p{margin:0;font-size:12px;color:#9ca3af;}
  .footer a{color:#059669;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>Viewing Request Received!</h1>
    <p>We've got your request — here's what happens next</p>
  </div>

  <div class="body">
    <p style="font-size:16px;font-weight:600;color:#111827;margin:0 0 14px;">Hello, {{ $bookerName }}!</p>

    <p style="font-size:14px;color:#4b5563;line-height:1.7;margin:0 0 20px;">
      Thank you for requesting a viewing on <strong>Realtors Circle</strong>.
      Your request has been received and our in-house agent will coordinate with the realtor to confirm your appointment.
    </p>

    <div class="alert">
      <p><strong>Request Summary</strong></p>
    </div>

    <table class="details">
      <tr>
        <td>Property</td>
        <td><strong>{{ $property->title }}</strong></td>
      </tr>
      <tr>
        <td>Location</td>
        <td><strong>{{ $property->neigborhood }}, {{ $property->city }}, {{ $property->state }}</strong></td>
      </tr>
      <tr>
        <td>Preferred Date</td>
        <td><strong>{{ \Carbon\Carbon::parse($preferredDate)->format('l, F j, Y') }}</strong></td>
      </tr>
      <tr>
        <td>Preferred Time</td>
        <td><strong>{{ $preferredTime }}</strong></td>
      </tr>
      <tr>
        <td>Tour Type</td>
        <td><span class="badge">{{ $visitType === 'virtual' ? 'Virtual Tour' : 'In-Person Visit' }}</span></td>
      </tr>
      <tr>
        <td>Status</td>
        <td><span class="badge" style="background:#fef3c7;color:#92400e;">Pending Confirmation</span></td>
      </tr>
    </table>

    <div class="note">
      <p>
        <strong>Inspection Fee Reminder</strong><br>
        As acknowledged during booking, an inspection fee of <strong>₦10,000</strong> is required on the day of viewing.
        This fee is collected by our in-house agent on-site and is split between the realtors involved.
      </p>
    </div>

    <p style="font-size:14px;color:#4b5563;line-height:1.7;margin:0 0 20px;">
      Our in-house agent will contact you once the viewing is confirmed. You will receive another email with the final confirmation.
    </p>

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
