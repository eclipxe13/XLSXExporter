<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Exceptions;

use LogicException;

final class InvalidDestinationFileException extends LogicException implements XLSXException
{
    private string $path;

    private function __construct(string $message, string $path)
    {
        parent::__construct("Invalid destination file: $message");
        $this->path = $path;
    }

    public static function isDirectory(string $path): self
    {
        return new self('Path exists and is a directory', $path);
    }

    public static function existsAndParentisNotDirectory(string $path): self
    {
        return new self('Path does not exists and parent directory is not a directory', $path);
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
