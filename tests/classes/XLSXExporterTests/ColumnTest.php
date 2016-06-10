<?php

namespace XLSXExporterTests;

use XLSXExporter\Column;

class ColumnTest extends \PHPUnit_Framework_TestCase
{
    private $column;
    private $id = "foobar";

    public function setUp()
    {
        parent::setUp();
        $this->column = new Column($this->id);
    }

    public function testID()
    {
        $this->assertEquals($this->id, $this->column->getID());
    }

    public function testTitle()
    {
        $title = "Foo Bar";
        $this->assertEquals("", $this->column->getTitle());
        $this->column->setTitle($title);
        $this->assertEquals($title, $this->column->getTitle());
    }

    public function testStyle()
    {
        $this->assertInstanceOf("XLSXExporter\\Style", $this->column->getStyle());
    }
}
