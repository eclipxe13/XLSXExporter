<?php
namespace XLSXExporter\Utils;

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
     * @param string|int $color Hex color representation or positive integer
     * @return string|false 8 hex digits representing a color 'AARRGGBB', false on error
     */
    public static function cast($color)
    {
        if (is_int($color)) {
            if ($color < 0 || $color > 4294967295) {
                return false;
            }
            return self::cast(str_pad(dechex($color), ($color > 16777215) ? 8 : 6, '0', STR_PAD_LEFT));
        }
        if (! is_string($color)) {
            return false;
        }
        $color = strtoupper(ltrim($color, '#'));
        if (! ctype_xdigit($color)) {
            return false;
        }
        if (strlen($color) < 3 || strlen($color) == 5 || strlen($color) > 8) {
            return false;
        }
        if (strlen($color) == 3) {
            $color = 'F' . $color;
        }
        if (strlen($color) == 4) {
            $color = $color[0] . $color[0]
                . $color[1] . $color[1]
                . $color[2] . $color[2]
                . $color[3] . $color[3]
            ;
        }
        $color = str_pad($color, 8, 'F', STR_PAD_LEFT);
        return $color;
    }
}
