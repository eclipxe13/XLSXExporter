<?php

namespace XLSXExporter\Styles;

/**
 * Interface StyleInterface
 *
 * @package XLSXExporter\Styles
 */
interface StyleInterface
{
    /**
     * @param int $index
     * @return void
     */
    public function setIndex($index);
    
    /**
     * @return int
     */
    public function getIndex();

    /**
     * @return bool
     */
    public function hasValues();

    /**
     * Set all values from the key-value array
     *
     * @param array $values
     * @return void
     */
    public function setValues(array $values);

    /**
     * Return a key-values array with the properties of the class and its values
     * @return array
     */
    public function getValues();

    /**
     * Get a string that represent the object,
     * two objects with the same data must retrieve the same hash
     *
     * @return string
     */
    public function getHash();

    /**
     * Get the object as xml element
     *
     * @return string
     */
    public function asXML();
}
