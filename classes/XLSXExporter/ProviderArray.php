<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace XLSXExporter;


class ProviderArray implements ProviderInterface
{
    private $data;
    private $index;

    public function __construct($data)
    {
        $this->data = $data;
        $this->index = 0;
    }

    public function get($key)
    {
        return $this->data[$this->index][$key];
    }

    public function next()
    {
        $this->index = $this->index + 1;
    }

    public function valid()
    {
        return ($this->index < count($this->data));
    }

    public function count()
    {
        return count($this->data);
    }

}
