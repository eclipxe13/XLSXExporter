<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Utils;

class OpenXmlColor
{
    /**
     * Try to convert a string or integer color to open document color.
     *
     * Allowed formats:
     * [#]RGB => FFRRGGBB
     * [#]ARGB => AARRGGBB
     * [#]RRGGBB => FFRRGGBB
     * [#]AARRGGBB => AARRGGBB
     * 0 - 16777215 => FFRRGGBB
     * 16777216 - 4294967295 => AARRGGBB
     *
     * @param scalar|null $color Hex color representation or positive integer
     * @return string|false 8 hex digits representing a color 'AARRGGBB', false on error
     */
    public static function cast($color)
    {
        if (is_int($color)) {
            if ($color < 0 || $color > 4_294_967_295) {
                return false;
            }
            return self::cast(str_pad(dechex($color), ($color > 16_777_215) ? 8 : 6, '0', STR_PAD_LEFT));
        }
        if (! is_string($color)) {
            return false;
        }
        $color = strtoupper(ltrim($color, '#'));
        if (! preg_match('/^[A-Fa-f\d]+$/', $color)) {
            return false;
        }
        if (strlen($color) < 3 || 5 == strlen($color) || strlen($color) > 8) {
            return false;
        }
        if (3 == strlen($color)) {
            $color = 'F' . $color;
        }
        if (4 == strlen($color)) {
            $color = $color[0] . $color[0]
                . $color[1] . $color[1]
                . $color[2] . $color[2]
                . $color[3] . $color[3]
            ;
        }
        return str_pad($color, 8, 'F', STR_PAD_LEFT);
    }
}
