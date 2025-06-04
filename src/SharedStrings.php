<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter;

use Countable;
use Eclipxe\XlsxExporter\Utils\TemporaryFile;
use Eclipxe\XlsxExporter\Utils\XmlConverter;
use EngineWorks\ProgressStatus\NullProgress;
use EngineWorks\ProgressStatus\ProgressInterface;
use SplFileObject;

/**
 * Collection of Shared strings, this collection is an increase-only, cannot reduce or clean
 */
class SharedStrings implements Countable
{
    protected int $count = 0;

    /** @var array<string, int> */
    protected $strings = [];

    /**
     * Add a string into the collection, return the index of the string
     * If the string already exists it will return the previous index
     */
    public function add(string $string): int
    {
        // use the key instead of the content for faster access, it works like a binary search tree
        if (array_key_exists($string, $this->strings)) {
            return $this->strings[$string];
        }
        $this->strings[$string] = $this->count;
        return $this->count++;
    }

    /**
     * Write the XML content info to a file, the file name will be truncated
     */
    public function write(TemporaryFile $temporaryFile, ?ProgressInterface $progress = null): void
    {
        $file = new SplFileObject($temporaryFile->getPath(), 'w');
        $this->writeTo($file, $progress);
    }

    /**
     * Write the content to a SplFileObject
     */
    protected function writeTo(SplFileObject $file, ?ProgressInterface $progress = null): void
    {
        $progress ??= new NullProgress();
        $progress->update('', 1, 1 + $this->count);
        $file->fwrite(
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' count="' . $this->count . '" uniqueCount="' . $this->count . '">'
        );
        // Not using the index, is not needed
        // Do not use array_keys, it (could?) duplicate the memory usage
        /** @var string|int $string */
        foreach ($this->strings as $string => $index) {
            $file->fwrite('<si><t>' . XmlConverter::parse((string) $string) . '</t></si>');
            $progress->increase();
        }
        $file->fwrite('</sst>');
    }

    public function count(): int
    {
        return $this->count;
    }
}
