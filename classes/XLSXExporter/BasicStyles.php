<?php

namespace XLSXExporter;

class BasicStyles {

    public static function defaultStyle($fontname = "Calibri", $fontsize = 11)
    {
        return new Style([
            "font" => [
                "name" => $fontname,
                "size" => $fontsize,
                "color" => "000000",
            ],
            "fill" => [
                "pattern" => Styles\Fill::NONE
            ],
            "alignment" => [
                "vertical" => Styles\Alignment::VERTICAL_BOTTOM,
                "horizontal" => Styles\Alignment::HORIZONTAL_GENERAL,
            ],
        ]);
    }

    public static function defaultHeader($fontname = "Calibri", $fontsize = 11)
    {
        return new Style([
            "font" => [
                "name" => $fontname,
                "size" => $fontsize,
                "bold" => true,
            ],
            "fill" => [
                "color" => "B8B8E5",
                "pattern" => Styles\Fill::SOLID
            ],
            "alignment" => [
                "horizontal" => Styles\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }

}
