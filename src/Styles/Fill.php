<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Styles;

use Eclipxe\XLSXExporter\Exceptions\InvalidPropertyValueException;
use Eclipxe\XLSXExporter\Utils\OpenXmlColor;

/**
 * @property string $pattern Fill pattern
 * @property string $color Fill color
 */
class Fill extends AbstractStyle
{
    public const SOLID = 'solid';

    public const NONE = 'none';

    public const GRAY125 = 'gray125';

    private const VALUES = [
        self::NONE,
        self::GRAY125,
        self::SOLID,
    ];

    protected function properties(): array
    {
        return [
            'color',
            'pattern',
        ];
    }

    public function asXML(): string
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

    /** @param scalar|null $value */
    protected function castPattern($value): string
    {
        $value = (string) $value;
        if (! in_array($value, self::VALUES, true)) {
            throw new InvalidPropertyValueException('Invalid fill pattern value', 'pattern', $value);
        }
        return $value;
    }

    /** @param scalar|null $value */
    protected function castColor($value): string
    {
        if (! is_string($value) && ! is_int($value)) {
            $value = (string) $value;
        }
        $color = OpenXmlColor::cast($value);
        if (false === $color) {
            throw new InvalidPropertyValueException('Invalid fill color value', 'color', $value);
        }
        return $color;
    }
}
