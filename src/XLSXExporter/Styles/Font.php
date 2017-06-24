<?php
namespace XLSXExporter\Styles;

use XLSXExporter\Utils\OpenXmlColor;
use XLSXExporter\XLSXException;

/**
 * @property string $name Font name
 * @property int $size Font size (pt)
 * @property bool $bold Font is bold
 * @property bool $italic Font is italic
 * @property bool $strike Font is strike
 * @property string $underline Underline value (use constants)
 * @property bool $wordwrap Font is strike
 * @property string $color Font color
 *
 * @package XLSXExporter\Styles
 */
class Font extends AbstractStyle
{
    const UNDERLINE_NONE = 'none';
    const UNDERLINE_DOUBLE = 'double';
    const UNDERLINE_SINGLE = 'single';

    protected function properties()
    {
        return [
            'name',
            'size',
            'bold',
            'italic',
            'strike',
            'underline',
            'wordwrap',
            'color',
        ];
    }

    public function asXML()
    {
        // According to http://msdn.microsoft.com/en-us/library/ff531499%28v=office.12%29.aspx
        // Excel requires the child elements to be in the following sequence:
        // b, i, strike, condense, extend, outline, shadow, u, vertAlign, sz, color, name, family, charset, scheme
        return '<font>'
            . '<b val="' . (($this->bold) ? '1' : '0') . '" />'
            . '<i val="' . (($this->italic) ? '1' : '0') . '" />'
            . '<strike val="' . (($this->strike) ? '1' : '0') . '" />'
            . '<u val="' . (($this->underline) ? : static::UNDERLINE_NONE) . '" />'
            . (($this->size) ? '<sz val="' . $this->size . '"/>' : '')
            . (($this->color) ? '<color rgb="' . $this->color . '"/>' : '')
            . (($this->name) ? '<name val="' . $this->name . '"/>' : '')
            . '</font>'
        ;
    }

    protected function castName($value)
    {
        return (string) $value;
    }

    protected function castSize($value)
    {
        return max(6, (int) $value);
    }

    protected function castBold($value)
    {
        return (bool) $value;
    }

    protected function castItalic($value)
    {
        return (bool) $value;
    }

    protected function castStrike($value)
    {
        return (bool) $value;
    }

    protected function castWordwrap($value)
    {
        return (bool) $value;
    }

    protected function castColor($value)
    {
        if (false === $color = OpenXmlColor::cast($value)) {
            throw new XLSXException('Invalid font color');
        }
        return $color;
    }

    protected function castUnderline($value)
    {
        if (! in_array($value, [static::UNDERLINE_NONE, static::UNDERLINE_SINGLE, static::UNDERLINE_DOUBLE])) {
            throw new XLSXException('Invalid font underline');
        }
        return $value;
    }
}
