<?php

namespace XLSXExporter;

class WorkSheetTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructorMinimal()
    {
        $o = new XLSXExporter();
        $this->setExpectedException("XLSXExporter\\XLSXException", "Invalid property name foobar");
        $o->foobar;

    }


    public function testColumnsAccess()
    {
        $o = new XLSXExporter();
        $this->assertInstanceOf("XLSXExporter\\Columns", $o->getColumns());
        $this->assertInstanceOf("XLSXExporter\\Columns", $o->columns);
    }


    public function testExport()
    {
        $provider = new ProviderArray([
            ["fname" => "John", "lname" => "Doe", "pdate" => mktime(13, 14, 15, 11, 16, 2014), "amount" => 12345.678],
            ["fname" => "Charles", "lname" => "Dickens", "pdate" => mktime(13, 14, 15, 11, 16, 2014), "amount" => 654321.01],
        ]);
        $ws = new WorkSheet(
            new Columns([
                new Column("fname", "First Name"),
                new Column("lname", "Last Name"),
                new Column("pdate", "Payment date"),
                new Column("amount", "Amount"),
            ]),
            $provider
        );

    }

}
