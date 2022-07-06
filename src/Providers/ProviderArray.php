<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Providers;

use Eclipxe\XLSXExporter\ProviderInterface;
use Eclipxe\XLSXExporter\Utils\ProviderGetValue;

class ProviderArray implements ProviderInterface
{
    /** @var array<int, array<scalar|null>> */
    private array $dataset;

    private int $index;

    private int $count;

    /** @param array<array<scalar|null>> $dataset */
    public function __construct(array $dataset)
    {
        $this->dataset = array_values($dataset);
        $this->count = count($this->dataset);
        $this->index = 0;
    }

    public function get(string $key)
    {
        if (! $this->valid()) {
            return null;
        }
        return ProviderGetValue::get($this->dataset[$this->index], $key);
    }

    public function next(): void
    {
        $this->index = $this->index + 1;
    }

    public function valid(): bool
    {
        return ($this->index < $this->count);
    }

    public function count(): int
    {
        return $this->count;
    }
}
