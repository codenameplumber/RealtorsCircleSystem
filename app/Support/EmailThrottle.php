<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Per-recipient cap on self-service transactional mail.
 *
 * IP throttling alone does not protect the *recipient*: a distributed caller,
 * or one just rotating through a proxy, can still bury a single inbox under
 * password-reset and verification mail. This caps how much mail any one
 * address can be made to receive, regardless of who asked for it.
 *
 * Only applied to flows an anonymous visitor can trigger by naming an address
 * (password reset, resend-verification, registration). Staff-initiated mail —
 * approvals, rejections, deal notifications — is deliberately not throttled;
 * an admin clicking approve must always result in an email.
 */
class EmailThrottle
{
    /** Mail of a single type, to a single address, per window. */
    public const MAX_PER_TYPE = 3;

    /** Mail of any self-service type, to a single address, per window. */
    public const MAX_PER_ADDRESS = 6;

    public const WINDOW_SECONDS = 3600;

    /**
     * Consume one send against $email's budget. Returns false when the address
     * has already received its allowance — callers should silently skip the
     * send and still report success to the user, so the throttle does not
     * become an account-existence oracle.
     */
    public static function allows(string $email, string $type): bool
    {
        $address = Str::lower(trim($email));

        if ($address === '') {
            return false;
        }

        $typeKey    = 'mail|' . $type . '|' . $address;
        $addressKey = 'mail|any|' . $address;

        if (RateLimiter::tooManyAttempts($typeKey, self::MAX_PER_TYPE)
            || RateLimiter::tooManyAttempts($addressKey, self::MAX_PER_ADDRESS)) {

            Log::warning('Transactional mail suppressed by recipient throttle', [
                'type' => $type,
                'ip'   => request()->ip(),
            ]);

            return false;
        }

        RateLimiter::hit($typeKey, self::WINDOW_SECONDS);
        RateLimiter::hit($addressKey, self::WINDOW_SECONDS);

        return true;
    }

    /**
     * Drop an address's budget — used after a reset actually completes, so the
     * user is not locked out of a legitimate second attempt.
     */
    public static function clear(string $email, string $type): void
    {
        $address = Str::lower(trim($email));

        RateLimiter::clear('mail|' . $type . '|' . $address);
        RateLimiter::clear('mail|any|' . $address);
    }
}
