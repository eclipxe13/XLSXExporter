<?php

namespace XLSXExporter;

class ProviderArray implements ProviderInterface
{
    private $dataset;
    private $index;

    public function __construct(array $dataset)
    {
        $this->dataset = $dataset;
        $this->index = 0;
    }

    public function get($key)
    {
        return $this->dataset[$this->index][$key];
    }

    public function next()
    {
        $this->index = $this->index + 1;
    }

    public function valid()
    {
        return ($this->index < count($this->dataset));
    }

    public function count()
    {
        return count($this->dataset);
    }
}
