<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests\Unit\Utils;

use ArrayAccess;
use ArrayObject;
use Eclipxe\XLSXExporter\Tests\TestCase;
use Eclipxe\XLSXExporter\Utils\ProviderGetValue;
use stdClass;

final class ProviderGetValueTest extends TestCase
{
    /**
     * @return array<int, array{?string, object|array<scalar|null>|ArrayAccess<scalar|null>|null, ?string}>
     */
    public function providerProviderGetValue(): array
    {
        $arrayContainer = [
            'foo' => 'foo content',
            'bar' => 'bar content',
        ];
        $accessArray = new ArrayObject($arrayContainer);
        $accessObject = new stdClass();
        $accessObject->foo = 'foo content';
        $accessObject->bar = 'bar content';
        return [
            ['foo content', $arrayContainer, 'foo'],
            ['bar content', $arrayContainer, 'bar'],
            [null, $arrayContainer, 'BAZ'],
            ['foo content', $accessArray, 'foo'],
            ['bar content', $accessArray, 'bar'],
            [null, $accessArray, 'BAZ'],
            ['foo content', $accessObject, 'foo'],
            ['bar content', $accessObject, 'bar'],
            [null, $accessObject, 'BAZ'],
            [null, null, ''],
        ];
    }

    /**
     * @param object|array<scalar|null>|ArrayAccess<scalar|null>|null $container
     * @dataProvider providerProviderGetValue
     */
    public function testProviderGetValue(?string $expected, $container, string $key): void
    {
        $this->assertSame($expected, ProviderGetValue::get($container, $key));
    }
}
