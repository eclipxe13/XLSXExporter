<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Providers;

use Eclipxe\XLSXExporter\ProviderInterface;

/**
 * This is a NullProvider
 */
class NullProvider implements ProviderInterface
{
    public function get(string $key)
    {
        return null;
    }

    public function next(): void
    {
    }

    public function valid(): bool
    {
        return false;
    }

    public function count(): int
    {
        return 0;
    }
}
