<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace XLSXExporter\Styles;

use XLSXExporter\XLSXException;

abstract class AbstractStyle
{

    protected $data = [];
    protected $index;

    abstract protected function properties();
    abstract public function asXML();

    public function setValues($array)
    {
        foreach($array as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getValues()
    {
        $array = [];
        foreach($this->properties() as $key) {
            $array[$key] = $this->$key;
        }
        return $array;
    }

    public function __get($name)
    {
        $props = $this->properties();
        if (!in_array($name, $props)) {
            throw new XLSXException("Invalid property name $name");
        }
        $method = "get".ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return null;
    }

    public function __set($name, $value)
    {
        $props = $this->properties();
        if (!in_array($name, $props)) {
            throw new XLSXException("Invalid property name $name");
        }
        $method = "cast".ucfirst($name);
        if (method_exists($this, $method)) {
            $value = $this->$method($value);
        } else {
            $method = "set".ucfirst($name);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        $this->data[$name] = $value;

    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function hasValues()
    {
        return (count($this->data) != 0);
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function setIndex($index)
    {
        $this->index = $index;
    }

    public function getHash()
    {
        return md5(get_class($this)."\n".print_r($this, true));
    }

    public static function utilCastColor($value, $exceptionText)
    {
        $color = strtoupper(ltrim($value, "#"));
        if (strlen($color) == 6) $color = "FF".$color;
        if (!preg_match("/[0-9A-F]{8}/", $color)) {
            throw new XLSXException($exceptionText);
        }
        return $color;
    }


}
