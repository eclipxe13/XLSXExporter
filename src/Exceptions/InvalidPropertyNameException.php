<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Exceptions;

use LogicException;

final class InvalidPropertyNameException extends LogicException implements XlsxException
{
    private string $propertyName;

    public function __construct(string $propertyName)
    {
        parent::__construct("Invalid property name $propertyName");
        $this->propertyName = $propertyName;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }
}
