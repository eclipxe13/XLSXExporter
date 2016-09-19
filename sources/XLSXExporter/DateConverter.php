<?php
namespace XLSXExporter;

class DateConverter
{
    const TIME = 'TIME';
    const DATE = 'DATE';
    const DATETIME = 'DATETIME';

    public static function tsToExcel($timestamp, $type)
    {
        if ($type === self::TIME) {
            return static::tsToExcelTime($timestamp);
        }
        if ($type === self::DATE) {
            return static::tsToExcelDate($timestamp);
        }
        return static::tsToExcelDateTime($timestamp);
    }

    public static function tsToExcelTime($timestamp)
    {
        $utc = static::utcParts($timestamp);
        return (($utc[3] * 3600) + ($utc[4] * 60) + $utc[5]) / 86400;
    }

    public static function tsToExcelDate($timestamp)
    {
        $utc = static::utcParts($timestamp);
        // Bug 1900 is not a leap year
        // http://support.microsoft.com/kb/214326
        $delta = ($utc[0] == 1900 && $utc[1] <= 2) ? -1 : 0;
        return (int) (25569 + $delta + (($timestamp + $utc[6]) / 86400));
    }

    public static function tsToExcelDateTime($timestamp)
    {
        return (float) static::tsToExcelDate($timestamp) + static::tsToExcelTime($timestamp);
    }

    public static function utcParts($timestamp)
    {
        $offset = date('Z', $timestamp);
        return array_merge(
            explode(',', gmdate('Y,m,d,H,i,s', $timestamp + $offset)),
            [$offset]
        );
    }
}
