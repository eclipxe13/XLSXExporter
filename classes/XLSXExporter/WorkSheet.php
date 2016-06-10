<?php

namespace XLSXExporter;

/**
 * @property-read Column $columns Columns object
 * @property-read string $name Name of the worksheet
 * @property-read Style $headerstyle Style of the header columns
 */
class WorkSheet
{
    /** @var string The name of the worksheet **/
    protected $name;

    /** @var Columns Columns collection */
    protected $columns;

    /** @var ProviderInterface */
    protected $provider;

    /** @var Style */
    protected $headerstyle;

    public function __construct($name, $provider = null, $columns = null, $headerstyle = null)
    {
        $this->setName($name);
        if (!($columns instanceof Columns)) {
            $columns = new Columns();
        }
        $this->setColumns($columns);
        if ($provider instanceof ProviderInterface) {
            $this->setProvider($provider);
        }
        if (!($headerstyle instanceof Style)) {
            $headerstyle = BasicStyles::defaultHeader();
        }
        $this->setHeaderStyle($headerstyle);
    }

    public function __get($name)
    {
        $props = ["name", "columns"];
        if (!in_array($name, $props)) {
            throw new XLSXException("Invalid property name $name");
        }
        $method = "get".ucfirst($name);
        return $this->$method();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        // TODO: Validate correct names for worksheets
        $this->name = $name;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function setColumns(Columns $columns)
    {
        $this->columns = $columns;
    }

    public function getHeaderStyle()
    {
        return $this->headerstyle;
    }

    public function setHeaderStyle(Style $headerstyle)
    {
        $this->headerstyle = $headerstyle;
    }

    public function setProvider(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function write(SharedStrings $strings)
    {
        $tempfile = tempnam(sys_get_temp_dir(), "ws-");
        $writer = new WorkSheetWriter();
        $writer->createSheet($tempfile, $this->columns->count(), $this->provider->count());
        $writer->openSheet();
        // -- write headers contents
        {
            // write new row
            $writer->openRow();
            $styleindex = $this->getHeaderStyle()->getStyleIndex();
        foreach ($this->columns as $column) {
            // write cell
            $s = $strings->add($column->getTitle());
            $writer->writeCell(CellTypes::TEXT, $s, $styleindex);
        }
            $writer->closeRow();
        }
        // -- write cell contents
        while ($this->provider->valid()) {
            // write new row
            $writer->openRow();
            foreach ($this->columns as $column) {
                // write cell
                $value = $this->provider->get($column->getId());
                if (CellTypes::TEXT === $type = $column->getType()) {
                    $value = $strings->add($value);
                }
                $writer->writeCell($type, $value, $column->style->getStyleIndex());
            }
            $writer->closeRow();
            // move to the next record
            $this->provider->next();
        }
        $writer->closeSheet();
        return $tempfile;
    }
}
