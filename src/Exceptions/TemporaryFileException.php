<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Exceptions;

use RuntimeException;

final class TemporaryFileException extends RuntimeException implements XlsxException
{
    // @phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
