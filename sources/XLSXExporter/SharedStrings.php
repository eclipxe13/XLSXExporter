<?php

namespace XLSXExporter;

use SplFileObject;
use XLSXExporter\Utils\XmlConverter;

/**
 * Collection of Shared strings, this collection is a increase-only, cannot reduce or clean
 */
class SharedStrings implements \Countable
{

    protected $strings = [];

    /**
     * Add a string into the collection, return the index of the string
     * If the string already exists it will return the previous index
     * @param string $string
     * @return integer
     */
    public function add($string)
    {
        // use the key instead of the content for faster access, it works like a bst
        if (array_key_exists($string, $this->strings)) {
            return $this->strings[$string];
        }
        $index = count($this->strings);
        $this->strings[$string] = $index;
        return $index;
    }

    /**
     * Write the XML content info to a file, the file name will be trucated
     * @return string Temporary file name
     */
    public function write()
    {
        $filename = tempnam(sys_get_temp_dir(), "ws-");
        $file = new SplFileObject($filename, "w");
        $this->writeTo($file);
        return $filename;
    }

    /**
     * Write the content to a SPlFileObject
     * @param SplFileObject $file
     */
    protected function writeTo(SplFileObject $file)
    {
        $count = count($this->strings);
        $file->fwrite(
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' count="'.$count.'" uniqueCount="'.$count.'">'
        );
        // Not using the index, is not needed
        // do not use array_keys, it (could?) duplicate the memory usage
        foreach ($this->strings as $string => $index) {
            $file->fwrite('<si><t>'.XmlConverter::parse($string).'</t></si>');
        }
        $file->fwrite('</sst>');
    }

    public function count($mode = 0)
    {
        return count($this->strings, $mode);
    }
}
