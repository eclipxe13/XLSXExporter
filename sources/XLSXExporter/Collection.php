<?php

namespace XLSXExporter;

abstract class Collection implements \IteratorAggregate, \Countable
{
    /**
     * @param mixed $item
     * @return void
     */
    abstract public function add($item);

    /**
     * Retrieve if an element match, this is used in searchById function
     *
     * @param string $id
     * @param mixed $item
     * @return bool
     */
    abstract protected function elementMatchId($id, $item);

    /**
     * Get the index in the collection for an element
     *
     * @param string $id
     * @param int $start
     * @return int index of the element, -1 if not found
     */
    public function searchById($id, $start = 0)
    {
        $count = count($this->collection);
        for ($index = max(0, $start); $index < $count; $index++) {
            if ($this->elementMatchId($id, $this->collection[$index])) {
                return $index;
            }
        }
        return -1;
    }

    /** @var array */
    protected $collection = [];

    public function __construct(array $items = [])
    {
        $this->addArray($items);
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
        return (array_key_exists((string) $index, $this->collection));
    }

    /**
     * Return if the item identified by id exists
     *
     * @param string $id
     * @param int $start
     * @return bool
     */
    public function existsById($id, $start = 0)
    {
        return (-1 !== $index = $this->searchById($id, $start));
    }

    /**
     * Return one item by index
     *
     * @param string $index
     * @return mixed
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
     * @param int $start
     * @return mixed
     * @throws XLSXException
     */
    public function getById($id, $start = 0)
    {
        if (-1 === $index = $this->searchById($id, $start)) {
            throw new XLSXException("The item {$id} does not exists");
        }
        return $this->get($index);
    }

    /**
     * Number of items
     * @return int
     */
    public function count()
    {
        return count($this->collection);
    }

    /**
     * Return all the items
     * @return array
     */
    public function all()
    {
        return $this->collection;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->collection);
    }
}
