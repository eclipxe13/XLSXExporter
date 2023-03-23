<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\DBAL;

use Eclipxe\XlsxExporter\ProviderInterface;
use Eclipxe\XlsxExporter\Providers\ProviderIterator;
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
