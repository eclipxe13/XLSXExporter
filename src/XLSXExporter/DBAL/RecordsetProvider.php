<?php
namespace XLSXExporter\DBAL;

use EngineWorks\DBAL\Recordset;
use XLSXExporter\ProviderInterface;
use XLSXExporter\Providers\ProviderIterator;

/**
 * The RecordsetProvider uses a Recordset object as a Provider
 * Important: This class will export from the current record and move forward, it will not move first
 *
 * @package XLSXExporter\DBAL
 */
class RecordsetProvider extends ProviderIterator implements ProviderInterface
{
    public function __construct(Recordset $recordset)
    {
        parent::__construct($recordset->getIterator(), $recordset->getRecordCount());
    }
}
