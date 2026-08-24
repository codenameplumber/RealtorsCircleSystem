<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? str_replace('_', ' ', config('app.name'))  }}</title> {{-- Use $subject if passed, otherwise app name --}}
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ $header ?? $subject ?? str_replace('_', ' ', config('app.name'))  }}</h1> {{-- Allows header to be passed, or uses subject, or app name --}}
        </div>
        <div class="content">
            {{-- This is where the Markdown content from your Mailable will be rendered --}}
            {{-- @slot('body') comes from the Mail::message component --}}
            {{ $slot }}
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ str_replace('_', ' ', config('app.name'))  }}. All rights reserved.<br>
            <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a>
        </div>
    </div>
</body>
</html>