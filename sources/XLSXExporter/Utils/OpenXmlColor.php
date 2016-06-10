<?php

namespace XLSXExporter\Utils;

class OpenXmlColor
{
    /**
     * Try to convert a string color to open document color.
     *
     * @param string $value Hex color representation
     * @return false|string 8 hex digits representing a color 'AARRGGBB', false on error
     */
    public static function cast($value)
    {
        $color = strtoupper(ltrim($value, "#"));
        if (strlen($color) == 6) {
            $color = "FF" . $color;
        }
        if (!preg_match("/[0-9A-F]{8}/", $color)) {
            return false;
        }
        return $color;
    }
}
