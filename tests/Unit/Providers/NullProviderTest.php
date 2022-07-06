<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests\Unit\Providers;

use Eclipxe\XLSXExporter\ProviderInterface;
use Eclipxe\XLSXExporter\Providers\NullProvider;
use Eclipxe\XLSXExporter\Tests\TestCase;

final class NullProviderTest extends TestCase
{
    public function testNullProvider(): void
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
