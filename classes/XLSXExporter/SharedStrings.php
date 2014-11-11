<?php

namespace XLSXExporter;

use SplFileObject;

class SharedStrings {

    protected $strings = [];

    public function getIndex($string)
    {
        // use the key instead of the content for faster access, it works like a bst
        if (array_key_exists($string, $this->strings)) {
            return $this->strings[$string];
        }
        $index = count($this->strings);
        $this->strings[$string] = $index;
        return $index;
    }

    public function write()
    {
        $tempfile = tempnam(sys_get_temp_dir(), "ws-");
        $file = new SplFileObject($tempfile, "a");
        $count = count($this->strings);
        $file->fwrite(
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
            .'<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.$count.'" uniqueCount="'.$count.'">'
        );
        // Not using the index, is not needed
        // do not use array_keys, it (could?) duplicate the memory used
        foreach($this->strings as $string => $index) {
            $file->fwrite('<si><t>'.XmlConverter::specialchars($string).'</t></si>');
        }
        $file->fwrite('</sst>');
        return $tempfile;
    }


}
