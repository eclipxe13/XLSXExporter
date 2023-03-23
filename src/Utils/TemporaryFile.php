<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Utils;

use Eclipxe\XlsxExporter\Exceptions\TemporaryFileException;

/**
 * Class to create a temporary file and remove it on object destruction
 * @internal
 */
final class TemporaryFile
{
    private string $path;

    public function __construct(string $prefix = '', string $directory = '')
    {
        $path = tempnam($directory, $prefix);
        if (false === $path) {
            throw new TemporaryFileException('Unable to create a temporary file'); // @codeCoverageIgnore
        }
        $this->path = $path;
    }

    public function __destruct()
    {
        if (file_exists($this->path)) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            @unlink($this->path);
        }
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getContents(): string
    {
        $contents = file_get_contents($this->path);
        if (false === $contents) {
            throw new TemporaryFileException(sprintf('Can not read contents from %s', $this->getPath()));
        }
        return $contents;
    }

    public function passthru(): void
    {
        if (false === $file = fopen($this->getPath(), 'r')) {
            throw new TemporaryFileException(sprintf('Can not open file %s', $this->getPath()));
        }
        /** @var false|int $passthru Is only INT from PHP 8.0 */
        $passthru = fpassthru($file);
        fclose($file);
        if (false === $passthru) {
            throw new TemporaryFileException(sprintf('Can not passthru %s', $this->getPath()));
        }
    }

    public function copy(string $destinationPath): void
    {
        if (! copy($this->getPath(), $destinationPath)) {
            throw new TemporaryFileException(sprintf('Cannot copy %s to %s', $this->getPath(), $destinationPath));
        }
    }
}
