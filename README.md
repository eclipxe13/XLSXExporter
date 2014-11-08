XLSXExporter
============
PHP Office Open XML Spreadsheet (xlsx) Exporter is a project to write xlsx files using PHP.
I recommend you to checkout the project [PHPExcel](https://github.com/PHPOffice/PHPExcel) that has an excellent support for this kind of files.

I create this project because PHPExcel does not fit my needs. Specifically, I use this tool to export big amount of data to spreadsheets files to be worked and processed by the end user. Using PHPExcel consume a lot of memory and raising the "memory exhausted error".

Other projects that does something like this and help me in the process:
 - https://github.com/PHPOffice/PHPExcel
 - https://github.com/mk-j/PHP_XLSXWriter


How it works
----------
Your main object is a workbook.
A workbook can contain 1 to several spreadsheets.
Every worksheet has a collection of columns and a DataProvider object.
When the structure information (workbook, sheets, columns and providers) has been set you can write the xlsx file.
Every time a worksheet will be created, the headers are wroten first, then every row of data is written. The data is extracted using the Provider. In this way, you don't need all your data stored on memory, you can use a PDO reader implementing the Provider interface. It also write the data directly to a temporary file, so no memory is being used.

Example
-------
    <?php
    // create a simple array as example
    $a = new ProviderArray([
        ["fname" => "Charles", "amount" => 1234.561, "visit" => strtotime('2014-01-13 13:14:15'), "check" => 1],
        ["fname" => "Foo", "amount" => 6543.219, "visit" => strtotime('2014-12-31 23:59:59'), "check" => 0],
    ]);
    // create the workbook with all the information
    $wb = new WorkBook(new WorkSheets([
        new WorkSheet("sheet01", $a, new Columns([
            new Column("fname", "Name"),
            new Column("amount", "Amount", CellTypes::NUMBER, BasicStyles::withStdFormat(Styles\Format::FORMAT_ZERO_2DECS)),
            new Column("visit", "Visit", CellTypes::DATETIME, BasicStyles::withStdFormat(Styles\Format::FORMAT_DATE_YMDHM)),
            new Column("check", "Check", CellTypes::NUMBER, BasicStyles::withStdFormat(Styles\Format::FORMAT_YESNO)),
        ]))
    ]));
    // call the write process
    $tempfile = $wb->write();
    // copy the file to a certain location
    $this->assertTrue(copy($tempfile, "result.xlsx"));
    // remove temporary file
    unlink($tempfile);


Development
----------
I will be using this project for a while, so, I will maintain and improve it a lot. In this stage you can consider it as a testing project (even when it's used in production).
I want to do several modifications:
- Make a better documentation
- Make better tests 
- Apply Inversion of Control Principle, depending on Interfaces and not on classes and use Factories
Feel free to contribute to this project!

License
----------
Consider this project under MIT License.
