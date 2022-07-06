<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\DBAL;

use Eclipxe\XLSXExporter\ProviderInterface;
use Eclipxe\XLSXExporter\Providers\ProviderIterator;
use EngineWorks\DBAL\Iterators\ResultIterator;
use EngineWorks\DBAL\Result;

/**
 * The ResultProvider uses a Result object as a Provider
 * Important: This class will export from the current record and move forward
 * If there is no current record (iterator is not valid) then it will call rewind
 */
class ResultProvider extends ProviderIterator implements ProviderInterface
{
    public function __construct(Result $result)
    {
        /**
         * @noinspection PhpUnhandledExceptionInspection
         * @var ResultIterator $iterator
         */
        $iterator = $result->getIterator();
        if (! $iterator->valid()) {
            $iterator->rewind();
        }
        parent::__construct($iterator, $result->count());
    }
}
