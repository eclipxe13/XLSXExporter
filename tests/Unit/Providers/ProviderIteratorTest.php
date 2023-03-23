<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Tests\Unit\Providers;

use ArrayIterator;
use Countable;
use Eclipxe\XlsxExporter\Providers\ProviderIterator;
use Eclipxe\XlsxExporter\Tests\TestCase;
use Iterator;
use IteratorIterator;

final class ProviderIteratorTest extends TestCase
{
    public function testProviderArray(): void
    {
        // create an iterator that is not countable, (ArrayIterator is)
        $noCountableIterator = new IteratorIterator(new ArrayIterator([
            ['a' => 'foo', 'b' => 1],
            ['a' => 'bar', 'b' => 2],
        ]));
        $this->assertNotInstanceOf(Countable::class, $noCountableIterator);
        $this->assertInstanceOf(Iterator::class, $noCountableIterator);
        $provider = new ProviderIterator($noCountableIterator);
        $this->assertEquals(2, $provider->count());

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
