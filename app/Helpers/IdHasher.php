<?php

namespace App\Helpers;

class IdHasher
{
    private static function secret(): int
    {
        // 32-bit unsigned secret derived from APP_KEY — changes per app
        return abs(crc32(config('app.key')));
    }

    public static function encode(int $id): string
    {
        $obfuscated = ($id ^ self::secret()) & 0xFFFFFFFF;
        return rtrim(strtr(base64_encode(pack('V', $obfuscated)), '+/', '-_'), '=');
    }

    public static function decode(string $hash): ?int
    {
        try {
            $padLen  = strlen($hash) % 4;
            $padded  = $padLen ? $hash . str_repeat('=', 4 - $padLen) : $hash;
            $bytes   = unpack('V', base64_decode(strtr($padded, '-_', '+/')));

            if (! $bytes) {
                return null;
            }

            $id = ($bytes[1] ^ self::secret()) & 0x7FFFFFFF;

            return $id > 0 ? $id : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
