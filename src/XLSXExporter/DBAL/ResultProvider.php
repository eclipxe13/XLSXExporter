<?php
namespace XLSXExporter\DBAL;

use EngineWorks\DBAL\Result;
use XLSXExporter\ProviderInterface;
use XLSXExporter\Providers\ProviderIterator;

/**
 * The ResultProvider uses a Result object as a Provider
 * Important: This class will export from the current record and move forward
 * If there is no current record (iterator is not valid) then it will call rewind
 *
 * @package XLSXExporter\DBAL
 */
class ResultProvider extends ProviderIterator implements ProviderInterface
{
    public function __construct(Result $result)
    {
        $iterator = $result->getIterator();
        if (! $iterator->valid()) {
            $iterator->rewind();
        }
        parent::__construct($iterator, $result->count());
    }
}
