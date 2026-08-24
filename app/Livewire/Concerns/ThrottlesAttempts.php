<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Rate limiting for Livewire component actions.
 *
 * Route-level `throttle:` middleware is useless for Livewire forms: every
 * action on every component POSTs to the same /livewire/update endpoint, so a
 * route limiter could only ever throttle "all Livewire activity" in aggregate.
 * Components that accept credentials or trigger mail therefore limit
 * themselves, using the same key shape and budgets as the named limiters in
 * AppServiceProvider.
 */
trait ThrottlesAttempts
{
    /**
     * Key on action + identifier + IP. Including the IP means an attacker
     * cannot lock a victim out of their own account just by hammering their
     * email address from elsewhere.
     */
    protected function throttleKey(string $action, string $identifier = ''): string
    {
        return 'lw|' . $action . '|'
            . Str::transliterate(Str::lower($identifier)) . '|'
            . request()->ip();
    }

    /**
     * Consume one attempt. Returns false (having already pushed a validation
     * error onto $errorField) when the caller is out of budget.
     *
     * $ipMaxAttempts adds a second, identifier-independent ceiling for the same
     * action. Without it, the per-identifier key alone is trivially sidestepped
     * for anything that sends mail: rotate to a new address and you get a fresh
     * budget every time. Actions that send mail should always pass it.
     */
    protected function passesRateLimit(
        string $action,
        string $identifier,
        int $maxAttempts,
        int $decaySeconds,
        string $errorField = 'email',
        ?int $ipMaxAttempts = null,
    ): bool {
        $keys = [$this->throttleKey($action, $identifier) => $maxAttempts];

        if ($ipMaxAttempts !== null) {
            $keys[$this->throttleKey($action)] = $ipMaxAttempts;
        }

        foreach ($keys as $key => $max) {
            if (RateLimiter::tooManyAttempts($key, $max)) {
                $seconds = RateLimiter::availableIn($key);

                Log::warning('Rate limit hit', [
                    'action' => $action,
                    'ip'     => request()->ip(),
                    'retry_in_seconds' => $seconds,
                ]);

                $this->addError($errorField, $this->throttleMessage($seconds));

                return false;
            }
        }

        foreach (array_keys($keys) as $key) {
            RateLimiter::hit($key, $decaySeconds);
        }

        return true;
    }

    /**
     * Release the per-identifier budget after a successful attempt, so a
     * legitimate user who fumbled their password a few times is not left
     * half-locked-out. The per-IP ceiling is deliberately *not* cleared: one
     * success should not reset an address-rotating caller's allowance.
     */
    protected function clearRateLimit(string $action, string $identifier = ''): void
    {
        RateLimiter::clear($this->throttleKey($action, $identifier));
    }

    protected function throttleMessage(int $seconds): string
    {
        if ($seconds >= 60) {
            $minutes = (int) ceil($seconds / 60);

            return "Too many attempts. Please try again in {$minutes} " . Str::plural('minute', $minutes) . '.';
        }

        return "Too many attempts. Please try again in {$seconds} " . Str::plural('second', $seconds) . '.';
    }
}
