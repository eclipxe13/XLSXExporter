# eclipxe13/XLSXExporter

[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Scrutinizer][badge-quality]][quality]
[![SensioLabsInsight][badge-sensiolabs]][sensiolabs]
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

Use composer, so please run `composer require eclipxe/xlsxexporter` or include this on your `composer.json` file:

```json
{
    "require": {
        "eclipxe/xlsxexporter": "dev-master"
    }
}
```

You can download a copy and include the `autoloader.php` file, it will register the autoloading feature using `spl_autoload_register`
function for the namespace `XLSXExporter\` and include the file only if it exists and is readable.


## Example

```php
// create a simple array as example
$a = new ProviderArray([
    ["fname" => "Charles", "amount" => 1234.561, "visit" => strtotime('2014-01-13 13:14:15'), "check" => 1],
    ["fname" => "Foo", "amount" => 6543.219, "visit" => strtotime('2014-12-31 23:59:59'), "check" => 0],
]);

// create the workbook with all the information
$wb = new WorkBook(new WorkSheets([
    new WorkSheet("sheet01", $a, new Columns([
        new Column("fname", "Name"),
        new Column("amount", "Amount", CellTypes::NUMBER,
            (new Style())->setFromArray(["format" => ["code" => Format::FORMAT_COMMA_2DECS]])),
        new Column("visit", "Visit", CellTypes::DATETIME,
            (new Style())->setFromArray(["format" => ["code" => Format::FORMAT_DATE_YMDHM]])),
        new Column("check", "Check", CellTypes::BOOLEAN,
            (new Style())->setFromArray(["format" => ["code" => Format::FORMAT_YESNO]])),
    ]))
]));

// call the write process
$tempfile = $wb->write();

// copy the file to a certain location
copy($tempfile, "result.xlsx");

// remove temporary file
unlink($tempfile);
```

## Development

I will be using this project for a while, so, I will maintain it and improve it.
In this stage you can consider it as a testing project (even when it's used in production).
I want to do several modifications:

- Follow PSR-2 http://www.php-fig.org/psr/psr-2/
- Better documentation
- Better tests
- Apply Inversion of Control Principle, depending on Interfaces and not on classes and use Factories
- Feel free to contribute to this project!

## TODO

All your help is very appreciated, please contribute with testing, ideas, code, documentation, coffees, etc.

- Add borders style
- Depend on interfaces and not on classes
- Write samples with PDO
- Find a better way to manage SharedStrings without so many memory and fast
- Depend on a class to create the temporary files
- Document all the classes

## License

License MIT - Copyright (c) 2014 - 2016 Carlos Cortés Soto

[source]: https://github.com/eclipxe13/XLSXExporter
[release]: https://github.com/eclipxe13/XLSXExporter/releases
[license]: https://github.com/eclipxe13/XLSXExporter/blob/master/LICENSE
[build]: https://travis-ci.org/eclipxe13/XLSXExporter
[quality]: https://scrutinizer-ci.com/g/eclipxe13/XLSXExporter/
[sensiolabs]: https://insight.sensiolabs.com/projects/4bddd94b-1f59-4e22-8053-b6e98712da50
[coverage]: https://coveralls.io/github/eclipxe13/XLSXExporter?branch=master
[downloads]: https://packagist.org/packages/eclipxe/xlsxexporter

[badge-source]: http://img.shields.io/badge/source-eclipxe13/XLSXExporter-blue.svg?style=flat-square
[badge-release]: https://img.shields.io/github/release/eclipxe13/XLSXExporter.svg?style=flat-square
[badge-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[badge-build]: https://img.shields.io/travis/eclipxe13/XLSXExporter.svg?style=flat-square
[badge-quality]: https://img.shields.io/scrutinizer/g/eclipxe13/XLSXExporter/master.svg?style=flat-square
[badge-sensiolabs]: https://img.shields.io/sensiolabs/i/4bddd94b-1f59-4e22-8053-b6e98712da50.svg?style=flat-square
[badge-coverage]: https://coveralls.io/repos/github/eclipxe13/XLSXExporter/badge.svg?branch=master
[badge-downloads]: https://img.shields.io/packagist/dt/eclipxe/xlsxexporter.svg?style=flat-square
