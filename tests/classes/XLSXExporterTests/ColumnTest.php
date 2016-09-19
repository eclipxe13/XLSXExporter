<?php
namespace XLSXExporterTests;

use PHPUnit\Framework\TestCase;
use XLSXExporter\Column;
use XLSXExporter\Style;

class ColumnTest extends TestCase
{
    /** @var Column */
    private $column;
    private $id = 'foobar';

    public function setUp()
    {
        parent::setUp();
        $this->column = new Column($this->id);
    }

    public function testID()
    {
        $this->assertEquals($this->id, $this->column->getId());
    }

    public function testTitle()
    {
        $title = 'Foo Bar';
        $this->assertEquals('', $this->column->getTitle());
        $this->column->setTitle($title);
        $this->assertEquals($title, $this->column->getTitle());
    }

    public function testStyle()
    {
        $this->assertInstanceOf(Style::class, $this->column->getStyle());
    }
}
