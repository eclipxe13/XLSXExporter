<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Exceptions;

use RuntimeException;

final class WorkBookCreateZipFileException extends RuntimeException implements XlsxException
{
    private string $path;

    private int $returnCode;

    public function __construct(string $path, int $returnCode)
    {
        parent::__construct(sprintf('Unable to create zip file %s (err: %d)', $path, $returnCode));
        $this->path = $path;
        $this->returnCode = $returnCode;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getReturnCode(): int
    {
        return $this->returnCode;
    }
}
