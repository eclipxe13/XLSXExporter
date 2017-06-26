<?php
namespace XLSXExporterTests\Providers;

use PHPUnit\Framework\TestCase;
use XLSXExporter\Providers\ProviderIterator;

class ProviderIteratorTest extends TestCase
{
    public function testProviderArray()
    {
        // create an iterator that is not countable, (ArrayIterator is)
        $noCountableIterator = new \IteratorIterator(new \ArrayIterator([
            ['a' => 'foo', 'b' => 1],
            ['a' => 'bar', 'b' => 2],
        ]));
        $this->assertNotInstanceOf(\Countable::class, $noCountableIterator);
        $this->assertInstanceOf(\Iterator::class, $noCountableIterator);
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
