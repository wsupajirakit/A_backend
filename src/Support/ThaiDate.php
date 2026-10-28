<?php

declare(strict_types=1);

namespace App\Support;

/** Date formatting used by the site and the app ("20 ตุลาคม 2026"). */
final class ThaiDate
{
    private const MONTHS = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม',
    ];

    public static function readable(string $ymd): string
    {
        [$y, $m, $d] = explode('-', $ymd);
        return $d . ' ' . self::MONTHS[(int) $m - 1] . ' ' . $y;
    }

    /** Y-m-d -> d/m/Y */
    public static function dmy(string $ymd): string
    {
        [$y, $m, $d] = explode('-', $ymd);
        return $d . '/' . $m . '/' . $y;
    }

    /** Strict YYYY-MM-DD that is a real calendar date. */
    public static function isValidYmd(string $value): bool
    {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m)) {
            return false;
        }
        return checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
    }
}
