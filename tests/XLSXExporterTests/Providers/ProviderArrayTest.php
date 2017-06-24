<?php
namespace XLSXExporterTests\Providers;

use PHPUnit\Framework\TestCase;
use XLSXExporter\Providers\ProviderArray;

class ProviderArrayTest extends TestCase
{
    public function testProviderArray()
    {
        $provider = new ProviderArray([
            ['a' => 'foo', 'b' => 1],
            ['a' => 'bar', 'b' => 2],
        ]);

        $this->assertTrue($provider->valid());
        $this->assertEquals('foo', $provider->get('a'));
        $this->assertEquals(1, $provider->get('b'));
        $provider->next();
        $this->assertTrue($provider->valid());
        $this->assertEquals('bar', $provider->get('a'));
        $this->assertEquals(2, $provider->get('b'));
        $provider->next();
        $this->assertFalse($provider->valid());
    }

    public function testProviderArrayWithKeys()
    {
        $provider = new ProviderArray([
            99 => ['a' => 'foo', 'b' => 1],
            'x' => ['a' => 'bar', 'b' => 2],
        ]);

        $this->assertTrue($provider->valid());
        $this->assertEquals('foo', $provider->get('a'));
        $this->assertEquals(1, $provider->get('b'));
        $provider->next();
        $this->assertTrue($provider->valid());
        $this->assertEquals('bar', $provider->get('a'));
        $this->assertEquals(2, $provider->get('b'));
        $provider->next();
        $this->assertFalse($provider->valid());
    }
}
