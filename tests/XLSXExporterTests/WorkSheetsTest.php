<?php
namespace XLSXExporterTests;

use PHPUnit\Framework\TestCase;
use XLSXExporter\Collection;
use XLSXExporter\WorkSheet;
use XLSXExporter\WorkSheets;
use XLSXExporter\XLSXException;

class WorkSheetsTest extends TestCase
{
    /** @var WorkSheets */
    private $worksheets;

    protected function setUp()
    {
        $this->worksheets = new WorkSheets();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(Collection::class, $this->worksheets);
        $this->assertCount(0, $this->worksheets);
    }

    public function testAdd()
    {
        $foo = new WorkSheet('foo');
        $bar = new WorkSheet('bar');
        $this->worksheets->add($foo);
        $this->worksheets->add($bar);
        $this->assertSame([$foo, $bar], $this->worksheets->all());
    }

    public function providerAddThrowsException()
    {
        return [[null], [new \stdClass()], ['foo']];
    }

    /**
     * @dataProvider providerAddThrowsException
     * @param $element
     */
    public function testAddThrowsException($element)
    {
        $this->expectException(XLSXException::class);
        $this->expectExceptionMessage('Invalid WorkSheet object');
        $this->worksheets->add($element);
    }

    public function testRetrieveRepeatedNames()
    {
        $foo = new WorkSheet('xxx');
        $bar = new WorkSheet('xxx');
        $baz = new WorkSheet('zzz');
        $this->worksheets->addArray([$foo, $bar, $baz]);
        $repeated = $this->worksheets->retrieveRepeatedNames();
        $expected = ['xxx'];
        $this->assertSame($expected, $repeated);
    }
}
