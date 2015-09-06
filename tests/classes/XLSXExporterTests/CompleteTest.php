<?php

namespace XLSXExporterTests;

use XLSXExporter\ProviderArray;
use XLSXExporter\WorkBook;
use XLSXExporter\WorkSheet;
use XLSXExporter\WorkSheets;
use XLSXExporter\Columns;
use XLSXExporter\Column;
use XLSXExporter\CellTypes;
use XLSXExporter\Style;
use XLSXExporter\Styles\Format;
use XLSXExporter\Styles\Alignment;

class CompleteTest extends \PHPUnit_Framework_TestCase
{

    public function testComplete()
    {
        // The provider
        $a = new ProviderArray([
            ["fname" => "Charles", "amount" => 1234.561, "visit" => strtotime('2014-01-13 13:14:15'), "check" => 1],
            ["fname" => "Foo", "amount" => 6543.219, "visit" => strtotime('2014-12-31 23:59:59'), "check" => 0],
        ]);
        // The workbook and columns
        $wb = new WorkBook(new WorkSheets([
            new WorkSheet("data", $a, new Columns([
                new Column("fname", "Name"),
                new Column("amount", "Amount", CellTypes::NUMBER,
                    (new Style())->setFromArray(["format" => ["code" => Format::FORMAT_COMMA_2DECS], "font" => ["bold" => 1]])),
                new Column("visit", "Visit", CellTypes::DATETIME,
                    (new Style())->setFromArray(["format" => ["code" => Format::FORMAT_DATE_YMDHM], "protection" => ["hidden" => 1, "locked" => 1]])),
                new Column("check", "Check", CellTypes::BOOLEAN,
                    (new Style())->setFromArray(["format" => ["code" => Format::FORMAT_YESNO], "alignment" => Alignment::HORIZONTAL_CENTER])),
            ]))
        ]));
        // write to a temporary file
        $tempfile = $wb->write();
        $this->assertFileExists($tempfile);
        // copy the file to a certain location
        $this->assertTrue(copy($tempfile, __DIR__ . "/../../result.xlsx"));
        // remove temporary file
        unlink($tempfile);
    }

}