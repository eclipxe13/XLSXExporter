<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests\Integration;

use Eclipxe\XLSXExporter\DBAL\WorkBookExporter;
use Eclipxe\XLSXExporter\Tests\TestCase;
use Eclipxe\XLSXExporter\Utils\TemporaryFile;

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
