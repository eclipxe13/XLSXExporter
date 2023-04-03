<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Tests\Unit;

use Eclipxe\XlsxExporter\CellTypes;
use Eclipxe\XlsxExporter\Tests\TestCase;
use Eclipxe\XlsxExporter\Utils\TemporaryFile;
use Eclipxe\XlsxExporter\WorkSheetWriter;

final class WorkSheetWriterTest extends TestCase
{
    public function testStaticIntegerToColumn(): void
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

    /**
     * @return array<string, array{string, scalar|null, string, string}>
     */
    public function providerWriteCell(): array
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
     * @param scalar|null $value
     * @dataProvider providerWriteCell
     */
    public function testWriteCell(string $cellType, $value, string $style, string $expectedContent): void
    {
        $tempfile = new TemporaryFile();
        $filename = $tempfile->getPath();
        $wsr = new WorkSheetWriter();
        $wsr->createSheet($filename, 1, 1);
        $wsr->writeCell($cellType, $value, $style);
        $this->assertEquals($expectedContent, $tempfile->getContents());
    }
}
