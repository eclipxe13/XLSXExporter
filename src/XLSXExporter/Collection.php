<?php
namespace XLSXExporter;

use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * Class Collection
 * @internal
 * @template TValue
 * @implements IteratorAggregate<TValue>
 */
abstract class Collection implements IteratorAggregate, Countable
{
    /** @var array<int, TValue> */
    protected $collection = [];

    public function __construct(array $items = [])
    {
        $this->addArray($items);
    }

    /**
     * @param TValue $item
     * @return void
     * @throws XLSXException if $item does not have correct type
     */
    abstract public function add($item);

    /**
     * Retrieve if an element match, this is used in searchById function
     *
     * @param string $id
     * @param TValue $item
     * @return bool
     */
    abstract protected function elementMatchId($id, $item);

    /**
     * Get the index in the collection for an element
     *
     * @param string $id
     * @return int index of the element, -1 if not found
     */
    public function searchById($id)
    {
        foreach ($this->collection as $index => $item) {
            if ($this->elementMatchId($id, $item)) {
                return $index;
            }
        }

        return -1;
    }

    /**
     * Call the add method with the contents of the item array
     * @param array $items
     */
    public function addArray(array $items)
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * Check if a item id exists
     * @param int $index
     * @return bool
     */
    public function exists($index)
    {
        return (array_key_exists($index, $this->collection));
    }

    /**
     * Return if the item identified by id exists
     *
     * @param string $id
     * @return bool
     */
    public function existsById($id)
    {
        return (-1 !== $this->searchById($id));
    }

    /**
     * Return one item by index
     *
     * @param int $index
     * @return TValue
     * @throws XLSXException
     */
    public function get($index)
    {
        if (! $this->exists($index)) {
            throw new XLSXException("The item $index does not exists");
        }
        return $this->collection[$index];
    }

    /**
     * Return one item by id
     *
     * @param string $id
     * @return TValue
     * @throws XLSXException
     */
    public function getById($id)
    {
        if (-1 === $index = $this->searchById($id)) {
            throw new XLSXException("The item {$id} does not exists");
        }
        return $this->get($index);
    }

    /**
     * Number of items
     * @return int
     */
    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * Return all the items
     * @return array<TValue>
     */
    public function all(): array
    {
        return $this->collection;
    }

    /**
     * @return ArrayIterator<TValue>
     */
    public function getIterator()
    {
        return new ArrayIterator($this->collection);
    }
}
