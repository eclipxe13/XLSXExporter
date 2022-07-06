<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use ReturnTypeWillChange;
use Traversable;

/**
 * @implements IteratorAggregate<int, WorkSheet>
 */
class WorkSheets implements IteratorAggregate, Countable
{
    /** @var array<int, WorkSheet> */
    private array $items;

    public function __construct(WorkSheet ...$items)
    {
        $this->items = array_values($items);
    }

    /**
     * Add WorkSheet objects to this collection
     */
    public function add(WorkSheet ...$items): void
    {
        $this->items = array_merge($this->items, array_values($items));
    }

    /**
     * Return the repeated worksheet names
     *
     * @return string[]
     */
    public function retrieveRepeatedNames(): array
    {
        $names = [];
        $repeated = [];
        foreach ($this->items as $worksheet) {
            $name = $worksheet->getName();
            if (! in_array($name, $names)) {
                $names[] = $name;
                continue;
            }
            $repeated[] = $name;
        }
        return array_unique($repeated);
    }

    public function isEmpty(): bool
    {
        return [] === $this->items;
    }

    /**
     * Return all the items
     * @return array<int, WorkSheet>
     */
    public function all(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    /** @return Traversable<int, WorkSheet> */
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
}
