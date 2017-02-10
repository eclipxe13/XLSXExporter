<?php
namespace XLSXExporterTests;

use PHPUnit\Framework\TestCase;
use XLSXExporter\CellTypes;
use XLSXExporter\WorkSheetWriter;

class WorkSheetWriterTest extends TestCase
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

    public function providerWriteCell()
    {
        $time = mktime(23, 59, 30, 12, 31, 2016);
        return [
            'style' => [CellTypes::TEXT, '', 'style', '<c r="A1" s="style" t="s"><v></v></c>'],
            'text as null' => [CellTypes::TEXT, null, '', '<c r="A1" t="n"><v></v></c>'],
            'empty as text' => [CellTypes::TEXT, '', '', '<c r="A1" t="s"><v></v></c>'],
            'foo as text' => [CellTypes::TEXT, 'foo', '', '<c r="A1" t="s"><v>foo</v></c>'],
            'false as boolean' => [CellTypes::BOOLEAN, false, '', '<c r="A1" t="b"><v>0</v></c>'],
            'true as boolean' => [CellTypes::BOOLEAN, true, '', '<c r="A1" t="b"><v>1</v></c>'],
            'date' => [CellTypes::DATE, $time, '', '<c r="A1"><v>42735</v></c>'],
            'datetime' => [CellTypes::DATETIME, $time, '', '<c r="A1"><v>42735.999653</v></c>'],
            'time' => [CellTypes::TIME, $time, '', '<c r="A1"><v>0.999653</v></c>'],
            'foo as inline' => [CellTypes::INLINE, 'foo', '', '<c r="A1"><is><t>foo</t></is></c>'],
        ];
    }

    /**
     * @param $cellType
     * @param $value
     * @param $style
     * @param $expectedContent
     * @dataProvider providerWriteCell
     */
    public function testWriteCell($cellType, $value, $style, $expectedContent)
    {
        $filename = TestUtils::buildPath() . '/worksheet-writecell.xml';
        $wsr = new WorkSheetWriter();
        $wsr->createSheet($filename, 1, 1);
        $wsr->writeCell($cellType, $value, $style);
        $this->assertEquals($expectedContent, file_get_contents($filename));
        unlink($filename);
    }
}
