<?php

namespace XLSXExporter;

use SplFileObject;

class SharedStrings {

    protected $strings = [];

    public function getIndex($string)
    {
        if (false === $index = array_search($string, $this->strings, true)) {
            $index = count($this->strings);
            $this->strings[] = $string;
        }
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
        for($i = 0 ; $i < $count ; $i++) {
            $file->fwrite('<si><t>'.WorkSheetWriter::xml($this->strings[$i]).'</t></si>');
        }
        $file->fwrite('</sst>');
        return $tempfile;
    }


}
