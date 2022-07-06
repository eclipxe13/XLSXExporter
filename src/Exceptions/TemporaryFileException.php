<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Exceptions;

use RuntimeException;

final class TemporaryFileException extends RuntimeException implements XLSXException
{
    // @phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
