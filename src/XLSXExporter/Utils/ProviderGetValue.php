<?php
namespace XLSXExporter\Utils;

class ProviderGetValue
{
    /**
     * Retrieve a value from a key-value array, an ArrayAccess object or an object property
     *
     * @param mixed $values
     * @param string $key
     * @return mixed|null
     */
    public static function get($values, $key)
    {
        $asObject = is_object($values);
        $asArray = is_array($values) || $values instanceof \ArrayAccess;
        if (! $asObject && ! $asArray) {
            return null;
        }
        if ($asArray) {
            return (isset($values[$key])) ? $values[$key] : null;
        }
        return (isset($values->{$key})) ? $values->{$key} : null;
    }
}
