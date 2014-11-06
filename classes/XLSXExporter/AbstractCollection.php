<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace XLSXExporter;

/**
 * Description of AbstractCollection
 *
 * @author eclipxe
 */
abstract class AbstractCollection  implements \IteratorAggregate
{
    public abstract static function isValidInstance($item);
    public abstract function add($item);

    /** @var array */
    protected $collection = [];

    public function __construct($items = null)
    {
        if (is_array($items)) {
            $this->addArray($items);
        }
    }

    protected function addItem($item, $id)
    {
        if (!static::isValidInstance($item)) {
            throw new XLSXException("The item is not a valid object for the collection");
        }
        if ($this->exists($id)) {
            throw new XLSXException("There is a item with the same id, ids must be unique");
        }
        $this->collection[$id] = $item;
    }

    /**
     * Call the add method with the contents of the item array
     * @param array $items
     */
    public function addArray(array $items)
    {
        foreach($items as $item) {
            $this->add($item);
        }
    }

    /**
     * Check if a item id exists
     * @param string $id
     * @return boolean
     */
    public function exists($id)
    {
        return (array_key_exists((string) $id, $this->collection));
    }

    /**
     * Number of items
     * @return integer
     */
    public function count()
    {
        return count($this->collection);
    }

    /**
     * Return one item
     * @param string $id
     * @throws XLSXException
     */
    public function get($id)
    {
        if (!$this->exists($id)) {
            throw new XLSXException("The item $id does not exists");
        }
        return $this->collection[$id];
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
    public function getIterator() {
        return new \ArrayIterator($this->collection);
    }

}
