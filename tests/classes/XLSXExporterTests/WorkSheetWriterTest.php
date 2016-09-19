<?php
namespace XLSXExporterTests;

use XLSXExporter\WorkSheetWriter;

class WorkSheetWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testStaticIntegerToColumn()
    {
        $a = [
            1 => 'A',
            2 => 'B',
            9 => 'I',
            10 => 'J',
            19 => 'S',
            20 => 'T',
            26 => 'Z',
            27 => 'AA',
            52 => 'AZ',
            53 => 'BA',
            1024 => 'AMJ',
        ];
        foreach ($a as $n => $v) {
            $this->assertEquals($v, WorkSheetWriter::colByNumber($n));
        }
    }
}
