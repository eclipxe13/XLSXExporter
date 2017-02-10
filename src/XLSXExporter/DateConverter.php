<?php
namespace XLSXExporter;

class DateConverter
{
    const TIME = 'TIME';
    const DATE = 'DATE';
    const DATETIME = 'DATETIME';

    const PRECISION_TIME = 6;

    /**
     * @param int $timestamp
     * @param string $type
     * @return string
     */
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

    /**
     * @param int $timestamp
     * @return string
     */
    public static function tsToExcelTime($timestamp)
    {
        $utc = static::utcParts($timestamp);
        return number_format((($utc[3] * 3600) + ($utc[4] * 60) + $utc[5]) / 86400, static::PRECISION_TIME, '.', '');
    }

    /**
     * @param int $timestamp
     * @return string
     */
    public static function tsToExcelDate($timestamp)
    {
        $utc = static::utcParts($timestamp);
        // Bug 1900 is not a leap year
        // http://support.microsoft.com/kb/214326
        $delta = ($utc[0] == 1900 && $utc[1] <= 2) ? -1 : 0;
        return (string) ((int) (25569 + $delta + (($timestamp + $utc[6]) / 86400)));
    }

    /**
     * @param int $timestamp
     * @return string
     */
    public static function tsToExcelDateTime($timestamp)
    {
        return number_format(
            (int) static::tsToExcelDate($timestamp) + (float) static::tsToExcelTime($timestamp),
            static::PRECISION_TIME,
            '.',
            ''
        );
    }

    /**
     * @param int $timestamp
     * @return array
     */
    public static function utcParts($timestamp)
    {
        $offset = date('Z', $timestamp);
        return array_merge(
            explode(',', gmdate('Y,m,d,H,i,s', $timestamp + $offset)),
            [$offset]
        );
    }
}
