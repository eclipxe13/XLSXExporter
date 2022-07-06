<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests\Unit;

use Countable;
use Eclipxe\XLSXExporter\SharedStrings;
use Eclipxe\XLSXExporter\Tests\TestCase;
use Eclipxe\XLSXExporter\Utils\TemporaryFile;

final class SharedStringsTest extends TestCase
{
    public function testConstructor(): void
    {
        $st = new SharedStrings();
        $this->assertInstanceOf(Countable::class, $st, 'SharedStrings must be a countable');
        $this->assertCount(0, $st, 'Shared Strings must be empty on creation');
    }

    public function testAddMultiple(): SharedStrings
    {
        $samples = [
            ['foo', 0],
            ['bar', 1],
            ['foo', 0],
            ['baz', 2],
        ];
        $st = new SharedStrings();
        foreach ($samples as $sample) {
            $index = $st->add($sample[0]);
            $this->assertSame($sample[1], $index, 'When include a string the expected index does not match');
        }
        $this->assertCount(3, $st, 'Total strings are not 3');
        return $st;
    }

    /**
     * @param SharedStrings $st
     * @depends testAddMultiple
     */
    public function testWrite(SharedStrings $st): void
    {
        $file = new TemporaryFile();
        $filename = $file->getPath();
        $st->write($file);
        $this->assertFileExists($filename, 'The shared strings file was not created');
        $this->assertXmlFileEqualsXmlFile($this->filesPath('sharedstrings-test.xml'), $filename);
    }
}
