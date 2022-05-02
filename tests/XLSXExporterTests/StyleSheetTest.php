<?php
namespace XLSXExporterTests;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use XLSXExporter\Style;
use XLSXExporter\Styles\Format;
use XLSXExporter\StyleSheet;
use XLSXExporter\XLSXException;

class StyleSheetTest extends TestCase
{
    public function testConstructWithOutStyles()
    {
        $this->expectException(XLSXException::class);
        new StyleSheet([]);
    }

    public function testConstructWithInvalidStyles()
    {
        $this->expectException(XLSXException::class);
        /** @var Style $notValidStyle */
        $notValidStyle = new \stdClass();
        new StyleSheet([$notValidStyle]);
    }

    public function testConstructWithGeneralStyle()
    {
        $general = new Style([
            'format' => ['code' => Format::FORMAT_GENERAL],
        ]);
        $ss = new StyleSheet([$general]);
        $numFmts = $this->extractNumFmts($ss->asXML());
        $this->assertEquals(1, (int) $numFmts['count']);
        $this->assertEquals(1, count($numFmts->numFmt));
        $this->compareFmt('0', 'General', $numFmts->numFmt[0]);
    }

    public function testConstructWithMultipleStyles()
    {
        $ss = new StyleSheet([
            new Style(['format' => ['code' => Format::FORMAT_COMMA_0DECS]]),
            new Style(['format' => ['code' => '0.0000']]),
            new Style(['format' => ['code' => Format::FORMAT_NUMBER]]),
            new Style(['format' => ['code' => Format::FORMAT_NUMBER_00]]),
            new Style(['format' => ['code' => '0.0000%']]),
        ]);
        $numFmts = $this->extractNumFmts($ss->asXML());
        $this->assertEquals(6, (int) $numFmts['count']);
        $this->assertEquals(6, count($numFmts->numFmt));
        $this->compareFmt('0', Format::FORMAT_GENERAL, $numFmts->numFmt[0]);
        $this->compareFmt('3', Format::FORMAT_COMMA_0DECS, $numFmts->numFmt[1]);
        $this->compareFmt('164', '0.0000', $numFmts->numFmt[2]);
        $this->compareFmt('1', Format::FORMAT_NUMBER, $numFmts->numFmt[3]);
        $this->compareFmt('2', Format::FORMAT_NUMBER_00, $numFmts->numFmt[4]);
        $this->compareFmt('165', '0.0000%', $numFmts->numFmt[5]);
    }

    private function compareFmt($numFmtId, $formatCode, SimpleXMLElement $node)
    {
        $this->assertEquals(
            $numFmtId,
            (string) $node['numFmtId'],
            "Compare format [$numFmtId, $formatCode] fail on numFmtId"
        );
        $this->assertEquals(
            $formatCode,
            (string) $node['formatCode'],
            "Compare format [$numFmtId, $formatCode] fail on formatCode"
        );
    }

    private function extractNumFmts($xmlContent)
    {
        $xml = new SimpleXMLElement($xmlContent);
        if (isset($xml->numFmts)) {
            return $xml->numFmts;
        }
        throw new \RuntimeException('The style sheet does not have numFmts node');
    }
}
