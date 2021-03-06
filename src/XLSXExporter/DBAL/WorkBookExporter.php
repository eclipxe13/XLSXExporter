<?php
namespace XLSXExporter\DBAL;

use EngineWorks\DBAL\CommonTypes;
use EngineWorks\DBAL\DBAL;
use EngineWorks\DBAL\Recordset;
use XLSXExporter\CellTypes;
use XLSXExporter\Column;
use XLSXExporter\Columns;
use XLSXExporter\Style;
use XLSXExporter\Styles\Alignment;
use XLSXExporter\Styles\Format;
use XLSXExporter\WorkBook;
use XLSXExporter\WorkSheet;
use XLSXExporter\XLSXException;

class WorkBookExporter
{
    /** @var WorkBook */
    private $workbook;

    /** @var Style */
    private $defaultHeaderStyle;

    public function __construct(Style $defaultStyle = null, Style $defaultHeaderStyle = null)
    {
        $this->workbook = new WorkBook(null, $defaultStyle);
        $this->defaultHeaderStyle = $defaultHeaderStyle;
    }

    /**
     * @return WorkBook
     */
    public function getWorkbook()
    {
        return $this->workbook;
    }

    /**
     * Attach a recordset with the specified sheetname, headers and header style
     *
     * The headers array must contain a key value array using the fieldname as key and two properties:
     * title & style, the title must be an string and the style a valid array to be used in Style::setFromArray method
     *
     * @param Recordset $recordset
     * @param string $sheetName
     * @param array $headers
     * @param Style|null $defaultHeaderStyle
     * @throws XLSXException when the recordset did not retrieve the fields collection
     */
    public function attach(Recordset $recordset, $sheetName, array $headers = [], Style $defaultHeaderStyle = null)
    {
        $fields = $recordset->getFields();
        if (! is_array($fields)) {
            throw new XLSXException('The recordset did not retrieve the fields collection');
        }
        $this->workbook->getWorkSheets()->add(
            new WorkSheet(
                $sheetName,
                new RecordsetProvider($recordset),
                $this->createColumnsFromFields($fields, $headers),
                $defaultHeaderStyle ? : $this->defaultHeaderStyle
            )
        );
    }

    /**
     * This function takes an array (as exposed by recordset::getFields method)
     * and headers array to create a Columns object
     *
     * See attach method to write the headers array
     *
     * @param array $fields
     * @param array $headers
     * @return Columns
     */
    public static function createColumnsFromFields(array $fields, array $headers = [])
    {
        $useheaders = count($headers);
        $columns = new Columns();
        foreach ($fields as $field) {
            if ($useheaders && ! array_key_exists($field['name'], $headers)) {
                continue;
            }
            $columns->add(new Column(
                $field['name'],
                $field['name'],
                self::getColumnTypeFromFieldType($field['commontype']),
                self::getColumnStyleFromFieldType($field['commontype'])
            ));
        }
        if ($useheaders) {
            $sorted = new Columns();
            foreach ($headers as $fieldname => $properties) {
                if (! $columns->existsById($fieldname)) {
                    continue;
                }
                /* @var $column Column */
                $column = $columns->getById($fieldname);
                // set title
                if (array_key_exists('title', $properties)
                    && is_string($properties['title'])) {
                    $column->setTitle($properties['title']);
                }
                // set style
                if (array_key_exists('style', $properties)
                    && is_array($properties['style'])
                    && count($properties['style'])) {
                    $column->getStyle()->setFromArray($properties['style']);
                }
                $sorted->add($column);
            }
            $columns = $sorted;
        }
        return $columns;
    }

    private static function getColumnTypeFromFieldType($type)
    {
        $map = [
            CommonTypes::TTEXT => CellTypes::TEXT,
            CommonTypes::TNUMBER => CellTypes::NUMBER,
            CommonTypes::TINT => CellTypes::NUMBER,
            CommonTypes::TBOOL => CellTypes::BOOLEAN,
            CommonTypes::TDATETIME => CellTypes::DATETIME,
            CommonTypes::TDATE => CellTypes::DATE,
            CommonTypes::TTIME => CellTypes::TIME,
        ];
        return (array_key_exists($type, $map)) ? $map[$type] : CellTypes::TEXT;
    }

    private static function getColumnStyleFromFieldType($type)
    {
        $map = [
            DBAL::TTEXT => [],
            DBAL::TNUMBER => ['format' => ['code' => Format::FORMAT_COMMA_2DECS]],
            DBAL::TINT => ['format' => ['code' => Format::FORMAT_ZERO_0DECS]],
            DBAL::TBOOL => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            DBAL::TDATETIME => ['format' => ['code' => Format::FORMAT_DATE_YMDHM]],
            DBAL::TDATE => ['format' => ['code' => Format::FORMAT_DATE_YMD]],
            DBAL::TTIME => ['format' => ['code' => Format::FORMAT_DATE_HM]],
        ];
        $style = new Style();
        $style->setFromArray((array_key_exists($type, $map)) ? $map[$type] : []);
        return $style;
    }
}
