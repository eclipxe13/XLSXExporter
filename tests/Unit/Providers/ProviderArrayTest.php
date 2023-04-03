<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Tests\Unit\Providers;

use Eclipxe\XlsxExporter\Providers\ProviderArray;
use Eclipxe\XlsxExporter\Tests\TestCase;

final class ProviderArrayTest extends TestCase
{
    public function testProviderArray(): void
    {
        $provider = new ProviderArray([
            ['a' => 'foo', 'b' => 1],
            ['a' => 'bar', 'b' => 2],
        ]);

        // simulate loop in iterator
        $this->assertTrue($provider->valid());
        $this->assertEquals('foo', $provider->get('a'));
        $this->assertEquals(1, $provider->get('b'));
        $provider->next();
        $this->assertTrue($provider->valid());
        $this->assertEquals('bar', $provider->get('a'));
        $this->assertEquals(2, $provider->get('b'));
        $provider->next();
        $this->assertFalse($provider->valid());

        // get returns null when not valid
        $this->assertNull($provider->get('a'));
    }

    public function testProviderArrayWithKeys(): void
    {
        $provider = new ProviderArray([
            99 => ['a' => 'foo', 'b' => 1],
            'x' => ['a' => 'bar', 'b' => 2],
        ]);

        // simulate loop in iterator
        $this->assertTrue($provider->valid());
        $this->assertEquals('foo', $provider->get('a'));
        $this->assertEquals(1, $provider->get('b'));
        $provider->next();
        $this->assertTrue($provider->valid());
        $this->assertEquals('bar', $provider->get('a'));
        $this->assertEquals(2, $provider->get('b'));
        $provider->next();
        $this->assertFalse($provider->valid());

        // get returns null when not valid
        $this->assertNull($provider->get('a'));
    }
}
