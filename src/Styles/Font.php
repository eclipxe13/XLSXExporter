<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Styles;

use Eclipxe\XlsxExporter\Exceptions\InvalidPropertyValueException;
use Eclipxe\XlsxExporter\Utils\OpenXmlColor;

/**
 * @property string $name Font name
 * @property int $size Font size (pt)
 * @property bool $bold Font is bold
 * @property bool $italic Font is italic
 * @property bool $strike Font is stricken
 * @property string $underline Underline value (use constants)
 * @property bool $wordwrap Font is word wrapped
 * @property string $color Font color
 */
class Font extends AbstractStyle
{
    /** @phpstan-var string */
    public const UNDERLINE_NONE = 'none';

    /** @phpstan-var string */
    public const UNDERLINE_DOUBLE = 'double';

    /** @phpstan-var string */
    public const UNDERLINE_SINGLE = 'single';

    /** @var list<string> */
    private const UNDERLINE_VALUES = [
        self::UNDERLINE_NONE,
        self::UNDERLINE_SINGLE,
        self::UNDERLINE_DOUBLE,
    ];

    protected function properties(): array
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

    public function asXML(): string
    {
        // According to http://msdn.microsoft.com/en-us/library/ff531499%28v=office.12%29.aspx
        // Excel requires the child elements to be in the following sequence:
        // b, i, strike, condense, extend, outline, shadow, u, vertAlign, sz, color, name, family, charset, scheme
        return implode('', array_filter([
            '<font>',
            sprintf('<b val="%s" />', ($this->bold) ? '1' : '0'),
            sprintf('<i val="%s" />', ($this->italic) ? '1' : '0'),
            sprintf('<strike val="%s" />', ($this->strike) ? '1' : '0'),
            sprintf('<u val="%s" />', $this->underline ?: static::UNDERLINE_NONE),
            (($this->size) ? sprintf('<sz val="%d"/>', $this->size) : ''),
            (($this->color) ? sprintf('<color rgb="%s"/>', $this->color) : ''),
            (($this->name) ? sprintf('<name val="%s"/>', $this->name) : ''),
            '</font>',
        ]));
    }

    /** @param scalar|null $value */
    protected function castName($value): ?string
    {
        $value = trim((string) $value);
        return ('' !== $value) ? $value : null;
    }

    /** @param scalar|null $value */
    protected function castSize($value): int
    {
        return max(6, (int) $value);
    }

    /** @param scalar|null $value */
    protected function castBold($value): bool
    {
        return (bool) $value;
    }

    /** @param scalar|null $value */
    protected function castItalic($value): bool
    {
        return (bool) $value;
    }

    /** @param scalar|null $value */
    protected function castStrike($value): bool
    {
        return (bool) $value;
    }

    /** @param scalar|null $value */
    protected function castWordwrap($value): bool
    {
        return (bool) $value;
    }

    /** @param scalar|null $value */
    protected function castColor($value): string
    {
        if (! is_string($value) && ! is_int($value)) {
            $value = (string) $value;
        }
        $color = OpenXmlColor::cast($value);
        if (false === $color) {
            throw new InvalidPropertyValueException('Invalid font color value', 'color', $value);
        }
        return $color;
    }

    /** @param scalar|null $value */
    protected function castUnderline($value): string
    {
        $value = (string) $value;
        if (! in_array($value, self::UNDERLINE_VALUES, true)) {
            throw new InvalidPropertyValueException('Invalid font underline value', 'underline', $value);
        }
        return $value;
    }
}
