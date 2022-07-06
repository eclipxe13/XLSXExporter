<?php

/**
 * @noinspection PhpClassConstantAccessedViaChildClassInspection
 */

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\DBAL;

use Eclipxe\XLSXExporter\CellTypes;
use Eclipxe\XLSXExporter\Column;
use Eclipxe\XLSXExporter\Columns;
use Eclipxe\XLSXExporter\Style;
use Eclipxe\XLSXExporter\Styles\Alignment;
use Eclipxe\XLSXExporter\Styles\Format;
use Eclipxe\XLSXExporter\WorkBook;
use Eclipxe\XLSXExporter\WorkSheet;
use EngineWorks\DBAL\CommonTypes;
use EngineWorks\DBAL\DBAL;
use EngineWorks\DBAL\Recordset;

class WorkBookExporter
{
    private WorkBook $workbook;

    private ?Style $defaultHeaderStyle;

    public function __construct(Style $defaultStyle = null, Style $defaultHeaderStyle = null)
    {
        $this->workbook = new WorkBook(null, $defaultStyle);
        $this->defaultHeaderStyle = $defaultHeaderStyle;
    }

    public function getWorkbook(): WorkBook
    {
        return $this->workbook;
    }

    /**
     * Attach a recordset with the specified sheet-name, headers and header style
     *
     * The headers array must contain a key value array using the field-name as key and two properties:
     * title & style, the title must be a string and the style a valid array to be used in Style::setFromArray method
     *
     * @param Recordset $recordset
     * @param string $sheetName
     * @param array<string, array{title?: string, style?: array<string, array<string, scalar>>}> $headers
     * @param Style|null $defaultHeaderStyle
     */
    public function attach(Recordset $recordset, string $sheetName, array $headers = [], Style $defaultHeaderStyle = null): void
    {
        /** @var array<string, array{name: string, table: string, commontype: string}> $fields */
        $fields = $recordset->getFields();
        $this->workbook->getWorkSheets()->add(
            new WorkSheet(
                $sheetName,
                new RecordsetProvider($recordset),
                $this->createColumnsFromFields($fields, $headers),
                $defaultHeaderStyle ?? $this->defaultHeaderStyle
            )
        );
    }

    /**
     * This function takes an array (as exposed by recordset::getFields method)
     * and headers array to create a Columns object
     *
     * See attach method to write the headers array
     *
     * @param array<array{name: string, commontype: string}> $fields
     * @param array<string, array{title?: string, style?: array<string, array<string, scalar>>}> $headers
     */
    public static function createColumnsFromFields(array $fields, array $headers = []): Columns
    {
        $useHeaders = ([] !== $headers);
        $columns = new Columns();
        foreach ($fields as $field) {
            if ($useHeaders && ! array_key_exists($field['name'], $headers)) {
                continue;
            }
            $columns->add(new Column(
                $field['name'],
                $field['name'],
                self::getColumnTypeFromFieldType($field['commontype']),
                self::getColumnStyleFromFieldType($field['commontype'])
            ));
        }
        if ($useHeaders) {
            $sorted = new Columns();
            foreach ($headers as $fieldname => $properties) {
                if (! $columns->existsById($fieldname)) {
                    continue;
                }
                $column = $columns->getById($fieldname);
                // set title
                if (
                    array_key_exists('title', $properties)
                    && is_string($properties['title'])
                ) {
                    $column->setTitle($properties['title']);
                }
                // set style
                if (
                    array_key_exists('style', $properties)
                    && is_array($properties['style'])
                    && count($properties['style'])
                ) {
                    $column->getStyle()->setFromArray($properties['style']);
                }
                $sorted->add($column);
            }
            $columns = $sorted;
        }
        return $columns;
    }

    private static function getColumnTypeFromFieldType(string $type): string
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
        return $map[$type] ?? CellTypes::TEXT;
    }

    private static function getColumnStyleFromFieldType(string $type): Style
    {
        /** @var array<string, array<string, array<string, scalar>>> $map */
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
        $style->setFromArray($map[$type] ?? []);
        return $style;
    }
}
