<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests\Unit;

use Eclipxe\XLSXExporter\Exceptions\InvalidWorkSheetNameException;
use Eclipxe\XLSXExporter\Tests\TestCase;
use Eclipxe\XLSXExporter\WorkSheet;

final class WorkSheetTest extends TestCase
{
    private WorkSheet $worksheet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheet = new WorkSheet('Sheet1');
    }

    public function testWorkSheetGetName(): void
    {
        $this->assertSame('Sheet1', $this->worksheet->getName());
        $this->assertSame('Sheet1', $this->worksheet->name);
    }

    public function testWorkSheetSetName(): void
    {
        $this->worksheet->setName('foo');
        $this->assertSame('foo', $this->worksheet->getName());
    }

    public function testWorkSheetSetNameWithEmptyString(): void
    {
        $this->expectException(InvalidWorkSheetNameException::class);
        $this->expectExceptionMessage('the name is empty');
        $this->worksheet->setName('');
    }

    public function testWorkSheetSetNameWithLongString(): void
    {
        $expected = str_repeat('x', 31);
        $this->worksheet->setName($expected);
        $this->assertSame($expected, $this->worksheet->getName());
        $this->expectException(InvalidWorkSheetNameException::class);
        $this->expectExceptionMessage('the name length is more than 31 chars length');
        $this->worksheet->setName(str_repeat('x', 32));
    }

    /**
     * @return array<int, array<string>>
     */
    public function providerWorkSheetSetNameWithInvalidString(): array
    {
        return [[':'], ['/'], ['\\'], ['?'], ['*'], ['['], [']'], ["'"], ["\t"], ["\r"], ["\n"], ["\0"]];
    }

    /**
     * @dataProvider providerWorkSheetSetNameWithInvalidString
     */
    public function testWorkSheetSetNameWithInvalidString(string $name): void
    {
        $this->expectException(InvalidWorkSheetNameException::class);
        $this->expectExceptionMessage('the name contains invalid chars');
        $this->worksheet->setName($name);
    }
}
