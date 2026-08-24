<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('email_title', 'Realtors Circle')</title>
<style>
  body{margin:0;padding:0;background:#f3f4f6;font-family:Inter,Arial,sans-serif;}
  .wrap{max-width:580px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.08);}

  /* ── Header ─────────────────────────────────── */
  .header{background:linear-gradient(135deg,#059669 0%,#047857 100%);padding:36px 40px;text-align:center;}
  .logo{display:inline-flex;align-items:center;gap:10px;margin-bottom:16px;}
  .logo-icon{width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;}
  .logo-text{color:#fff;font-size:18px;font-weight:700;}
  .header h1{color:#fff;font-size:22px;font-weight:800;margin:0 0 6px;}
  .header p{color:#a7f3d0;font-size:14px;margin:0;}

  /* ── Body ────────────────────────────────────── */
  .body{padding:36px 40px;}
  .greeting{font-size:16px;font-weight:600;color:#111827;margin-bottom:14px;}
  .text{color:#4b5563;font-size:14px;line-height:1.7;margin-bottom:14px;}

  /* ── Alert / Note boxes ──────────────────────── */
  .alert{background:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;padding:18px 20px;margin-bottom:24px;}
  .alert p{margin:0;color:#065f46;font-size:14px;line-height:1.6;}
  .note{background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:18px 20px;margin-bottom:24px;}
  .note p{margin:0;font-size:13px;color:#92400e;line-height:1.6;}

  /* ── Property / Feature card ─────────────────── */
  .prop-card{background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:20px;margin-bottom:24px;}
  .prop-card h2{margin:0 0 4px;font-size:16px;color:#111827;font-weight:700;}
  .prop-card p{margin:0;font-size:13px;color:#6b7280;}

  /* ── Key–value table ─────────────────────────── */
  .details{border-collapse:collapse;width:100%;margin-bottom:24px;}
  .details td{padding:10px 0;font-size:14px;border-bottom:1px solid #f3f4f6;color:#374151;}
  .details td:first-child{color:#6b7280;width:42%;}

  /* ── Badges ──────────────────────────────────── */
  .badge{display:inline-block;border-radius:999px;padding:3px 12px;font-size:11px;font-weight:700;}
  .badge-green{background:#d1fae5;color:#065f46;}
  .badge-amber{background:#fef3c7;color:#92400e;}
  .badge-gray{background:#f3f4f6;color:#374151;}

  /* ── Steps ───────────────────────────────────── */
  .steps{margin:24px 0;}
  .step{display:flex;align-items:flex-start;gap:14px;margin-bottom:18px;}
  .step-num{width:30px;height:30px;background:#059669;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;margin-top:2px;}
  .step-title{font-weight:600;font-size:13px;color:#111827;margin-bottom:2px;}
  .step-desc{font-size:12px;color:#6b7280;line-height:1.5;}

  /* ── CTA button ──────────────────────────────── */
  .cta{display:block;width:fit-content;margin:28px auto;padding:13px 36px;background:#059669;color:#fff !important;text-decoration:none;border-radius:12px;font-size:14px;font-weight:700;text-align:center;}

  /* ── URL fallback box ────────────────────────── */
  .url-box{background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:14px 16px;margin:16px 0;word-break:break-all;font-size:12px;color:#6b7280;}

  /* ── Footer ──────────────────────────────────── */
  .footer{background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #f3f4f6;}
  .footer p{margin:0;font-size:12px;color:#9ca3af;line-height:1.8;}
  .footer a{color:#059669;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">

  {{-- Header --}}
  <div class="header">
    <div class="logo">
      <div class="logo-icon">
        <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
      </div>
      <span class="logo-text">Realtors Circle</span>
    </div>
    <h1>@yield('header_title')</h1>
    <p>@yield('header_sub', "Nigeria's #1 P2P Real Estate Platform")</p>
  </div>

  {{-- Body --}}
  <div class="body">
    @yield('content')
  </div>

  {{-- Footer --}}
  <div class="footer">
    <p>© {{ date('Y') }} Realtors Circle &nbsp;·&nbsp; Nigeria's #1 P2P Real Estate Platform</p>
    <p><a href="{{ url('/privacy') }}">Privacy Policy</a> &nbsp;·&nbsp; <a href="{{ url('/terms') }}">Terms of Service</a></p>
  </div>

</div>
</body>
</html>
