<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Providers;

use Countable;
use Eclipxe\XlsxExporter\ProviderInterface;
use Eclipxe\XlsxExporter\Utils\ProviderGetValue;
use Iterator;

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
 */
class ProviderIterator implements ProviderInterface
{
    private Iterator $iterator;

    private int $count;

    /**
     * ProviderIterator constructor.
     * @param Iterator $iterator
     * @param int $count The total count of records, -1 to obtain
     */
    public function __construct(Iterator $iterator, int $count = -1)
    {
        $this->iterator = $iterator;
        if ($count < 0) {
            if ($this->iterator instanceof Countable) {
                $count = $this->iterator->count();
            } else {
                $count = iterator_count($this->iterator);
                $this->iterator->rewind();
            }
        }
        $this->count = $count;
    }

    public function get(string $key)
    {
        return ProviderGetValue::get($this->iterator->current(), $key);
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    public function count(): int
    {
        return $this->count;
    }
}
