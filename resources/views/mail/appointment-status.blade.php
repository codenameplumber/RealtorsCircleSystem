<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Appointment Update — Realtors Circle</title>
<style>
  body{margin:0;padding:0;background:#f3f4f6;font-family:Inter,Arial,sans-serif;}
  .wrap{max-width:580px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.08);}
  .header{padding:36px 40px;text-align:center;}
  .header.confirmed{background:linear-gradient(135deg,#059669 0%,#047857 100%);}
  .header.rescheduled{background:linear-gradient(135deg,#d97706 0%,#b45309 100%);}
  .header.completed{background:linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);}
  .header.cancelled{background:linear-gradient(135deg,#dc2626 0%,#b91c1c 100%);}
  .header h1{color:#fff;font-size:22px;font-weight:800;margin:0 0 6px;}
  .header p{font-size:14px;margin:0;}
  .header.confirmed p{color:#a7f3d0;}
  .header.rescheduled p{color:#fde68a;}
  .header.completed p{color:#bfdbfe;}
  .header.cancelled p{color:#fecaca;}
  .body{padding:36px 40px;}
  .alert{border-radius:12px;padding:18px 20px;margin-bottom:24px;}
  .alert-green{background:#ecfdf5;border:1px solid #a7f3d0;}
  .alert-green p{margin:0;color:#065f46;font-size:14px;line-height:1.6;}
  .alert-amber{background:#fffbeb;border:1px solid #fde68a;}
  .alert-amber p{margin:0;color:#92400e;font-size:14px;line-height:1.6;}
  .alert-blue{background:#eff6ff;border:1px solid #bfdbfe;}
  .alert-blue p{margin:0;color:#1e40af;font-size:14px;line-height:1.6;}
  .alert-red{background:#fef2f2;border:1px solid #fecaca;}
  .alert-red p{margin:0;color:#991b1b;font-size:14px;line-height:1.6;}
  .details{border-collapse:collapse;width:100%;margin-bottom:24px;}
  .details td{padding:10px 0;font-size:14px;border-bottom:1px solid #f3f4f6;color:#374151;}
  .details td:first-child{color:#6b7280;width:40%;}
  .badge{display:inline-block;border-radius:999px;padding:3px 12px;font-size:12px;font-weight:600;}
  .footer{background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #f3f4f6;}
  .footer p{margin:0;font-size:12px;color:#9ca3af;}
  .footer a{color:#059669;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">
  <div class="header {{ $statusType }}">
    @if($statusType === 'confirmed')
      <h1>Inspection Confirmed!</h1>
      <p>Your property inspection has been confirmed</p>
    @elseif($statusType === 'rescheduled')
      <h1>Inspection Rescheduled</h1>
      <p>Your appointment has been moved to a new date</p>
    @elseif($statusType === 'completed')
      <h1>Inspection Completed!</h1>
      <p>Your property inspection has been marked as completed</p>
    @else
      <h1>Inspection Cancelled</h1>
      <p>Your property inspection has been cancelled</p>
    @endif
  </div>

  <div class="body">
    <p style="font-size:16px;font-weight:600;color:#111827;margin:0 0 14px;">Hello, {{ $recipientName ?? $inquiry->name }}!</p>

    @if($statusType === 'confirmed')
      <div class="alert alert-green">
        <p>
          <strong>Your inspection has been confirmed.</strong><br>
          Please ensure you are available at the property on the scheduled date. Our in-house agent will be present on-site.
        </p>
      </div>
    @elseif($statusType === 'rescheduled')
      <div class="alert alert-amber">
        <p>
          <strong>Your inspection appointment has been rescheduled.</strong><br>
          The realtor has proposed a new date and time. Please review the updated details below.
        </p>
      </div>
    @elseif($statusType === 'completed')
      <div class="alert alert-blue">
        <p>
          <strong>This inspection has been marked as completed.</strong><br>
          Thank you for taking part in this property inspection. If a deal is reached, our team will follow up with the next steps.
        </p>
      </div>
    @else
      <div class="alert alert-red">
        <p>
          <strong>Your inspection appointment has been cancelled.</strong><br>
          @if($reason) Reason: {{ $reason }} @endif
        </p>
      </div>
    @endif

    <table class="details">
      <tr>
        <td>Property</td>
        <td><strong>{{ $inquiry->property?->title }}</strong></td>
      </tr>
      <tr>
        <td>Location</td>
        <td><strong>{{ $inquiry->property?->neigborhood }}, {{ $inquiry->property?->city }}</strong></td>
      </tr>
      @if($statusType === 'rescheduled' && $inquiry->reschedule_date)
        <tr>
          <td>New Date</td>
          <td><strong>{{ $inquiry->reschedule_date->format('l, F j, Y') }}</strong></td>
        </tr>
        <tr>
          <td>New Time</td>
          <td><strong>{{ $inquiry->reschedule_time }}</strong></td>
        </tr>
      @else
        <tr>
          <td>Original Date</td>
          <td><strong>{{ $inquiry->preferred_date?->format('l, F j, Y') }}</strong></td>
        </tr>
        <tr>
          <td>Original Time</td>
          <td><strong>{{ $inquiry->preferred_time }}</strong></td>
        </tr>
      @endif
      <tr>
        <td>Tour Type</td>
        <td><strong>{{ $inquiry->visit_type === 'virtual' ? 'Virtual Tour' : 'In-Person Visit' }}</strong></td>
      </tr>
    </table>

    @if($statusType === 'cancelled')
      <p style="font-size:14px;color:#4b5563;line-height:1.7;margin:0 0 20px;">
        We apologise for the inconvenience. You are welcome to browse other properties on Realtors Circle and submit a new inspection request.
      </p>
    @elseif($statusType === 'rescheduled')
      <p style="font-size:14px;color:#4b5563;line-height:1.7;margin:0 0 20px;">
        If the new date and time does not work for you, please contact us and we will arrange another suitable time.
      </p>
    @elseif($statusType === 'completed')
      <p style="font-size:14px;color:#4b5563;line-height:1.7;margin:0 0 20px;">
        You can review the full appointment history and any next steps from your Realtors Circle dashboard at any time.
      </p>
    @endif

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
