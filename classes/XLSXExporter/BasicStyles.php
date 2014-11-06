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
        $s = new Style();
        $s->setFormat(Styles\Format::standarFormat(0));
        $s->getFont()->setValues([
            "name" => $fontname,
            "size" => $fontsize
        ]);
        $s->getAlignment()->setValues([
            "vertical" => Styles\Alignment::VERTICAL_BOTTOM,
            "horizontal" => Styles\Alignment::HORIZONTAL_GENERAL,
        ]);
        $s->getFill()->setValues([
            "pattern" => Styles\Fill::NONE,
        ]);
        return $s;
    }

    public static function withStdFormat($format)
    {
        return (new Style())->setFormat(Styles\Format::standarFormat($format));
    }

    public static function defaultHeader()
    {
        $s = new Style();
        $s->getFont()->setValues([
            "bold" => true
        ]);
        $s->getFill()->setValues([
            "color" => "ccccff",
            "pattern" => Styles\Fill::SOLID
        ]);
        $s->getAlignment()->setValues([
            "horizontal" => Styles\Alignment::HORIZONTAL_CENTER,
        ]);
        return $s;
    }

}
