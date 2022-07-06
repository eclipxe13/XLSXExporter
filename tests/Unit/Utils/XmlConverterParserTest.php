<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests\Unit\Utils;

use Eclipxe\XLSXExporter\Tests\TestCase;
use Eclipxe\XLSXExporter\Utils\XmlConverter;
use XMLWriter;

final class XmlConverterParserTest extends TestCase
{
    public function parseXML(string $text, bool $israw): string
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

    public function testXmlEscaping(): void
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
