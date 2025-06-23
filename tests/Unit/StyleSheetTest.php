<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Tests\Unit;

use Eclipxe\XlsxExporter\Style;
use Eclipxe\XlsxExporter\Styles\Format;
use Eclipxe\XlsxExporter\StyleSheet;
use Eclipxe\XlsxExporter\Tests\TestCase;
use LogicException;
use RuntimeException;
use SimpleXMLElement;

final class StyleSheetTest extends TestCase
{
    public function testConstructWithOutStyles(): void
    {
        $this->expectException(LogicException::class);
        new StyleSheet();
    }

    public function testConstructWithGeneralStyle(): void
    {
        $general = new Style([
            'format' => ['code' => Format::FORMAT_GENERAL],
        ]);
        $ss = new StyleSheet($general);
        $numFmts = $this->extractNumFmts($ss->asXML());
        $this->assertEquals(1, (int) $numFmts['count']);
        $this->assertCount(1, $numFmts->numFmt);
        $this->compareFmt('0', 'General', $numFmts->numFmt[0]);
    }

    public function testConstructWithMultipleStyles(): void
    {
        $ss = new StyleSheet(
            new Style(['format' => ['code' => Format::FORMAT_COMMA_0DECS]]),
            new Style(['format' => ['code' => '0.0000']]),
            new Style(['format' => ['code' => Format::FORMAT_NUMBER]]),
            new Style(['format' => ['code' => Format::FORMAT_NUMBER_00]]),
            new Style(['format' => ['code' => '0.0000%']]),
        );
        $numFmts = $this->extractNumFmts($ss->asXML());
        $this->assertEquals(6, (int) $numFmts['count']);
        $this->assertCount(6, $numFmts->numFmt);
        $this->compareFmt('0', Format::FORMAT_GENERAL, $numFmts->numFmt[0]);
        $this->compareFmt('3', Format::FORMAT_COMMA_0DECS, $numFmts->numFmt[1]);
        $this->compareFmt('164', '0.0000', $numFmts->numFmt[2]);
        $this->compareFmt('1', Format::FORMAT_NUMBER, $numFmts->numFmt[3]);
        $this->compareFmt('2', Format::FORMAT_NUMBER_00, $numFmts->numFmt[4]);
        $this->compareFmt('165', '0.0000%', $numFmts->numFmt[5]);
    }

    /** @param mixed $node */
    private function compareFmt(string $numFmtId, string $formatCode, $node): void
    {
        if (! $node instanceof SimpleXMLElement) {
            $this->fail('Call to compareFmt $node should be an instance of SimpleXMLElement.');
        }
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

    private function extractNumFmts(string $xmlContent): SimpleXMLElement
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $xml = new SimpleXMLElement($xmlContent);
        if (isset($xml->numFmts)) {
            return $xml->numFmts;
        }
        throw new RuntimeException('The style sheet does not have numFmts node');
    }
}
