<?php

namespace XLSXExporter;

interface ProviderInterface
{
    /**
     * Return a value from the current tuple
     *
     * @param string $key
     * @return mixed
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
