<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests\Unit;

use Countable;
use Eclipxe\XLSXExporter\Column;
use Eclipxe\XLSXExporter\Columns;
use Eclipxe\XLSXExporter\Exceptions\ItemNotFoundException;
use Eclipxe\XLSXExporter\Tests\TestCase;
use Traversable;

final class ColumnsTest extends TestCase
{
    public function testConstructMinimalParameters(): void
    {
        $o = new Columns();
        $this->assertInstanceOf(Countable::class, $o);
        $this->assertInstanceOf(Traversable::class, $o);
        $this->assertEquals(0, $o->count());
    }

    public function testAddColumn(): void
    {
        $foo = new Column('foo');
        $bar = new Column('bar');
        $o = new Columns();
        $o->add($foo, $bar);
        $this->assertCount(2, $o, 'The count must be 2');
        $this->assertSame([$foo, $bar], $o->all(), 'The contents of the columns is not the same');
    }

    public function testAddAllowDuplicity(): void
    {
        $c = new Column('foo');
        $o = new Columns();
        $o->add($c);
        $o->add($c);
        $this->assertCount(2, $o);
    }

    public function testGetColumn(): void
    {
        $c = new Column('foo');
        $o = new Columns();
        $o->add($c);
        $this->assertInstanceOf(Column::class, $o->getById('foo'));
        $this->assertTrue($o->existsById('foo'));
        $this->assertFalse($o->existsById('baz'));
        $this->expectException(ItemNotFoundException::class);
        $this->expectExceptionMessage('The item with id baz does not exists');
        $o->getById('baz');
    }

    public function testCommonUsage(): void
    {
        $expectedArray = [
            new Column('foo'),
            new Column('bar'),
            new Column('baz'),
        ];
        $o = new Columns();
        $o->add(...$expectedArray);
        $this->assertSame($expectedArray, $o->all());
    }
}
