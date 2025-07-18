<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Styles;

use Eclipxe\XlsxExporter\Exceptions\InvalidPropertyNameException;
use Eclipxe\XlsxExporter\Exceptions\InvalidPropertyValueException;

/**
 * Abstract implementation of the StyleInterface for internal use
 */
abstract class AbstractStyle implements StyleInterface
{
    /**
     * Storage of the properties contents
     * @var array<string, scalar|null>
     */
    protected array $data = [];

    /** @var int|null Index property */
    protected ?int $index = null;

    /**
     * Get an array of property names
     *
     * @return string[]
     */
    abstract protected function properties(): array;

    abstract public function asXML(): string;

    public function setValues(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getValues(): array
    {
        $array = [];
        foreach ($this->properties() as $key) {
            $value = $this->{$key};
            $array[$key] = (is_null($value) || is_scalar($value)) ? $value : null;
        }
        return $array;
    }

    /** @return scalar|null */
    public function __get(string $name)
    {
        if (! in_array($name, $this->properties())) {
            throw new InvalidPropertyNameException($name);
        }

        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            // use getter if method exists
            $value = $this->$method();
        } else {
            // return data value if method exists
            $value = $this->data[$name] ?? null;
        }

        return (is_null($value) || is_scalar($value)) ? $value : null;
    }

    /** @param scalar|null $value */
    public function __set(string $name, $value): void
    {
        if (! in_array($name, $this->properties())) {
            throw new InvalidPropertyNameException($name);
        }

        // cast value if method exists
        $method = 'cast' . ucfirst($name);
        if (method_exists($this, $method)) {
            $value = $this->$method($value);
        }

        if (! is_null($value) && ! is_scalar($value)) {
            throw new InvalidPropertyValueException(sprintf('Invalid value for property %s', $name), $name, $value);
        }

        // use setter if method exists, otherwise just set the value on $data
        $method = 'set' . ucfirst($name);
        if (method_exists($this, $method)) {
            $this->$method($value);
        } else {
            $this->data[$name] = $value;
        }
    }

    public function __isset(string $name)
    {
        return isset($this->data[$name]);
    }

    public function hasValues(): bool
    {
        return [] !== $this->data;
    }

    public function getIndex(): ?int
    {
        return $this->index;
    }

    public function setIndex(int $index): void
    {
        $this->index = $index;
    }

    public function getHash(): string
    {
        return sha1(get_class($this) . '::' . print_r($this->data, true));
    }
}
