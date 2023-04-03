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
        if ($asArray) {
            return $values[$key] ?? null;
        }
        if ($asObject) {
            return $values->{$key} ?? null;
        }
        return null;
    }
}
