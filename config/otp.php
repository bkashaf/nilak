<?php

return [
    // Keep disabled until SMS provider integration is finished.
    'enabled' => env('OTP_ENABLED', false),

    // One-time code format.
    'length' => (int) env('OTP_LENGTH', 6),
    'ttl_seconds' => (int) env('OTP_TTL_SECONDS', 120),
    'resend_seconds' => (int) env('OTP_RESEND_SECONDS', 60),
    'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),

    // During development you can log generated codes instead of sending SMS.
    'debug_log' => env('OTP_DEBUG_LOG', true),

    // Reserved for future integration: kavenegar/melipayamak/custom.
    'sms_provider' => env('SMS_PROVIDER', 'none'),
];
