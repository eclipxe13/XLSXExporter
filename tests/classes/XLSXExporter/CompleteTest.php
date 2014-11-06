<?php

namespace XLSXExporter;

class CompleteTest extends \PHPUnit_Framework_TestCase
{

    public function testComplete()
    {
        $a = new ProviderArray([
            ["fname" => "Charles", "lname" => "Dickens", "amount" => 1234.56],
            ["fname" => "Foo", "lname" => "Bar", "amount" => 6543.21],
        ]);
        $wb = new WorkBook(new WorkSheets([
            new WorkSheet("sheet01", $a, new Columns([
                new Column("fname", "First Name"),
                new Column("lname", "Last Name"),
                new Column("amount", "Debit amount", CellTypes::NUMBER, BasicStyles::withStdFormat(Styles\Format::FORMAT_ZERO_2DECS)),
            ]))
        ]));
        $tempfile = $wb->write();
        $this->assertFileExists($tempfile);
        $this->assertTrue(copy($tempfile, "/home/eclipxe/xlsx/result.xlsx"));
        unlink($tempfile);
    }

}