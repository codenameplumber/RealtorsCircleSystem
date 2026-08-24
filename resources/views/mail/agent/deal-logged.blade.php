<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Deal Logged — Realtors Circle</title>
<style>body{font-family:'Inter',Arial,sans-serif;background:#f3f4f6;margin:0;padding:32px 16px}.card{max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)}.header{background:linear-gradient(135deg,#059669,#047857);padding:32px;text-align:center}.header h1{color:#fff;font-size:20px;font-weight:700;margin:12px 0 4px}.header p{color:#a7f3d0;font-size:13px;margin:0}.body{padding:32px}.greeting{font-size:15px;font-weight:600;color:#111827;margin:0 0 12px}.text{font-size:14px;color:#6b7280;line-height:1.6;margin:0 0 16px}.deal-box{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:16px;margin:0 0 24px}.deal-title{font-size:14px;font-weight:700;color:#064e3b;margin:0 0 4px}.deal-sub{font-size:12px;color:#6b7280;margin:0}.details{border-collapse:collapse;width:100%;margin:0 0 24px}.details td{padding:9px 0;font-size:13px;border-bottom:1px solid #f3f4f6;color:#374151}.details td:first-child{color:#6b7280;width:46%}.btn{display:inline-block;padding:12px 24px;background:#059669;color:#fff;text-decoration:none;border-radius:10px;font-size:13px;font-weight:600}.footer{padding:0 32px 32px;text-align:center;font-size:11px;color:#9ca3af}</style>
</head>
<body>
<div class="card">
    <div class="header">
        <h1>Deal Logged!</h1>
        <p>Realtors Circle</p>
    </div>
    <div class="body">
        <p class="greeting">Hi {{ $recipientName }},</p>
        <p class="text">
            A closed deal has been recorded on Realtors Circle and your
            {{ $role === 'referring' ? 'referral commission' : 'commission share' }}
            has been calculated and logged for payout processing.
        </p>
        <div class="deal-box">
            <p class="deal-title">{{ $commission->property?->title }}</p>
            <p class="deal-sub">{{ $commission->property?->neigborhood }}, {{ $commission->property?->city }} · Ref: {{ $commission->deal_reference }}</p>
        </div>
        <table class="details">
            <tr>
                <td>Your Role</td>
                <td><strong>{{ $role === 'referring' ? 'Referring Realtor' : 'Listing Realtor' }}</strong></td>
            </tr>
            <tr>
                <td>Split Model</td>
                <td><strong>{{ $commission->split_model_label }}</strong></td>
            </tr>
            <tr>
                <td>Total Deal Commission</td>
                <td><strong>₦{{ number_format($commission->total_amount, 2) }}</strong></td>
            </tr>
            <tr>
                <td>Your Share</td>
                <td><strong>₦{{ number_format($shareAmount, 2) }}</strong></td>
            </tr>
            <tr>
                <td>Deal Date</td>
                <td><strong>{{ $commission->deal_date?->format('l, F j, Y') }}</strong></td>
            </tr>
        </table>
        <p class="text">You can track the status of this payout from your Commission and Payments pages on your dashboard.</p>
        <a href="{{ url('/realtor/commission') }}" class="btn">View My Commission</a>
    </div>
    <div class="footer">© {{ date('Y') }} Realtors Circle. All rights reserved.</div>
</div>
</body>
</html>
