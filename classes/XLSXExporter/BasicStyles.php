<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

    public static function withStdFormat($format)
    {
        return (new Style())->setFormat(Styles\Format::standarFormat($format));
    }

}
