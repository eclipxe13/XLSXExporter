<?php

namespace XLSXExporter;

class CompleteTest extends \PHPUnit_Framework_TestCase
{

    public function testComplete()
    {
        $a = new ProviderArray([
            ["fname" => "Charles", "lname" => "Dickens", "amount" => 1234.56, "visit" => strtotime('2014-01-13 13:14:15'), "check" => 1],
            ["fname" => "Foo", "lname" => "Bar", "amount" => 6543.21, "visit" => strtotime('2014-12-31 23:59:59'), "check" => 0],
        ]);
        $wb = new WorkBook(new WorkSheets([
            new WorkSheet("sheet01", $a, new Columns([
                new Column("fname", "First Name"),
                new Column("lname", "Last Name"),
                new Column("amount", "Debit amount", CellTypes::NUMBER, BasicStyles::withStdFormat(Styles\Format::FORMAT_ZERO_2DECS)),
                new Column("visit", "Visit", CellTypes::DATETIME, BasicStyles::withStdFormat(Styles\Format::FORMAT_DATE_YMDHM)),
                new Column("check", "Check", CellTypes::NUMBER, BasicStyles::withStdFormat(Styles\Format::FORMAT_YESNO)),
            ]))
        ]));
        $tempfile = $wb->write();
        $this->assertFileExists($tempfile);
        // copy the file to a certain location
        // $this->assertTrue(copy($tempfile, "result.xlsx"));
        unlink($tempfile);
    }

}