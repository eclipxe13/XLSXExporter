<?php

namespace XLSXExporter;

use Iterator;
use XLSXExporter\Utils\ProviderGetValue;

/**
 * ProviderIterator is a facade to be able to use any Iterator as a Provider
 *
 * The iterator current method must return an array, an ArrayAccess or an object,
 * the code will check if the key is set in the element, if it is not set then
 * will return null
 *
 * As the Iterator does not know the length of the elements by itself then it is
 * desirable to provide the total count from the constructor.
 * If a negative number is provided then the function will traverse the hole iterator
 * to count the total elements.
 *
 * @package XLSXExporter
 */
class ProviderIterator implements ProviderInterface
{
    /** @var Iterator */
    private $iterator;
    private $count;

    /**
     * ProviderIterator constructor.
     * @param Iterator $iterator
     * @param int $count The total count of records, -1 to obtain
     */
    public function __construct(Iterator $iterator, $count = -1)
    {
        $this->iterator = $iterator;
        if ($count < 0) {
            $count = $this->obtainCountFromIterator();
        }
        $this->count = $count;
    }

    private function obtainCountFromIterator()
    {
        $count = 0;
        $this->iterator->rewind();
        while ($this->iterator->valid()) {
            $count = $count + 1;
        }
        $this->iterator->rewind();
    }

    public function get($key)
    {
        return ProviderGetValue::get($this->iterator->current(), $key);
    }

    public function next()
    {
        $this->iterator->next();
    }

    public function valid()
    {
        return $this->iterator->valid();
    }

    public function count()
    {
        return $this->count;
    }
}
