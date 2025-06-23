<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Tests\Integration;

use ArrayIterator;
use Eclipxe\XlsxExporter\CellTypes;
use Eclipxe\XlsxExporter\Column;
use Eclipxe\XlsxExporter\Columns;
use Eclipxe\XlsxExporter\Providers\NullProvider;
use Eclipxe\XlsxExporter\Providers\ProviderArray;
use Eclipxe\XlsxExporter\Providers\ProviderIterator;
use Eclipxe\XlsxExporter\Style;
use Eclipxe\XlsxExporter\Styles\Alignment;
use Eclipxe\XlsxExporter\Styles\Format;
use Eclipxe\XlsxExporter\Tests\TestCase;
use Eclipxe\XlsxExporter\WorkBook;
use Eclipxe\XlsxExporter\WorkSheet;
use Eclipxe\XlsxExporter\WorkSheets;
use Eclipxe\XlsxExporter\XlsxExporter;
use ZipArchive;

final class CompleteTest extends TestCase
{
    public function testComplete(): void
    {
        // The provider
        $dataArray = [
            [
                'fname' => 'Charles',
                'amount' => 1234.561,
                'visit' => strtotime('2014-01-13 13:14:15'),
                'check' => 1,
                'rate' => 1,
            ],
            [
                'fname' => 'Foo',
                'amount' => 6543.219,
                'visit' => strtotime('2014-12-31 23:59:59'),
                'check' => true,
                'rate' => 1.9876543,
            ],
            [
                'fname' => 'Derp',
                'amount' => -999,
                'visit' => null,
                'check' => false,
                'rate' => 1.2345,
            ],
        ];
        $providerArray = new ProviderArray($dataArray);
        $providerIterator = new ProviderIterator(new ArrayIterator($dataArray));
        $providerNull = new NullProvider();
        // The workbook and columns
        $columns = new Columns(
            new Column('fname', 'Name'),
            new Column(
                'amount',
                'Amount',
                CellTypes::NUMBER,
                new Style([
                    'format' => ['code' => Format::FORMAT_COMMA_2DECS],
                    'font' => ['bold' => 1],
                ])
            ),
            new Column(
                'rate',
                'Rate',
                CellTypes::NUMBER,
                new Style([
                    'format' => ['code' => '0.0000'],
                ])
            ),
            new Column(
                'visit',
                'Visit',
                CellTypes::DATETIME,
                new Style([
                    'format' => ['code' => Format::FORMAT_DATE_YMDHM],
                    'protection' => ['hidden' => 1, 'locked' => 1],
                ])
            ),
            new Column(
                'check',
                'Check',
                CellTypes::BOOLEAN,
                new Style([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ])
            ),
        );
        $wb = new WorkBook(new WorkSheets(
            new WorkSheet('first', $providerArray, $columns),
            new WorkSheet('second', $providerIterator, $columns),
            new WorkSheet('empty', $providerNull, $columns),
        ));

        $exportedFilePath = $this->buildPath('complete-test.xlsx');
        if (file_exists($exportedFilePath)) {
            unlink($exportedFilePath);
        }

        $exporter = new XlsxExporter();
        $exporter->save($wb, $exportedFilePath);
        $this->assertFileExists($exportedFilePath);

        // test zip file has contents
        $zipFile = new ZipArchive();
        $this->assertSame(true, $zipFile->open($exportedFilePath), 'Can open exported file as ZIP');
        $this->assertNotSame(false, $zipFile->locateName('_rels/.rels'));
        $this->assertNotSame(false, $zipFile->locateName('[Content_Types].xml'));
        $this->assertNotSame(false, $zipFile->locateName('xl/worksheets/sheet3.xml'));
        $this->assertNotSame(false, $zipFile->locateName('xl/worksheets/sheet2.xml'));
        $this->assertNotSame(false, $zipFile->locateName('xl/worksheets/sheet1.xml'));
        $this->assertNotSame(false, $zipFile->locateName('xl/_rels/workbook.xml.rels'));
        $this->assertNotSame(false, $zipFile->locateName('xl/workbook.xml'));
        $this->assertNotSame(false, $zipFile->locateName('xl/styles.xml'));
        $this->assertNotSame(false, $zipFile->locateName('xl/sharedStrings.xml'));
    }
}
