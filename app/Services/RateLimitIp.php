<?php

namespace App\Services;

/**
 * D-025：IP 単位のレート制限キー用に IP を正規化する。
 * IPv4 はそのまま、IPv4 射影 IPv6 は IPv4 として、IPv6 は先頭 /64 に丸める。
 */
final class RateLimitIp
{
    /** IPv6 を丸めるプレフィックス長（ビット）。 */
    public const IPV6_PREFIX_BITS = 64;

    public static function normalize(?string $ip): string
    {
        if ($ip === null || $ip === '') {
            return '';
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            return $ip;
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
            return $ip;
        }

        $bin = inet_pton($ip);
        if ($bin === false) {
            return $ip;
        }

        // ::ffff:a.b.c.d（IPv4 射影）。丸めると全 IPv4 利用者が 1 枠に潰れるため IPv4 として扱う
        if (str_starts_with($bin, str_repeat("\0", 10)."\xff\xff")) {
            return inet_ntop(substr($bin, 12));
        }

        $bytes = intdiv(self::IPV6_PREFIX_BITS, 8);

        return inet_ntop(substr($bin, 0, $bytes).str_repeat("\0", 16 - $bytes)).'/'.self::IPV6_PREFIX_BITS;
    }
}
