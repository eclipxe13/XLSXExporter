<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests\Unit\Styles;

use Eclipxe\XLSXExporter\Exceptions\InvalidPropertyValueException;
use Eclipxe\XLSXExporter\Styles\Alignment;
use Eclipxe\XLSXExporter\Styles\StyleInterface;
use Eclipxe\XLSXExporter\Tests\TestCase;

final class AlignmentTest extends TestCase
{
    public function testConstructor(): void
    {
        $style = new Alignment();
        $this->assertInstanceOf(StyleInterface::class, $style);
        $this->assertNull($style->horizontal);
        $this->assertNull($style->vertical);
        $this->assertNull($style->wraptext);
    }

    public function testHorizontalProperty(): void
    {
        $style = new Alignment();
        $this->assertNull($style->horizontal);
        $style->horizontal = Alignment::HORIZONTAL_CENTER;
        $this->assertSame(Alignment::HORIZONTAL_CENTER, $style->horizontal);

        $this->expectException(InvalidPropertyValueException::class);
        $this->expectExceptionMessage('Invalid alignment horizontal value');
        $style->horizontal = Alignment::VERTICAL_BOTTOM;
    }

    public function testVerticalProperty(): void
    {
        $style = new Alignment();
        $this->assertNull($style->vertical);
        $style->vertical = Alignment::VERTICAL_BOTTOM;
        $this->assertSame(Alignment::VERTICAL_BOTTOM, $style->vertical);

        $this->expectException(InvalidPropertyValueException::class);
        $this->expectExceptionMessage('Invalid alignment vertical value');
        $style->vertical = Alignment::HORIZONTAL_JUSTIFY;
    }

    public function testWraptextProperty(): void
    {
        $style = new Alignment();
        $this->assertNull($style->wraptext);
        $style->wraptext = true;
        $this->assertSame(true, $style->wraptext);
    }

    public function testSetValuesAndExportToXml(): void
    {
        $style = new Alignment();
        $style->setValues([
            'horizontal' => Alignment::HORIZONTAL_RIGHT,
            'vertical' => Alignment::VERTICAL_BOTTOM,
            'wraptext' => true,
        ]);
        $xml = $style->asXML();

        $expectedXml = '<alignment horizontal="right" vertical="bottom" wrapText="1" />';
        $this->assertXmlStringEqualsXmlString($expectedXml, $xml);
    }
}
