<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter;

use ArrayIterator;
use Countable;
use Eclipxe\XLSXExporter\Exceptions\ItemNotFoundException;
use IteratorAggregate;
use ReturnTypeWillChange;
use Traversable;

/**
 * @implements IteratorAggregate<int, Column>
 */
class Columns implements IteratorAggregate, Countable
{
    /** @var array<int, Column> */
    private array $items;

    public function __construct(Column ...$items)
    {
        $this->items = array_values($items);
    }

    /**
     * Add Column objects to this collection
     */
    public function add(Column ...$items): void
    {
        $this->items = array_merge($this->items, array_values($items));
    }

    public function exists(int $index): bool
    {
        return array_key_exists($index, $this->items);
    }

    public function existsById(string $id): bool
    {
        return (-1 !== $this->searchById($id));
    }

    public function getById(string $id): Column
    {
        $index = $this->searchById($id);
        if (-1 === $index) {
            throw new ItemNotFoundException("The item with id $id does not exists", $id);
        }
        return $this->get($index);
    }

    public function get(int $index): Column
    {
        if (! isset($this->items[$index])) {
            throw new ItemNotFoundException("The item with index $index does not exists", $index);
        }
        return $this->items[$index];
    }

    /** @return array<int, Column> */
    public function all(): array
    {
        return $this->items;
    }

    public function searchById(string $id): int
    {
        foreach ($this->items as $index => $item) {
            if ($this->elementMatchId($id, $item)) {
                return $index;
            }
        }

        return -1;
    }

    protected function elementMatchId(string $id, Column $item): bool
    {
        return ($id === $item->getId());
    }

    public function count(): int
    {
        return count($this->items);
    }

    /** @return Traversable<int, Column> */
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
}
