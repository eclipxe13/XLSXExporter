<?php
namespace XLSXExporter\Providers;

use XLSXExporter\ProviderInterface;

/**
 * This is a NullProvider
 *
 * @package XLSXExporter\Providers
 */
class NullProvider implements ProviderInterface
{
    public function get($key)
    {
        return null;
    }

    public function next()
    {
    }

    public function valid()
    {
        return false;
    }

    public function count()
    {
        return 0;
    }
}
