<?php
namespace XLSXExporter\Styles;

use XLSXExporter\XLSXException;

/**
 * Abstract implementation of the StyleInterface
 * Use this class
 *
 * @package XLSXExporter\Styles
 */
abstract class AbstractStyle implements StyleInterface
{
    /**
     * Storage of the properties contents
     * @var array
     */
    protected $data = [];

    /** @var int Index property */
    protected $index;

    /**
     * Get an array of property names
     *
     * @return array
     */
    abstract protected function properties();

    /**
     * @inheritdoc
     */
    abstract public function asXML();

    public function setValues(array $array)
    {
        foreach ($array as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getValues()
    {
        $array = [];
        foreach ($this->properties() as $key) {
            $array[$key] = $this->$key;
        }
        return $array;
    }

    public function __get($name)
    {
        if (! in_array($name, $this->properties())) {
            throw new XLSXException("Invalid property name $name");
        }
        $method = 'get' . ucfirst($name);
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
        if (! in_array($name, $this->properties())) {
            throw new XLSXException("Invalid property name $name");
        }
        $method = 'cast' . ucfirst($name);
        if (method_exists($this, $method)) {
            $value = $this->$method($value);
        } else {
            $method = 'set' . ucfirst($name);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        $this->data[$name] = $value;
    }

    public function __isset($name)
    {
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
        return sha1(self::class . '::' . print_r($this->data, true));
    }
}
