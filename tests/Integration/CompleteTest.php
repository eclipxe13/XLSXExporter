<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests\Integration;

use ArrayIterator;
use Eclipxe\XLSXExporter\CellTypes;
use Eclipxe\XLSXExporter\Column;
use Eclipxe\XLSXExporter\Columns;
use Eclipxe\XLSXExporter\Providers\NullProvider;
use Eclipxe\XLSXExporter\Providers\ProviderArray;
use Eclipxe\XLSXExporter\Providers\ProviderIterator;
use Eclipxe\XLSXExporter\Style;
use Eclipxe\XLSXExporter\Styles\Alignment;
use Eclipxe\XLSXExporter\Styles\Format;
use Eclipxe\XLSXExporter\Tests\TestCase;
use Eclipxe\XLSXExporter\WorkBook;
use Eclipxe\XLSXExporter\WorkSheet;
use Eclipxe\XLSXExporter\WorkSheets;
use Eclipxe\XLSXExporter\XLSXExporter;
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

        $exporter = new XLSXExporter();
        $exporter->save($wb, $exportedFilePath);
        $this->assertFileExists($exportedFilePath);

        // test zip file has contents
        $zipFile = new ZipArchive();
        $this->assertSame(true, $zipFile->open($exportedFilePath), 'Can open exported file as ZIP');
        $this->assertTrue(false !== $zipFile->locateName('_rels/.rels'));
        $this->assertTrue(false !== $zipFile->locateName('[Content_Types].xml'));
        $this->assertTrue(false !== $zipFile->locateName('xl/worksheets/sheet3.xml'));
        $this->assertTrue(false !== $zipFile->locateName('xl/worksheets/sheet2.xml'));
        $this->assertTrue(false !== $zipFile->locateName('xl/worksheets/sheet1.xml'));
        $this->assertTrue(false !== $zipFile->locateName('xl/_rels/workbook.xml.rels'));
        $this->assertTrue(false !== $zipFile->locateName('xl/workbook.xml'));
        $this->assertTrue(false !== $zipFile->locateName('xl/styles.xml'));
        $this->assertTrue(false !== $zipFile->locateName('xl/sharedStrings.xml'));
    }
}
