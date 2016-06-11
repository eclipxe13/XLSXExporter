<?php

namespace XLSXExporterTests;

use XLSXExporter\Column;
use XLSXExporter\Columns;
use XLSXExporter\XLSXException;

class ColumnsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructMinimalParameters()
    {
        $o = new Columns();
        $this->assertInstanceOf(Columns::class, $o);
        $this->assertInternalType("array", $o->all());
        $this->assertCount(0, $o->all());
        $this->assertEquals(0, $o->count());
    }

    public function testAddColumn()
    {
        $c = new Column("foo");
        $o = new Columns();
        $o->add($c);
        $this->assertCount(1, $o, "The count must be 1");
        $this->assertSame([$c], $o->all(), "The contents of the columns is not the same");
    }

    public function testAddAllowDuplicity()
    {
        $c = new Column("foo");
        $o = new Columns();
        $o->add($c);
        $o->add($c);
        $this->assertCount(2, $o);
    }

    public function testGetColumn()
    {
        $c = new Column("foo");
        $o = new Columns();
        $o->add($c);
        $this->assertInstanceOf(Column::class, $o->getById("foo"));
        $this->assertTrue($o->existsById("foo"));
        $this->assertFalse($o->existsById("baz"));
        $this->expectException(XLSXException::class);
        $this->expectExceptionMessage("The item baz does not exists");
        $o->getById("baz");
    }

    public function testAddArrayOnlyAllowColumnObjects()
    {
        $o = new Columns();
        $this->expectException(XLSXException::class);
        $this->expectExceptionMessage("Invalid Column object");
        $o->addArray([ new Column("foo"), new \stdClass(), new Column("bar") ]);
    }

    public function testCommonUsage()
    {
        $expectedArray = [
            new Column("foo"),
            new Column("bar"),
            new Column("baz"),
        ];
        $o = new Columns();
        $o->addArray($expectedArray);
        $this->assertSame($expectedArray, $o->all());
    }
}
