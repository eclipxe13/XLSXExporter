<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests;

use EngineWorks\DBAL\DBAL;
use EngineWorks\DBAL\Factory as DBALFactory;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function buildPath(string $addToPath = ''): string
    {
        return __DIR__ . '/../build/' . $addToPath;
    }

    public static function filesPath(string $addToPath = ''): string
    {
        return __DIR__ . '/_files/' . $addToPath;
    }

    public static function getDBAL(): DBAL
    {
        $factory = new DBALFactory('\EngineWorks\DBAL\Sqlite');
        $dbal = $factory->dbal($factory->settings([
            'filename' => ':memory:',
        ]));
        if (! $dbal->connect()) {
            trigger_error('Unable to connect to memory sqlite database', E_USER_ERROR);
        }
        $sqlFile = self::filesPath('employees.sql');
        $instructions = explode(';', (string) file_get_contents($sqlFile));
        foreach ($instructions as $sql) {
            $dbal->execute($sql);
        }
        return $dbal;
    }
}
