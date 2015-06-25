<?php

namespace XLSXExporterTests;

use XLSXExporter\Column;
use XLSXExporter\Columns;

class ColumnsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructMinimalParameters()
    {
        $o = new Columns();
        $this->assertInstanceOf("XLSXExporter\\Columns", $o);
        $this->assertInternalType("array", $o->all());
        $this->assertCount(0, $o->all());
        $this->assertEquals(0, $o->count());
    }

    public function testAddColumn()
    {
        $c = new Column("foo");
        $o = new Columns();
        $o->add($c);
        $this->assertEquals(1, $o->count(), "The count must be 1");
        $this->assertCount(1, $o->all(), "The count of all must be 1");
        $a = $o->all();
        $this->assertArrayHasKey("foo", $a, "method all must return an array with a key");
        $this->assertInstanceOf("XLSXExporter\\Column", $a["foo"]);
    }

    public function testAddAvoidDuplicity()
    {
        $c = new Column("foo");
        $o = new Columns();
        $o->add($c);
        $this->setExpectedException("XLSXExporter\\XLSXException", "There is a item with the same id, ids must be unique");
        $o->add($c);
    }

    public function testGetColumn()
    {
        $c = new Column("foo");
        $o = new Columns();
        $o->add($c);
        $this->assertInstanceOf("XLSXExporter\\Column", $o->get("foo"));
        $this->assertTrue($o->exists("foo"));
        $this->assertFalse($o->exists("baz"));
        $this->setExpectedException("XLSXExporter\\XLSXException", "The item baz does not exists");
        $o->get("baz");
    }

    public function testAddArrayOnlyAllowColumnObjects()
    {
        $o = new Columns();
        $this->setExpectedException("XLSXExporter\\XLSXException", "The item is not a valid object for the collection");
        $o->addArray([ new Column("foo"), new \stdClass(), new Column("bar") ]);
    }

    public function testCommonUsage()
    {
        $o = new Columns();
        $o->addArray([
            new Column("foo"),
            new Column("bar"),
            new Column("baz"),
        ]);
        $this->assertEquals(3, $o->count(), "The count must be 3");
        $this->assertCount(3, $o->all(), "The count of all must be 3");
        foreach($o as $key => $value) {
            $this->assertTrue(in_array($key, ["foo", "bar", "baz"]));
            $this->assertInstanceOf("XLSXExporter\\Column", $value);
        }
    }
}
