<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Exceptions;

use LogicException;

final class InvalidWorkSheetNameException extends LogicException implements XLSXException
{
    private string $name;

    private string $reason;

    public function __construct(string $name, string $reason)
    {
        parent::__construct("Invalid worksheet name '$name': $reason");
        $this->name = $name;
        $this->reason = $reason;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}
