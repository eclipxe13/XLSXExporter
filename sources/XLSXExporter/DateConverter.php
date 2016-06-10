<?php

namespace XLSXExporter;

class DateConverter
{
    const TIME = 'TIME';
    const DATE = 'DATE';
    const DATETIME = 'DATETIME';

    public static function tsToExcel($ts, $type)
    {
        if ($type === self::TIME) {
            return static::tsToExcelTime($ts);
        }
        if ($type === self::DATE) {
            return static::tsToExcelDate($ts);
        }
        return static::tsToExcelDateTime($ts);
    }

    public static function tsToExcelTime($ts)
    {
        $utc = static::utcParts($ts);
        return (($utc[3] * 3600) + ($utc[4] * 60) + $utc[5]) / 86400;
    }

    public static function tsToExcelDate($ts)
    {
        $utc = static::utcParts($ts);
        // Bug 1900 is not a leap year
        // http://support.microsoft.com/kb/214326
        $delta = ($utc[0] == 1900 and $utc[1] <= 2) ? -1 : 0;
        return (int) (25569 + $delta + (($ts + $utc[6]) / 86400));
    }

    public static function tsToExcelDateTime($ts)
    {
        return (float) static::tsToExcelDate($ts) + static::tsToExcelTime($ts);
    }

    public static function utcParts($ts)
    {
        $offset = date('Z', $ts);
        return array_merge(
            explode(',', gmdate('Y,m,d,H,i,s', $ts + $offset)),
            [$offset]
        );
    }
}
