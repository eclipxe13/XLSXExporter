<?php

namespace XLSXExporter;

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
