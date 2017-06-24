<?php
namespace XLSXExporter\DBAL;

use EngineWorks\DBAL\Recordset;
use XLSXExporter\ProviderInterface;

/**
 * The RecordsetProvider uses a Recordset object as a Provider
 * Important: This class will not move the current record but forward (it will not rewind)
 *
 * @package XLSXExporter\DBAL
 */
class RecordsetProvider implements ProviderInterface
{
    /** @var Recordset */
    private $recordset;

    public function __construct(Recordset $recordset)
    {
        $this->recordset = $recordset;
    }

    public function count()
    {
        return $this->recordset->getRecordCount();
    }

    public function get($key)
    {
        return (array_key_exists($key, $this->recordset->values)) ? $this->recordset->values[$key] : null;
    }

    public function next()
    {
        $this->recordset->moveNext();
    }

    public function valid()
    {
        return (! $this->recordset->eof());
    }
}
