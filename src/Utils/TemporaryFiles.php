<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Utils;

use ArrayObject;

/**
 * Class to create a temporary file and remove it on object destruction
 * @internal
 */
final class TemporaryFiles
{
    /** @var ArrayObject<int, TemporaryFile> */
    private ArrayObject $files;

    public function __construct()
    {
        $this->files = new ArrayObject();
    }

    public function __destruct()
    {
        $this->clear();
    }

    public function create(string $prefix = '', string $directory = ''): TemporaryFile
    {
        $temporaryFile = new TemporaryFile($prefix, $directory);
        $this->files[] = $temporaryFile;
        return $temporaryFile;
    }

    public function clear(): void
    {
        foreach ($this->files as $key => $_) {
            unset($this->files[$key]);
        }
    }
}
