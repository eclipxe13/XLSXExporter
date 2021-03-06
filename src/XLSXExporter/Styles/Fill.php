<?php
namespace XLSXExporter\Styles;

use XLSXExporter\Utils\OpenXmlColor;
use XLSXExporter\XLSXException;

/**
 * @property string $pattern Fill pattern
 * @property string $color Fill color
 *
 * @package XLSXExporter\Styles
 */
class Fill extends AbstractStyle
{
    const SOLID = 'solid';
    const NONE = 'none';
    const GRAY125 = 'gray125';

    protected function properties()
    {
        return [
            'color',
            'pattern',
        ];
    }

    public function asXML()
    {
        if (! $this->pattern || $this->pattern == static::NONE || $this->pattern === static::GRAY125) {
            return '<fill><patternFill patternType="' . $this->pattern . '"/></fill>';
        }
        return '<fill>'
            . '<patternFill patternType="' . $this->pattern . '">'
            . '<fgColor rgb="' . $this->color . '"/>'
            . '<bgColor rgb="' . $this->color . '"/>'
            . '</patternFill>'
            . '</fill>'
        ;
    }

    protected function castPattern($value)
    {
        if (! in_array($value, [static::NONE, static::GRAY125, static::SOLID])) {
            throw new XLSXException('Invalid fill pattern');
        }
        return $value;
    }

    protected function castColor($value)
    {
        if (false === $color = OpenXmlColor::cast($value)) {
            throw new XLSXException('Invalid fill color');
        }
        return $color;
    }
}
