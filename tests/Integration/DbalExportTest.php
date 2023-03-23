<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Tests\Integration;

use Eclipxe\XlsxExporter\DBAL\WorkBookExporter;
use Eclipxe\XlsxExporter\Tests\TestCase;
use Eclipxe\XlsxExporter\Utils\TemporaryFile;

final class DbalExportTest extends TestCase
{
    public function testExportCase(): void
    {
        $buildFile = $this->buildPath('employees.xlsx');
        if (file_exists($buildFile)) {
            unlink($buildFile);
        }
        $this->assertFileDoesNotExist($buildFile, 'The file to be build is already present');
        $dbal = $this->getDBAL();
        $sql = /** @lang text */ 'SELECT * FROM employees;';
        $wbExporter = new WorkBookExporter();
        $recordset = $dbal->createRecordset($sql);
        $this->assertGreaterThan(0, $recordset->count(), 'It should exists some data to export');
        $wbExporter->attach($recordset, 'Employees');
        $exported = new TemporaryFile();
        $wbExporter->getWorkbook()->write($exported);
        $exported->copy($buildFile);
        $this->assertFileExists($buildFile);
    }
}
