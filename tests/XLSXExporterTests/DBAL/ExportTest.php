<?php
namespace XLSXExporterTests\DBAL;

use PHPUnit\Framework\TestCase;
use XLSXExporter\DBAL\WorkBookExporter;
use XLSXExporterTests\TestUtils;

class ExportTest extends TestCase
{
    public function testExportCase()
    {
        $buildFile = TestUtils::buildPath() . '/employees.xlsx';
        if (file_exists($buildFile)) {
            unlink($buildFile);
        }
        $this->assertFileNotExists($buildFile, 'The file to be build is already present');
        $dbal = TestUtils::getDBAL();
        $sql = 'SELECT ' . '* FROM employees;';
        $wbExporter = new WorkBookExporter();
        $wbExporter->attach($dbal->queryRecordset($sql), 'Employees');
        $exported = $wbExporter->getWorkbook()->write();
        copy($exported, $buildFile);
        unlink($exported);
        $this->assertFileExists($buildFile);
    }
}
