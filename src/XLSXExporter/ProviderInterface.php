<?php
namespace XLSXExporter;

/**
 * Interface ProviderInterface
 *
 * The providers are very similar to Iterator but its not the same.
 * The objects that implement this interface will be used like:
 *
 * $totalRecords = $provider->count();
 * while($provider->valid()) {
 *     $provider->get($key);
 *     $provider->next();
 * }
 *
 * If the provider does not implement correctly the total count the resulting
 * xlsx file could warning for an error in the file.
 *
 * Be aware that there are some very generic providers already implemented:
 * ProviderArray: To be used with an array
 * ProviderIterator: To be used with an iterator.
 * In both cases, to retrieve a value the helper ProviderGetValue::get() will be used.
 *
 * @see \XLSXExporter\Providers\ProviderArray
 * @see \XLSXExporter\Providers\ProviderIterator
 * @see \XLSXExporter\Utils\ProviderGetValue::get()
 *
 * @package XLSXExporter
 */
interface ProviderInterface
{
    /**
     * Get a value of the current tuple based on the key
     * If the current tuple does not contains the key it should return NULL
     *
     * @param string $key
     * @return mixed|null value or null if the key is not set
     */
    public function get($key);

    /**
     * Move the provider to the next tuple
     *
     * @return void
     */
    public function next();

    /**
     * Checks if current tuple is valid
     *
     * @return bool
     */
    public function valid();

    /**
     * Return the total number of tuples
     *
     * @return int
     */
    public function count();
}
