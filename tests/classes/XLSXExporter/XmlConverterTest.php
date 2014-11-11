<?php
namespace XLSXExporter;

class XmlConverterTest extends \PHPUnit_Framework_TestCase
{
    public function parseXML($text, $israw)
    {
        $oXMLWriter = new \XMLWriter();
        $oXMLWriter->openMemory();
        $oXMLWriter->startDocument('1.0', 'UTF-8');
        $oXMLWriter->startElement('t');
        if (!$israw) {
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
            "something",
            "something\nstupid",
            "something html < > & ' \"",
            "something unicode á",
            "I am text with Ünicödé & HTML €ntities ©",
        ];
        foreach ($texts as $t) {
            $a = $this->parseXML($t, false);
            $b = $this->parseXML(XmlConverter::specialchars($t), true);
            $this->assertEquals($a, $b);
        }
    }


}
