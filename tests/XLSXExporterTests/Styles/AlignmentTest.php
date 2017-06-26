<?php
namespace XLSXExporterTests\Styles;

use PHPUnit\Framework\TestCase;
use XLSXExporter\Styles\Alignment;
use XLSXExporter\Styles\StyleInterface;
use XLSXExporter\XLSXException;

class AlignmentTest extends TestCase
{
    public function testConstructor()
    {
        $style = new Alignment();
        $this->assertInstanceOf(StyleInterface::class, $style);
        $this->assertNull($style->horizontal);
        $this->assertNull($style->vertical);
        $this->assertNull($style->wraptext);
    }

    public function testHorizontalProperty()
    {
        $style = new Alignment();
        $this->assertNull($style->horizontal);
        $style->horizontal = Alignment::HORIZONTAL_CENTER;
        $this->assertEquals(Alignment::HORIZONTAL_CENTER, $style->horizontal);

        $this->expectException(XLSXException::class);
        $style->horizontal = Alignment::VERTICAL_BOTTOM;
    }

    public function testVerticalProperty()
    {
        $style = new Alignment();
        $this->assertNull($style->vertical);
        $style->vertical = Alignment::VERTICAL_BOTTOM;
        $this->assertEquals(Alignment::VERTICAL_BOTTOM, $style->vertical);

        $this->expectException(XLSXException::class);
        $style->vertical = Alignment::HORIZONTAL_JUSTIFY;
    }

    public function testWraptextProperty()
    {
        $style = new Alignment();
        $this->assertNull($style->wraptext);
        $style->wraptext = true;
        $this->assertEquals(true, $style->wraptext);
    }

    public function testSetValuesAndExportToXml()
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
