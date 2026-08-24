<?php

namespace App\Helpers;

class PhoneNormalizer
{
    /**
     * Reduce any phone format (WhatsApp international "2348012345678",
     * "+2348012345678", or local "08012345678") down to the last 10 digits —
     * the part that's actually unique to a Nigerian subscriber — so numbers
     * from different sources can be compared reliably.
     */
    public static function last10(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        return substr($digits, -10);
    }

    /**
     * Convert any incoming format to the app's canonical local storage format
     * ("0" + 10 digits), matching how existing Phone columns are stored.
     */
    public static function toLocal(string $phone): string
    {
        return '0' . self::last10($phone);
    }

    public static function matches(string $a, string $b): bool
    {
        return self::last10($a) === self::last10($b);
    }
}
