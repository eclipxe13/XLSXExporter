# eclipxe13/XLSXExporter

[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Scrutinizer][badge-quality]][quality]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]

PHP Office Open XML Spreadsheet (xlsx) Exporter is a project to write xlsx files using PHP.
I recommend you to checkout the project [PHPExcel](https://github.com/PHPOffice/PHPExcel)
that has an excellent support for this kind of files.

I create this project because PHPExcel does not fit my needs.
Specifically, I use this tool to export big amount of data to spreadsheets
files to be exported and processed by the end user.
Using PHPExcel consume a lot of memory and raising the "memory exhausted error".

Projects that does something like this and I use it as reference:

 - https://github.com/PHPOffice/PHPExcel
 - https://github.com/mk-j/PHP_XLSXWriter

## How it works

1. Your main object is a workbook.
1. A workbook contains at least 1 spreadsheet.
1. Every spreadsheet (worksheet) has a collection of columns and a DataProvider object.
1. When the structure information (workbook, worksheets, columns and providers) has been set you can write the xlsx file.
1. Every time a worksheet will be created, the headers are written first, then every row of data is written. The data is extracted using the Provider. In this way, you don't need all your data stored on memory, you can use a PDO reader implementing the Provider interface.
1. The data is written to a temporary files (including the final zip), so no large amount of data is being used.

## Installation

Use [composer](https://getcomposer.org/), run:
 
```shell
composer require eclipxe/xlsxexporter
```

## Basic usage example

```php
<?php
use XLSXExporter\CellTypes;
use XLSXExporter\Column;
use XLSXExporter\Columns;
use XLSXExporter\Providers\ProviderArray;
use XLSXExporter\Style;
use XLSXExporter\Styles\Format;
use XLSXExporter\WorkBook;
use XLSXExporter\WorkSheet;
use XLSXExporter\WorkSheets;
use XLSXExporter\XLSXException;
use XLSXExporter\XLSXExporter;

// create a simple array as example
$provider = new ProviderArray([
    ['fname' => 'Charles', 'amount' => 1234.561, 'visit' => strtotime('2014-01-13 13:14:15'), 'check' => 1],
    ['fname' => 'Foo', 'amount' => 6543.219, 'visit' => strtotime('2014-12-31 23:59:59'), 'check' => 0],
]);

// create some special formats
$formatNumber2Decs = (new Style())->setFromArray(['format' => ['code' => Format::FORMAT_COMMA_2DECS]]);
$formatDateTime = (new Style())->setFromArray(['format' => ['code' => Format::FORMAT_DATE_YMDHM]]);
$formatYesNo = (new Style())->setFromArray(['format' => ['code' => Format::FORMAT_YESNO]]);

// create the workbook with all the information
$workbook = new WorkBook(
    new WorkSheets([
        new WorkSheet(
            'sheet01',
            $provider,
            new Columns([
                new Column('fname', 'Name'),
                new Column('amount', 'Amount', CellTypes::NUMBER, $formatNumber2Decs),
                new Column('visit', 'Visit', CellTypes::DATETIME, $formatDateTime),
                new Column('check', 'Check', CellTypes::BOOLEAN, $formatYesNo),
            ])
        ),
    ])
);

// call the write process
try{
    XLSXExporter::save($workbook, __DIR__ . '/result.xlsx');
} catch (XLSXException $exception) {
    echo 'Export error: ', $exception->getMessage(), PHP_EOL;
}
```

## Contributing

Contributions are welcome! Please read [CONTRIBUTING][] for details
and don't forget to take a look in the [TODO][] and [CHANGELOG][] files.

## License

The eclipxe13/XLSXExporter library is copyright © [Carlos C Soto](https://eclipxe.com.mx/)
and licensed for use under the MIT License (MIT). Please see [LICENSE][] for more information.

[contributing]: https://github.com/eclipxe13/XLSXExporter/blob/master/CONTRIBUTING.md
[changelog]: https://github.com/eclipxe13/XLSXExporter/blob/master/CHANGELOG.md
[todo]: https://github.com/eclipxe13/XLSXExporter/blob/master/TODO.md

[source]: https://github.com/eclipxe13/XLSXExporter
[release]: https://github.com/eclipxe13/XLSXExporter/releases
[license]: https://github.com/eclipxe13/XLSXExporter/blob/master/LICENSE
[build]: https://travis-ci.com/eclipxe13/XLSXExporter?branch=master
[quality]: https://scrutinizer-ci.com/g/eclipxe13/XLSXExporter/
[coverage]: https://scrutinizer-ci.com/g/eclipxe13/XLSXExporter/code-structure/master
[downloads]: https://packagist.org/packages/eclipxe/xlsxexporter

[badge-source]: http://img.shields.io/badge/source-eclipxe13/XLSXExporter-blue.svg?style=flat-square
[badge-release]: https://img.shields.io/github/release/eclipxe13/XLSXExporter.svg?style=flat-square
[badge-license]: https://img.shields.io/github/license/eclipxe13/XLSXExporter.svg?style=flat-square
[badge-build]: https://img.shields.io/travis/com/eclipxe13/XLSXExporter/master.svg?style=flat-square
[badge-quality]: https://img.shields.io/scrutinizer/g/eclipxe13/XLSXExporter/master.svg?style=flat-square
[badge-coverage]: https://img.shields.io/scrutinizer/coverage/g/eclipxe13/XLSXExporter/master.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/eclipxe/xlsxexporter.svg?style=flat-square
