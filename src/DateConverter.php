<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter;

class DateConverter
{
    public const TIME = 'TIME';

    public const DATE = 'DATE';

    public const DATETIME = 'DATETIME';

    public const PRECISION_TIME = 6;

    public const PRECISION_POW = 10 ** self::PRECISION_TIME;

    public static function tsToExcel(int $timestamp, string $type): string
    {
        if (self::TIME === $type) {
            return static::tsToExcelTime($timestamp);
        }
        if (self::DATE === $type) {
            return static::tsToExcelDate($timestamp);
        }
        return static::tsToExcelDateTime($timestamp);
    }

    public static function tsToExcelTime(int $timestamp): string
    {
        $utc = static::utcParts($timestamp);
        // return number_format((($utc[3] * 3600) + ($utc[4] * 60) + $utc[5]) / 86400, static::PRECISION_TIME, '.', '');
        return number_format(self::roundTime($utc[3], $utc[4], $utc[5]), static::PRECISION_TIME, '.', '');
    }

    public static function roundTime(int $hours, int $minutes, int $seconds): float
    {
        $relative = ($hours * 3600 + $minutes * 60 + $seconds) / 86400;
        return ceil(round(self::PRECISION_POW * $relative, 2)) / self::PRECISION_POW;
    }

    public static function tsToExcelDate(int $timestamp): string
    {
        $utc = static::utcParts($timestamp);
        // Bug 1900 is not a leap year
        // http://support.microsoft.com/kb/214326
        $delta = (1900 == $utc[0] && $utc[1] <= 2) ? -1 : 0;
        return (string) ((int) (25569 + $delta + (($timestamp + $utc[6]) / 86400)));
    }

    public static function tsToExcelDateTime(int $timestamp): string
    {
        return number_format(
            (int) static::tsToExcelDate($timestamp) + (float) static::tsToExcelTime($timestamp),
            static::PRECISION_TIME,
            '.',
            ''
        );
    }

    /**
     * @return array{int, int, int, int, int, int, int}
     */
    public static function utcParts(int $timestamp): array
    {
        $offset = idate('Z', $timestamp);
        /** @var array{string, string, string, string, string, string, string} $values */
        $values = explode(',', gmdate('Y,m,d,H,i,s', $timestamp + $offset) . ',' . $offset);
        return array_map('intval', $values);
    }
}
