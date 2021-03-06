<?php
namespace XLSXExporterTests;

use PHPUnit\Framework\TestCase;
use XLSXExporter\CellTypes;
use XLSXExporter\Column;
use XLSXExporter\Columns;
use XLSXExporter\Providers\NullProvider;
use XLSXExporter\Providers\ProviderArray;
use XLSXExporter\Providers\ProviderIterator;
use XLSXExporter\Style;
use XLSXExporter\Styles\Alignment;
use XLSXExporter\Styles\Format;
use XLSXExporter\WorkBook;
use XLSXExporter\WorkSheet;
use XLSXExporter\WorkSheets;

class CompleteTest extends TestCase
{
    public function testComplete()
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
        $providerIterator = new ProviderIterator(new \ArrayIterator($dataArray));
        $providerNull = new NullProvider();
        // The workbook and columns
        $columns = new Columns([
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
                    'alignment' => Alignment::HORIZONTAL_CENTER,
                ])
            ),
        ]);
        $wb = new WorkBook(new WorkSheets([
            new WorkSheet('first', $providerArray, $columns),
            new WorkSheet('second', $providerIterator, $columns),
            new WorkSheet('empty', $providerNull, $columns),
        ]));
        // write to a temporary file
        $tempfile = $wb->write();
        $this->assertFileExists($tempfile);
        // copy the file to a certain location
        $build = TestUtils::buildPath();
        if ('' !== $build) {
            copy($tempfile, $build . '/complete-test.xlsx');
        }
        // remove temporary file
        unlink($tempfile);
    }
}
