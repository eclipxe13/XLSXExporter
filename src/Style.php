<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter;

use BadMethodCallException;
use Eclipxe\XlsxExporter\Exceptions\InvalidPropertyNameException;
use Eclipxe\XlsxExporter\Exceptions\InvalidPropertyValueException;
use Eclipxe\XlsxExporter\Styles\StyleInterface;

/**
 * Class to access the style specification
 *
 * @property Styles\Alignment $alignment
 * @property Styles\Border $border
 * @property Styles\Fill $fill
 * @property Styles\Font $font
 * @property Styles\Format $format
 * @property Styles\Protection $protection
 * @method Styles\Alignment getAlignment()
 * @method Styles\Border getBorder()
 * @method Styles\Fill getFill()
 * @method Styles\Font getFont()
 * @method Styles\Format getFormat()
 * @method Styles\Protection getProtection()
 * @method $this setAlignment(Styles\Alignment $value)
 * @method $this setBorder(Styles\Border $value)
 * @method $this setFill(Styles\Fill $value)
 * @method $this setFont(Styles\Font $value)
 * @method $this setFormat(Styles\Format $value)
 * @method $this setProtection(Styles\Protection $value)
 */
class Style
{
    protected ?int $styleindex = null;

    /** @var array<string, Styles\StyleInterface> */
    protected $members = [];

    /**
     * @param array<string, array<string, scalar>> $arrayStyles
     */
    public function __construct(array $arrayStyles = [])
    {
        $this->members = [
            'format' => new Styles\Format(),
            'font' => new Styles\Font(),
            'fill' => new Styles\Fill(),
            'alignment' => new Styles\Alignment(),
            'border' => new Styles\Border(),
            'protection' => new Styles\Protection(),
        ];
        $this->setFromArray($arrayStyles);
    }

    /** @return StyleInterface */
    public function __get(string $name)
    {
        if (! isset($this->members[$name])) {
            throw new InvalidPropertyNameException($name);
        }
        return $this->members[$name];
    }

    /** @param mixed $value */
    public function __set(string $name, $value): void
    {
        if (! isset($this->members[$name])) {
            throw new InvalidPropertyNameException($name);
        }
        $styleclass = get_class($this->members[$name]);
        if (! $value instanceof $styleclass) {
            throw new InvalidPropertyValueException("The value must be an instance of \\$styleclass", $name, $value);
        }
        /** @var StyleInterface $value */
        $this->members[$name] = $value;
    }

    /**
     * @param mixed[] $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $getter = ('get' === substr($name, 0, 3));
        $setter = ('set' === substr($name, 0, 3));
        if (! $getter && ! $setter) {
            throw new BadMethodCallException("Invalid method name $name");
        }
        $name = lcfirst(substr($name, 3));
        if (! array_key_exists($name, $this->members)) {
            throw new BadMethodCallException("Invalid setter/getter name $name");
        }
        if ($getter) {
            return $this->{$name};
        }
        if (1 !== count($arguments)) {
            throw new BadMethodCallException('Invalid setter argument');
        }
        $this->{$name} = $arguments[0];
        return $this;
    }

    /**
     * @return string[]
     */
    public function getMemberNames(): array
    {
        return array_keys($this->members);
    }

    /**
     * Set styles from an array of key-values
     * Keys: format, font, fill, alignment, border, protection
     * @param array<string, array<string, scalar>> $array
     * @return $this
     */
    public function setFromArray(array $array): self
    {
        if ([] === $array) {
            return $this;
        }
        foreach ($this->members as $key => $style) {
            if (array_key_exists($key, $array) && is_array($array[$key])) {
                $style->setValues($array[$key]);
            }
        }
        return $this;
    }

    /**
     * Check if any of the styles has valid values
     */
    public function hasValues(): bool
    {
        foreach ($this->members as $style) {
            if ($style->hasValues()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return $this
     */
    public function setStyleIndex(int $index): self
    {
        $this->styleindex = $index;
        return $this;
    }

    public function getStyleIndex(): ?int
    {
        return $this->styleindex;
    }

    /**
     * Method to return the XML xf node
     * This method could be outside this object
     *
     * @internal
     */
    public function asXML(int $xfId = 0): string
    {
        // all the "apply" attributes are set to not inherit the value from cellStyleXfs
        return '<xf'
            . ' numFmtId="' . $this->format->id . '"'
            . ' fontId="' . $this->font->getIndex() . '"'
            . ' fillId="' . $this->fill->getIndex() . '"'
            . ' borderId="' . $this->border->getIndex() . '"'
            . ' xfId="' . $xfId . '"' // all is based on cellStyleXfs[0] by default
            . ' applyNumberFormat="false"'
            . ' applyFont="false"'
            . ' applyFill="false"'
            . ' applyBorder="false"'
            . ' applyAlignment="false"'
            . ' applyProtection="false"'
            . '>'
            . (($this->alignment->hasValues()) ? $this->alignment->asXML() : '')
            . (($this->protection->hasValues()) ? $this->protection->asXML() : '')
            . '</xf>'
        ;
    }
}
