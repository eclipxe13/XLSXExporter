<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Exceptions;

use LogicException;

final class InvalidPropertyValueException extends LogicException implements XlsxException
{
    private string $propertyName;

    /** @var mixed */
    private $value;

    /** @param mixed $value */
    public function __construct(string $message, string $propertyName, $value)
    {
        parent::__construct($message);
        $this->propertyName = $propertyName;
        $this->value = $value;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    /** @return mixed */
    public function getValue()
    {
        return $this->value;
    }
}
