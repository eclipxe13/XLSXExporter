<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests\Unit\Styles;

use Eclipxe\XLSXExporter\Exceptions\InvalidPropertyValueException;
use Eclipxe\XLSXExporter\Styles\AbstractStyle;
use Eclipxe\XLSXExporter\Styles\Font;
use Eclipxe\XLSXExporter\Styles\StyleInterface;
use Eclipxe\XLSXExporter\Tests\TestCase;

final class FontTest extends TestCase
{
    public function testConstructor(): void
    {
        $style = new Font();
        $this->assertInstanceOf(StyleInterface::class, $style);
        $this->assertInstanceOf(AbstractStyle::class, $style);
        $this->assertNull($style->name);
        $this->assertNull($style->size);
        $this->assertNull($style->bold);
        $this->assertNull($style->italic);
        $this->assertNull($style->strike);
        $this->assertNull($style->underline);
        $this->assertNull($style->wordwrap);
        $this->assertNull($style->color);
    }

    public function testNameProperty(): void
    {
        $style = new Font();
        $this->assertNull($style->name);
        $style->name = 'Calibri';
        $this->assertSame('Calibri', $style->name);

        $style->name = '';
        $this->assertNull($style->name);
    }

    public function testColorProperty(): void
    {
        $style = new Font();
        $this->assertNull($style->color);
        $style->color = 'abc';
        $this->assertSame('FFAABBCC', $style->color);

        $this->expectException(InvalidPropertyValueException::class);
        $this->expectExceptionMessage('Invalid font color value');
        $style->color = 'invalid color';
    }
}
