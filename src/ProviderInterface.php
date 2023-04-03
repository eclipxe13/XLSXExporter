<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter;

use Countable;

/**
 * Interface ProviderInterface
 *
 * The providers are very similar to Iterator, but it is not the same.
 * The objects that implement this interface will be used like:
 *
 * $totalRecords = $provider->count();
 * while($provider->valid()) {
 *     $provider->get($key);
 *     $provider->next();
 * }
 *
 * If the provider does not implement correctly the total count the resulting
 * xlsx file could warn for an error in the file.
 *
 * Be aware that there are some very generic providers already implemented:
 * ProviderArray: To be used with an array
 * ProviderIterator: To be used with an iterator.
 * In both cases, to retrieve a value the helper ProviderGetValue::get() will be used.
 *
 * @see \Eclipxe\XlsxExporter\Providers\ProviderArray
 * @see \Eclipxe\XlsxExporter\Providers\ProviderIterator
 * @see \Eclipxe\XlsxExporter\Utils\ProviderGetValue::get()
 */
interface ProviderInterface extends Countable
{
    /**
     * Get a value of the current tuple based on the key
     * If the current tuple does not contain the key it should return NULL
     *
     * @return scalar|null value or null if the key is not set
     */
    public function get(string $key);

    /**
     * Move the provider to the next tuple
     */
    public function next(): void;

    /**
     * Checks if current tuple is valid
     */
    public function valid(): bool;

    /**
     * Return the total number of tuples
     *
     * @return int
     */
    public function count(): int;
}
