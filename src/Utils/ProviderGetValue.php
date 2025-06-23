<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Utils;

use ArrayAccess;

class ProviderGetValue
{
    /**
     * Retrieve a value from a key-value array, an ArrayAccess object or an object property
     *
     * @param mixed $values
     * @return scalar|null
     */
    public static function get($values, string $key)
    {
        $asObject = is_object($values);
        $asArray = is_array($values) || $values instanceof ArrayAccess;
        $value = null;
        if ($asArray && isset($values[$key])) {
            $value = $values[$key];
        }
        if ($asObject && isset($values->{$key})) {
            $value = $values->{$key};
        }
        return (is_null($value) || is_scalar($value)) ? $value : null;
    }
}
