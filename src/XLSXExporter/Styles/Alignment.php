<?php
namespace XLSXExporter\Styles;

use XLSXExporter\XLSXException;

/**
 * @property string $horizontal Horizontal alignment
 * @property string $vertical Vertical alignment
 * @property bool $wraptext Wrap text
 *
 * @package XLSXExporter\Styles
 */
class Alignment extends AbstractStyle
{
    const HORIZONTAL_GENERAL = 'general';
    const HORIZONTAL_LEFT = 'left';
    const HORIZONTAL_CENTER = 'center';
    const HORIZONTAL_RIGHT = 'right';
    const HORIZONTAL_JUSTIFY = 'justify';

    const VERTICAL_TOP = 'top';
    const VERTICAL_BOTTOM = 'bottom';
    const VERTICAL_CENTER = 'center';

    protected function properties()
    {
        return [
            'horizontal',
            'vertical',
            'wraptext',
        ];
    }

    public function asXML()
    {
        return '<alignment'
            . (($this->horizontal) ? ' horizontal="' . $this->horizontal . '"' : '')
            . (($this->vertical) ? ' vertical="' . $this->vertical . '"' : '')
            . (($this->wraptext) ? ' wrapText="' . (($this->wraptext) ? '1' : '0') . '"' : '')
            . '/>'
        ;
    }

    protected function castHorizontal($value)
    {
        $aligns = [
            static::HORIZONTAL_GENERAL,
            static::HORIZONTAL_LEFT,
            static::HORIZONTAL_CENTER,
            static::HORIZONTAL_RIGHT,
            static::HORIZONTAL_JUSTIFY,
        ];
        if (! in_array($value, $aligns)) {
            throw new XLSXException('Invalid alignment horizontal');
        }
        return $value;
    }

    protected function castVertical($value)
    {
        if (! in_array($value, [static::VERTICAL_TOP, static::VERTICAL_CENTER, static::VERTICAL_BOTTOM])) {
            throw new XLSXException('Invalid alignment vertical');
        }
        return $value;
    }

    protected function castWraptext($value)
    {
        return (bool) $value;
    }
}
