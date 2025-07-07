<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Providers;

use Eclipxe\XlsxExporter\ProviderInterface;

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
        // Null pattern, not implemented.
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
