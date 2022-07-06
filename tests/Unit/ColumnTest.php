<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests\Unit;

use Eclipxe\XLSXExporter\Column;
use Eclipxe\XLSXExporter\Exceptions\InvalidPropertyNameException;
use Eclipxe\XLSXExporter\Style;
use Eclipxe\XLSXExporter\Tests\TestCase;

final class ColumnTest extends TestCase
{
    private Column $column;

    private string $id = 'foobar';

    public function setUp(): void
    {
        parent::setUp();
        $this->column = new Column($this->id);
    }

    public function testId(): void
    {
        $this->assertEquals($this->id, $this->column->getId());
    }

    public function testTitle(): void
    {
        $title = 'Foo Bar';
        $this->assertEquals('', $this->column->getTitle());
        $this->column->setTitle($title);
        $this->assertEquals($title, $this->column->getTitle());
    }

    public function testStyle(): void
    {
        $this->assertInstanceOf(Style::class, $this->column->getStyle());
    }

    public function testInvalidPropertyGet(): void
    {
        $this->expectException(InvalidPropertyNameException::class);
        $this->expectExceptionMessage('Invalid property name foo');
        echo $this->column->{'foo'}; /** @phpstan-ignore-line */
    }

    public function testInvalidPropertySet(): void
    {
        $this->expectException(InvalidPropertyNameException::class);
        $this->expectExceptionMessage('Invalid property name foo');
        echo $this->column->{'foo'} = 'bar'; /** @phpstan-ignore-line */
    }

    public function testMagicProperty(): void
    {
        $style = new Style();
        $this->column->style = $style;
        $this->assertSame($style, $this->column->style);
    }
}
