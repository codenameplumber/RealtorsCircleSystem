<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>New Viewing Request</title>
<style>
  body{margin:0;padding:0;background:#f3f4f6;font-family:Inter,Arial,sans-serif;}
  .wrap{max-width:580px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.08);}
  .header{background:linear-gradient(135deg,#059669 0%,#047857 100%);padding:36px 40px;text-align:center;}
  .header img{width:32px;margin-bottom:12px;}
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
  .note{background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:18px 20px;margin-bottom:24px;}
  .note p{margin:0;font-size:13px;color:#92400e;line-height:1.6;}
  .footer{background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #f3f4f6;}
  .footer p{margin:0;font-size:12px;color:#9ca3af;}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>New Viewing Request</h1>
    <p>Someone is interested in your property on Realtors Circle</p>
  </div>

  <div class="body">
    <div class="alert">
      <p>
        <strong>A visitor has expressed interest in your property.</strong><br>
        To protect both parties, we do not share the visitor's personal details at this stage.
        Our in-house agent will reach out to you shortly to coordinate and confirm the viewing.
      </p>
    </div>

    <div class="prop-card">
      <h2>{{ $property->title }}</h2>
      <p>{{ $property->neigborhood }}, {{ $property->city }}, {{ $property->state }}</p>
    </div>

    <table class="details">
      <tr>
        <td>Requested Date</td>
        <td><strong>{{ \Carbon\Carbon::parse($preferredDate)->format('l, F j, Y') }}</strong></td>
      </tr>
      <tr>
        <td>Preferred Time</td>
        <td><strong>{{ $preferredTime }}</strong></td>
      </tr>
      <tr>
        <td>Visit Type</td>
        <td>
          <span class="badge">{{ $visitType === 'virtual' ? 'Virtual Tour' : 'In-Person Visit' }}</span>
        </td>
      </tr>
      <tr>
        <td>Property Price</td>
        <td><strong>₦{{ $property->price_display }}</strong></td>
      </tr>
    </table>

    <div class="note">
      <p>
        <strong>What happens next?</strong><br>
        One of our in-house agents will contact you to confirm availability and coordinate this viewing.
        Please ensure the property is accessible on the requested date. You do not need to contact the visitor directly.
      </p>
    </div>
  </div>

  <div class="footer">
    <p>© {{ date('Y') }} Realtors Circle &nbsp;·&nbsp; Nigeria's #1 P2P Real Estate Platform</p>
    <p style="margin-top:6px;">This email was sent because you have an active listing on Realtors Circle.</p>
  </div>
</div>
</body>
</html>
