<?php
namespace XLSXExporterTests\Providers;

use PHPUnit\Framework\TestCase;
use XLSXExporter\ProviderInterface;
use XLSXExporter\Providers\NullProvider;

class NullProviderTest extends TestCase
{
    public function testNullProvider()
    {
        $provider = new NullProvider();
        $this->assertInstanceOf(ProviderInterface::class, $provider);
        // next must not throw any exception
        $provider->next();
        $provider->next();
        // review values
        $this->assertSame(false, $provider->valid());
        $this->assertSame(0, $provider->count());
        $this->assertSame(null, $provider->get('null'));
    }
}
