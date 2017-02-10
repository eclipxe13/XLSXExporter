<?php
namespace XLSXExporter\DBAL;

use EngineWorks\DBAL\Recordset;
use XLSXExporter\ProviderInterface;

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
        /*
        print_r([
            '$key' => $key,
            '$this->recordset->values' => $this->recordset->values,
            'exists' => (array_key_exists($key, $this->recordset->values)) ? 'true' : 'false',
            'return' => (array_key_exists($key, $this->recordset->values)) ? $this->recordset->values[$key] : null,
        ]);
        die();
        */
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
