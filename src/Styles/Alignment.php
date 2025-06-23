<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Styles;

use Eclipxe\XlsxExporter\Exceptions\InvalidPropertyValueException;

/**
 * @property string $horizontal Horizontal alignment
 * @property string $vertical Vertical alignment
 * @property bool $wraptext Wrap text
 */
class Alignment extends AbstractStyle
{
    public const HORIZONTAL_GENERAL = 'general';

    public const HORIZONTAL_LEFT = 'left';

    public const HORIZONTAL_CENTER = 'center';

    public const HORIZONTAL_RIGHT = 'right';

    public const HORIZONTAL_JUSTIFY = 'justify';

    private const HORIZONTAL_VALUES = [
        self::HORIZONTAL_GENERAL,
        self::HORIZONTAL_LEFT,
        self::HORIZONTAL_CENTER,
        self::HORIZONTAL_RIGHT,
        self::HORIZONTAL_JUSTIFY,
    ];

    public const VERTICAL_TOP = 'top';

    public const VERTICAL_BOTTOM = 'bottom';

    public const VERTICAL_CENTER = 'center';

    private const VERTICAL_VALUES = [
        self::VERTICAL_TOP,
        self::VERTICAL_CENTER,
        self::VERTICAL_BOTTOM,
    ];

    protected function properties(): array
    {
        return [
            'horizontal',
            'vertical',
            'wraptext',
        ];
    }

    public function asXML(): string
    {
        return '<alignment'
            . (($this->horizontal) ? sprintf(' horizontal="%s"', $this->horizontal) : '')
            . (($this->vertical) ? sprintf(' vertical="%s"', $this->vertical) : '')
            . ((null !== $this->wraptext) ? sprintf(' wrapText="%s"', $this->wraptext ? '1' : '0') : '')
            . '/>'
        ;
    }

    /** @param scalar|null $value */
    protected function castHorizontal($value): string
    {
        $value = (string) $value;
        if (! in_array($value, self::HORIZONTAL_VALUES, true)) {
            throw new InvalidPropertyValueException('Invalid alignment horizontal value', 'horizontal', $value);
        }
        return $value;
    }

    /** @param scalar|null $value */
    protected function castVertical($value): string
    {
        $value = (string) $value;
        if (! in_array($value, self::VERTICAL_VALUES, true)) {
            throw new InvalidPropertyValueException('Invalid alignment vertical value', 'vertical', $value);
        }
        return $value;
    }

    /** @param scalar|null $value */
    protected function castWraptext($value): bool
    {
        return (bool) $value;
    }
}
