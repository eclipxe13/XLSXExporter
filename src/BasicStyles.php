<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter;

class BasicStyles
{
    public static function defaultStyle(): Style
    {
        return new Style([
            'font' => [
                'name' => 'Calibri',
                'size' => '11',
                'color' => '000000',
            ],
            'fill' => [
                'pattern' => Styles\Fill::NONE,
            ],
            'alignment' => [
                'vertical' => Styles\Alignment::VERTICAL_BOTTOM,
                'horizontal' => Styles\Alignment::HORIZONTAL_GENERAL,
            ],
        ]);
    }

    public static function defaultHeader(): Style
    {
        return new Style([
            'font' => [
                'name' => 'Calibri',
                'size' => '11',
                'bold' => true,
            ],
            'fill' => [
                'color' => 'B8B8E5',
                'pattern' => Styles\Fill::SOLID,
            ],
            'alignment' => [
                'horizontal' => Styles\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }
}
