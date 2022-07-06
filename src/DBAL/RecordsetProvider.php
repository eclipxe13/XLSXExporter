<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\DBAL;

use Eclipxe\XLSXExporter\ProviderInterface;
use Eclipxe\XLSXExporter\Providers\ProviderIterator;
use EngineWorks\DBAL\Iterators\RecordsetIterator;
use EngineWorks\DBAL\Recordset;

/**
 * The RecordsetProvider uses a Recordset object as a Provider
 * Important: This class will export from the current record and move forward, it will not move first
 */
class RecordsetProvider extends ProviderIterator implements ProviderInterface
{
    public function __construct(Recordset $recordset)
    {
        /**
         * @noinspection PhpUnhandledExceptionInspection
         * @var RecordsetIterator $iterator
         */
        $iterator = $recordset->getIterator();
        parent::__construct($iterator, $recordset->getRecordCount());
    }
}
