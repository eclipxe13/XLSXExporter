<?php
namespace XLSXExporterTests\DBAL;

use PHPUnit\Framework\TestCase;
use XLSXExporter\DBAL\ResultProvider;
use XLSXExporter\ProviderInterface;
use XLSXExporterTests\TestUtils;

class ResultProviderTest extends TestCase
{
    public function testProviderImplementation()
    {
        $dbal = TestUtils::getDBAL();
        $sql = 'SELECT ' . 'EmployeeId, FirstName, LastName FROM employees WHERE ReportsTo = 2 ORDER BY EmployeeId;';
        $result = $dbal->queryResult($sql);
        $provider = new ResultProvider($result);

        $this->assertInstanceOf(ProviderInterface::class, $provider);
        $this->assertTrue($provider->valid());
        $this->assertSame(3, $provider->count());
        $expectedResults = [
            ['EmployeeId' => 3, 'FirstName' => 'Jane', 'LastName' => 'Peacock', 'Null' => null],
            ['EmployeeId' => 4, 'FirstName' => 'Margaret', 'LastName' => 'Park', 'Null' => null],
            ['EmployeeId' => 5, 'FirstName' => 'Steve', 'LastName' => 'Johnson', 'Null' => null],
        ];
        $retrieved = [];
        while ($provider->valid()) {
            $retrieved[] = [
                'EmployeeId' => $provider->get('EmployeeId'),
                'FirstName' => $provider->get('FirstName'),
                'LastName' => $provider->get('LastName'),
                'Null' => $provider->get('Null'),
            ];
            $provider->next();
        }
        $this->assertEquals($expectedResults, $retrieved);
    }
}
