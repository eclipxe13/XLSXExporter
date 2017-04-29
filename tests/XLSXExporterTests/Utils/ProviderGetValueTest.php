<?php
namespace XLSXExporterTests\Utils;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use XLSXExporter\Utils\ProviderGetValue;

class ProviderGetValueTest extends TestCase
{
    public function providerProviderGetValue()
    {
        $arrayContainer = [
            'foo' => 'foo content',
            'bar' => 'bar content',
        ];
        $accessArray = new ArrayObject($arrayContainer);
        $accessObject = new \stdClass();
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
            [null, null, null],
        ];
    }

    /**
     * @param $expected
     * @param $container
     * @param $key
     * @dataProvider providerProviderGetValue
     */
    public function testProviderGetValue($expected, $container, $key)
    {
        $this->assertSame($expected, ProviderGetValue::get($container, $key));
    }
}
