<?php
namespace classes\XLSXExporterTests;

use PHPUnit\Framework\TestCase;
use XLSXExporter\WorkSheet;
use XLSXExporter\XLSXException;

class WorkSheetTest extends TestCase
{
    /** @var WorkSheet */
    private $worksheet;

    protected function setUp()
    {
        parent::setUp();
        $this->worksheet = new WorkSheet('Sheet1');
    }

    public function testWorkSheetGetName()
    {
        $this->assertSame('Sheet1', $this->worksheet->getName());
        $this->assertSame('Sheet1', $this->worksheet->name);
    }

    public function testWorkSheetSetName()
    {
        $this->worksheet->setName('foo');
        $this->assertSame('foo', $this->worksheet->getName());
    }

    public function testWorkSheetSetNameWithNotAString()
    {
        $this->expectException(XLSXException::class);
        $this->expectExceptionMessage('Invalid worksheet name, is not a string');
        $this->worksheet->setName(null);
    }

    public function testWorkSheetSetNameWithEmptyString()
    {
        $this->expectException(XLSXException::class);
        $this->expectExceptionMessage('Invalid worksheet name, is empty');
        $this->worksheet->setName('');
    }

    public function testWorkSheetSetNameWithLongString()
    {
        $expected = str_repeat('x', 31);
        $this->worksheet->setName($expected);
        $this->assertSame($expected, $this->worksheet->getName());
        $this->expectException(XLSXException::class);
        $this->expectExceptionMessage('Invalid worksheet name, is more than 31 chars length');
        $this->worksheet->setName(str_repeat('x', 32));
    }

    public function providerWorkSheetSetNameWithInvalidString()
    {
        return [[':'], ['/'], ['\\'], ['?'], ['*'], ['['], [']'], ["'"], ["\t"], ["\r"], ["\n"], ["\0"]];
    }

    /**
     * @param $name
     * @dataProvider providerWorkSheetSetNameWithInvalidString
     */
    public function testWorkSheetSetNameWithInvalidString($name)
    {
        $this->expectException(XLSXException::class);
        $this->expectExceptionMessage('Invalid worksheet name, contains invalid chars');
        $this->worksheet->setName($name);
    }
}
