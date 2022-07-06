<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests\Unit;

use Countable;
use Eclipxe\XLSXExporter\Tests\TestCase;
use Eclipxe\XLSXExporter\WorkSheet;
use Eclipxe\XLSXExporter\WorkSheets;
use Traversable;

final class WorkSheetsTest extends TestCase
{
    private WorkSheets $worksheets;

    protected function setUp(): void
    {
        $this->worksheets = new WorkSheets();
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(Countable::class, $this->worksheets);
        $this->assertInstanceOf(Traversable::class, $this->worksheets);
        $this->assertCount(0, $this->worksheets);
    }

    public function testAdd(): void
    {
        $foo = new WorkSheet('foo');
        $bar = new WorkSheet('bar');
        $this->worksheets->add($foo, $bar);
        $this->assertSame([$foo, $bar], $this->worksheets->all());
    }

    public function testRetrieveRepeatedNames(): void
    {
        $foo = new WorkSheet('xxx');
        $bar = new WorkSheet('xxx');
        $baz = new WorkSheet('zzz');
        $this->worksheets->add($foo, $bar, $baz);
        $repeated = $this->worksheets->retrieveRepeatedNames();
        $expected = ['xxx'];
        $this->assertSame($expected, $repeated);
    }
}
