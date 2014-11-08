<?php

namespace XLSXExporter;

class WorkSheetWriterTest extends \PHPUnit_Framework_TestCase
{

    public function testStaticIntegerToColumn()
    {
        $a = [
            1 => "A",
            2 => "B",
            9 => "I",
            10 => "J",
            19 => "S",
            20 => "T",
            26 => "Z",
            27 => "AA",
            52 => "AZ",
            53 => "BA",
            1024 => "AMJ"
        ];
        foreach ($a as $n => $v) {
            $this->assertEquals($v, WorkSheetWriter::colByNumber($n));
        }
    }

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
            $b = $this->parseXML(WorkSheetWriter::xml($t), true);
            $this->assertEquals($a, $b);
        }
    }

}