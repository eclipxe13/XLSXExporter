<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter;

use Eclipxe\XlsxExporter\Exceptions\InvalidPropertyNameException;
use Eclipxe\XlsxExporter\Exceptions\InvalidPropertyValueException;

/**
 * @property-read string $id Column identifier
 * @property string $type Type of cell
 * @property string $title Column title
 * @property Style $style Style object
 */
class Column
{
    protected string $id;

    protected string $type;

    protected string $title;

    protected Style $style;

    public function __construct(string $id, string $title = '', string $type = CellTypes::TEXT, ?Style $style = null)
    {
        $this->setId($id);
        $this->setType($type);
        $this->setTitle($title);
        $this->setStyle($style ?? new Style());
    }

    /** @return mixed */
    public function __get(string $name)
    {
        $props = ['id', 'type', 'title', 'style'];
        if (! in_array($name, $props)) {
            throw new InvalidPropertyNameException($name);
        }
        $method = 'get' . ucfirst($name);
        return $this->$method();
    }

    /** @param mixed $value */
    public function __set(string $name, $value): void
    {
        if ('type' === $name) {
            if (! is_string($value)) {
                throw new InvalidPropertyValueException('Invalid type, expected string', 'type', $value);
            }
            $this->setType($value);
            return;
        }
        if ('title' === $name) {
            if (! is_string($value)) {
                throw new InvalidPropertyValueException('Invalid title, expected string', 'title', $value);
            }
            $this->setTitle($value);
            return;
        }
        if ('style' === $name) {
            if (! $value instanceof Style) {
                throw new InvalidPropertyValueException(sprintf('Invalid style, expected %s', Style::class), 'style', $value);
            }
            $this->setStyle($value);
            return;
        }
        throw new InvalidPropertyNameException($name);
    }

    protected function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setStyle(Style $style): void
    {
        $this->style = $style;
    }

    public function getStyle(): Style
    {
        return $this->style;
    }
}
