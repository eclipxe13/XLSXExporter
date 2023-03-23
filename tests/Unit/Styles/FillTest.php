<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Tests\Unit\Styles;

use Eclipxe\XlsxExporter\Exceptions\InvalidPropertyValueException;
use Eclipxe\XlsxExporter\Styles\AbstractStyle;
use Eclipxe\XlsxExporter\Styles\Fill;
use Eclipxe\XlsxExporter\Styles\StyleInterface;
use Eclipxe\XlsxExporter\Tests\TestCase;

final class FillTest extends TestCase
{
    public function testConstructor(): void
    {
        $style = new Fill();
        $this->assertInstanceOf(StyleInterface::class, $style);
        $this->assertInstanceOf(AbstractStyle::class, $style);
        $this->assertNull($style->color);
        $this->assertNull($style->pattern);
    }

    public function testColorProperty(): void
    {
        $style = new Fill();
        $this->assertNull($style->color);
        $style->color = 'abc';
        $this->assertSame('FFAABBCC', $style->color);

        $this->expectException(InvalidPropertyValueException::class);
        $this->expectExceptionMessage('Invalid fill color value');
        $style->color = 'invalid color';
    }

    public function testPatternProperty(): void
    {
        $style = new Fill();
        $this->assertNull($style->pattern);
        $style->pattern = Fill::SOLID;
        $this->assertSame(Fill::SOLID, $style->pattern);

        $this->expectException(InvalidPropertyValueException::class);
        $this->expectExceptionMessage('Invalid fill pattern value');
        $style->pattern = '';
    }

    public function testSetValuesAndExportToXml(): void
    {
        $style = new Fill();
        $style->setValues([
            'color' => 'FFFF0000',
            'pattern' => Fill::SOLID,
        ]);
        $xml = $style->asXML();

        $expectedXml = <<<'XML'
            <fill>
              <patternFill patternType="solid">
                <fgColor rgb="FFFF0000"/>
                <bgColor rgb="FFFF0000"/>
              </patternFill>
            </fill>
            XML;
        $this->assertXmlStringEqualsXmlString($expectedXml, $xml);
    }
}
