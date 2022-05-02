<?php
namespace XLSXExporterTests;

use EngineWorks\DBAL\DBAL;
use EngineWorks\DBAL\Factory as DBALFactory;

class TestUtils
{
    /**
     * @return string
     */
    public static function buildPath()
    {
        $path = __DIR__ . '/../../build';
        if (! is_dir($path)) {
            if (file_exists($path)) {
                trigger_error("Build path $path exists but is not a directory", E_USER_ERROR);
            }
            mkdir($path);
        }
        return realpath($path);
    }

    public static function assetsPath($addToPath = '')
    {
        $path = dirname(__DIR__) . '/assets';
        if (! is_dir($path)) {
            trigger_error("Assets path $path does not exists", E_USER_ERROR);
        }
        return $path . (('' !== $addToPath) ? '/' . $addToPath : '');
    }

    /**
     * @return DBAL
     */
    public static function getDBAL()
    {
        $factory = new DBALFactory('EngineWorks\DBAL\Sqlite');
        $dbal = $factory->dbal($factory->settings([
            'filename' => ':memory:',
        ]));
        if (! $dbal->connect()) {
            trigger_error('Unable to connect to memory sqlite database', E_USER_ERROR);
        }
        $sqlFile = static::assetsPath('employees.sql');
        $instructions = explode(';', file_get_contents($sqlFile));
        foreach ($instructions as $sql) {
            $dbal->execute($sql);
        }
        return $dbal;
    }
}
