<?php
namespace classes\XLSXExporterTests\Utils;

use PHPUnit\Framework\TestCase;
use XLSXExporter\Utils\XmlConverter;
use XMLWriter;

class XmlConverterParserTest extends TestCase
{
    public function parseXML($text, $israw)
    {
        $oXMLWriter = new XMLWriter();
        $oXMLWriter->openMemory();
        $oXMLWriter->startDocument('1.0', 'UTF-8');
        $oXMLWriter->startElement('t');
        if (! $israw) {
            $oXMLWriter->text($text);
        } else {
            $oXMLWriter->writeRaw($text);
        }
        $oXMLWriter->endElement();
        $oXMLWriter->endDocument();
        return $oXMLWriter->outputMemory(true);
    }

    public function testXmlEscaping()
    {
        $texts = [
            'something',
            "something\nstupid",
            "something html < > & ' \"",
            'something unicode á',
            'I am text with Ünicödé & HTML €ntities ©',
        ];
        foreach ($texts as $t) {
            $expected = $this->parseXML($t, false);
            $received = $this->parseXML(XmlConverter::parse($t), true);
            $this->assertEquals($expected, $received);
        }
    }
}
