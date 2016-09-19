<?php
namespace XLSXExporter;

use XLSXExporter\Providers\NullProvider;

/**
 * WorkSheet class
 *
 * @property ProviderInterface $provider Provider object
 * @property Columns|Column[] $columns Columns object
 * @property string $name Name of the worksheet
 * @property Style $headerStyle Style of the header columns
 */
class WorkSheet
{
    /** @var string The name of the worksheet **/
    protected $name;

    /** @var Columns|Column[] Columns collection */
    protected $columns;

    /** @var ProviderInterface */
    protected $provider;

    /** @var Style */
    protected $headerStyle;

    /**
     * WorkSheet constructor.
     *
     * @param string $name
     * @param ProviderInterface $provider
     * @param Columns $columns
     * @param Style $headerStyle
     */
    public function __construct(
        $name,
        ProviderInterface $provider = null,
        Columns $columns = null,
        Style $headerStyle = null
    ) {
        $this->setName($name);
        $this->setProvider(($provider) ? : new NullProvider());
        $this->setColumns(($columns) ? : new Columns());
        $this->setHeaderStyle(($headerStyle) ? : BasicStyles::defaultHeader());
    }

    /**
     * Magic method, this allow to access methods as getters:
     * - name => getName
     * - columns => getColumns
     *
     * @param string $name
     * @return mixed
     * @throws XLSXException
     */
    public function __get($name)
    {
        $props = ['name', 'provider', 'columns', 'headerStyle'];
        if (! in_array($name, $props)) {
            throw new XLSXException("Invalid property name $name");
        }
        $method = 'get' . ucfirst($name);
        return $this->$method();
    }

    /**
     * @return string Worksheet name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the sheet, be aware that the name has several constraints:
     * - Must be a string
     * - Cannot contains: : \ / ? * [ ] ' tab nl cr null
     * - Cannot be more than 31 chars
     * - Cannot be an empty string
     *
     * @param string $name
     * @throws XLSXException
     */
    public function setName($name)
    {
        if (! is_string($name)) {
            throw new XLSXException('Invalid worksheet name, is not a string');
        }
        if ('' === $name) {
            throw new XLSXException('Invalid worksheet name, is empty');
        }
        $castedName = preg_replace('/[\'\/\\\\:\?\*\[\]\n\r\t\0]/', '', $name);
        if ($name !== $castedName) {
            throw new XLSXException('Invalid worksheet name, contains invalid chars');
        }
        if (strlen($castedName) > 31) {
            throw new XLSXException('Invalid worksheet name, is more than 31 chars length');
        }
        $this->name = $name;
    }

    /**
     * Retrieve the columns collection object
     *
     * @return Column[]|Columns
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Set the columns collection object
     *
     * @param Columns $columns
     */
    public function setColumns(Columns $columns)
    {
        $this->columns = $columns;
    }

    /**
     * Retrieve the header style
     *
     * @return Style
     */
    public function getHeaderStyle()
    {
        return $this->headerStyle;
    }

    /**
     * Set the header style
     * @param Style $headerStyle
     */
    public function setHeaderStyle(Style $headerStyle)
    {
        $this->headerStyle = $headerStyle;
    }

    /**
     * Set the provider object
     *
     * @param ProviderInterface $provider
     */
    public function setProvider(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Retrieve the provider object, null if the object has not been set
     *
     * @return ProviderInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Write the contents of the worksheet, it requires a SharedStrings object
     *
     * @param SharedStrings $strings
     * @return string
     * @throws XLSXException
     */
    public function write(SharedStrings $strings)
    {
        $tempfile = tempnam(sys_get_temp_dir(), 'ws-');
        $writer = new WorkSheetWriter();
        $writer->createSheet($tempfile, $this->columns->count(), $this->provider->count());
        $writer->openSheet();
        // -- write headers contents
        $writer->openRow();
        $styleindex = $this->getHeaderStyle()->getStyleIndex();
        foreach ($this->columns as $column) {
            // write cell
            $s = $strings->add($column->getTitle());
            $writer->writeCell(CellTypes::TEXT, $s, $styleindex);
        }
        $writer->closeRow();
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
