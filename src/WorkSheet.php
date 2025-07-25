<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter;

use Eclipxe\XlsxExporter\Exceptions\InvalidPropertyNameException;
use Eclipxe\XlsxExporter\Exceptions\InvalidWorkSheetNameException;
use Eclipxe\XlsxExporter\Providers\NullProvider;
use Eclipxe\XlsxExporter\Utils\TemporaryFile;
use EngineWorks\ProgressStatus\NullProgress;
use EngineWorks\ProgressStatus\ProgressInterface;

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
    protected string $name;

    /** @var Columns|Column[] Columns collection */
    protected $columns;

    protected ProviderInterface $provider;

    protected Style $headerStyle;

    /**
     * WorkSheet constructor.
     *
     * @param string $name
     * @param ProviderInterface|null $provider
     * @param Columns|null $columns
     * @param Style|null $headerStyle
     */
    public function __construct(
        string $name,
        ?ProviderInterface $provider = null,
        ?Columns $columns = null,
        ?Style $headerStyle = null
    ) {
        $this->setName($name);
        $this->setProvider($provider ?? new NullProvider());
        $this->setColumns($columns ?? new Columns());
        $this->setHeaderStyle($headerStyle ?? BasicStyles::defaultHeader());
    }

    /**
     * Magic method, this allows to access methods as getters
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        $props = ['name', 'provider', 'columns', 'headerStyle'];
        if (! in_array($name, $props)) {
            throw new InvalidPropertyNameException($name);
        }
        $method = 'get' . ucfirst($name);
        return $this->$method();
    }

    /**
     * @return string Worksheet name
     */
    public function getName(): string
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
     */
    public function setName(string $name): void
    {
        if ('' === $name) {
            throw new InvalidWorkSheetNameException($name, 'the name is empty');
        }
        if (preg_match('/[\'\/\\\\:?*\[\]\n\r\t\0]/', $name)) {
            throw new InvalidWorkSheetNameException($name, 'the name contains invalid chars');
        }
        if (strlen($name) > 31) {
            throw new InvalidWorkSheetNameException($name, 'the name length is more than 31 chars length');
        }
        $this->name = $name;
    }

    public function getColumns(): Columns
    {
        return $this->columns;
    }

    public function setColumns(Columns $columns): void
    {
        $this->columns = $columns;
    }

    /**
     * Retrieve the header style
     */
    public function getHeaderStyle(): Style
    {
        return $this->headerStyle;
    }

    /**
     * Set the header style
     * @param Style $headerStyle
     */
    public function setHeaderStyle(Style $headerStyle): void
    {
        $this->headerStyle = $headerStyle;
    }

    /**
     * Set the provider object
     *
     * @param ProviderInterface $provider
     */
    public function setProvider(ProviderInterface $provider): void
    {
        $this->provider = $provider;
    }

    /**
     * Retrieve the provider object, null if the object has not been set
     */
    public function getProvider(): ProviderInterface
    {
        return $this->provider;
    }

    /**
     * Write the contents of the worksheet, it requires a SharedStrings object
     */
    public function write(TemporaryFile $file, SharedStrings $strings, ?ProgressInterface $progress = null): void
    {
        $writer = new WorkSheetWriter();
        $rowsCount = $this->provider->count();
        $progress = $progress ?: new NullProgress();
        $progress->update('', 0, $rowsCount + 1);
        $writer->createSheet($file->getPath(), $this->columns->count(), $rowsCount);
        $writer->openSheet();
        // -- write headers contents
        $writer->openRow();
        $styleIndex = $this->getHeaderStyle()->getStyleIndex();
        foreach ($this->columns as $column) {
            // write cell
            $writer->writeCell(CellTypes::TEXT, $strings->add($column->getTitle()), $styleIndex);
        }
        $writer->closeRow();
        $progress->increase();
        // -- write cell contents
        while ($this->provider->valid()) {
            // write new row
            $writer->openRow();
            foreach ($this->columns as $column) {
                // write cell
                $value = $this->provider->get($column->getId());
                $type = $column->getType();
                if (CellTypes::TEXT === $type) {
                    $value = $strings->add((string) $value);
                }
                $writer->writeCell($type, $value, $column->style->getStyleIndex());
            }
            $writer->closeRow();
            // move to the next record
            $this->provider->next();
            $progress->increase();
        }
        $writer->closeSheet();
    }
}
