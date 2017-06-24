<?php
namespace XLSXExporter;

use EngineWorks\ProgressStatus\NullProgress;
use EngineWorks\ProgressStatus\ProgressInterface;
use SplFileObject;
use XLSXExporter\Utils\XmlConverter;

/**
 * Collection of Shared strings, this collection is a increase-only, cannot reduce or clean
 */
class SharedStrings implements \Countable
{
    /** @var int */
    protected $count = 0;

    /** @var string[] */
    protected $strings = [];

    /**
     * Add a string into the collection, return the index of the string
     * If the string already exists it will return the previous index
     * @param string $string
     * @return int
     */
    public function add($string)
    {
        // use the key instead of the content for faster access, it works like a binary search tree
        if (array_key_exists($string, $this->strings)) {
            return $this->strings[$string];
        }
        $this->strings[$string] = $this->count;
        return $this->count++;
    }

    /**
     * Write the XML content info to a file, the file name will be trucated
     * @param ProgressInterface $progress
     * @return string Temporary file name
     */
    public function write(ProgressInterface $progress = null)
    {
        $filename = tempnam(sys_get_temp_dir(), 'ws-');
        $file = new SplFileObject($filename, 'w');
        $this->writeTo($file, $progress ? : new NullProgress());
        return $filename;
    }

    /**
     * Write the content to a SPlFileObject
     * @param SplFileObject $file
     * @param ProgressInterface $progress
     */
    protected function writeTo(SplFileObject $file, ProgressInterface $progress = null)
    {
        $progress = $progress ? : new NullProgress();
        $progress->update('', 1, 1 + $this->count);
        $file->fwrite(
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' count="' . $this->count . '" uniqueCount="' . $this->count . '">'
        );
        // Not using the index, is not needed
        // do not use array_keys, it (could?) duplicate the memory usage
        foreach ($this->strings as $string => $index) {
            $file->fwrite('<si><t>' . XmlConverter::parse($string) . '</t></si>');
            $progress->increase();
        }
        $file->fwrite('</sst>');
    }

    public function count()
    {
        return $this->count;
    }
}
