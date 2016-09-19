<?php
namespace XLSXExporter;

use SplFileObject;
use XLSXExporter\Utils\XmlConverter;

class WorkSheetWriter
{
    /** @var SplFileObject */
    protected $file;
    protected $row;
    protected $col;
    protected $initialrow;
    protected $initialcol;
    protected $rowscount;
    protected $colscount;

    public function createSheet($filename, $colscount, $rowscount, $initialcol = 1, $initialrow = 1)
    {
        $this->initialrow = $initialrow;
        $this->initialcol = $initialcol;
        $this->colscount = $colscount;
        $this->rowscount = $rowscount;
        $this->row = $initialrow;
        $this->col = $initialcol;
        $this->file = new SplFileObject($filename, 'w');
    }

    public function openSheet()
    {
        $firstcell = $this->colByNumber($this->initialcol) . $this->initialrow;
        $lastcell = $this->colByNumber($this->colscount) . ($this->rowscount + 1);
        $this->file->fwrite(
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<dimension ref="' . $firstcell . ':' . $lastcell . '"/>'
            . '<sheetViews>'
            . '<sheetView tabSelected="0" workbookViewId="0"><selection activeCell="A1" sqref="A1"/></sheetView>'
            . '</sheetViews>'
            . '<sheetFormatPr baseColWidth="10" defaultRowHeight="15"/>'
            . '<sheetData>'
        );
    }

    public function closeSheet()
    {
        $this->file->fwrite(''
            . '</sheetData>'
            . '<pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>'
            . '</worksheet>');
        $this->file = null;
    }

    public function openRow()
    {
        $this->file->fwrite('<row r="' . $this->row . '" spans="1:' . $this->colscount . '">');
    }

    public function closeRow()
    {
        $this->file->fwrite('</row>');
        $this->row = $this->row + 1;
        $this->col = $this->initialcol;
    }

    /**
     * @param string $type one constant of CellTypes class
     * @param mixed $value the value to write
     * @param string $style the cell style
     */
    public function writeCell($type, $value, $style)
    {
        if ($value === null) {
            $type = CellTypes::NUMBER;
            $value = '';
        }
        $ooxType = static::getDataType($type);
        $this->file->fwrite('<c r="' . static::colByNumber($this->col) . $this->row . '"'
            . (($style) ? ' s="' . $style . '"' : '')
            . (($ooxType) ? ' t="' . $ooxType . '"' : '')
            . '>');
        if ($type === CellTypes::TEXT || $type === CellTypes::NUMBER) {
            $this->file->fwrite('<v>' . $value . '</v>');
        } elseif ($type === CellTypes::BOOLEAN) {
            $this->file->fwrite('<v>' . (($value) ? 1 : 0) . '</v>');
        } elseif ($type === CellTypes::DATE) {
            $this->file->fwrite('<v>' . DateConverter::tsToExcelDate($value) . '</v>');
        } elseif ($type === CellTypes::TIME) {
            $this->file->fwrite('<v>' . DateConverter::tsToExcelTime($value) . '</v>');
        } elseif ($type === CellTypes::DATETIME) {
            $this->file->fwrite('<v>' . DateConverter::tsToExcelDateTime($value) . '</v>');
        } elseif ($type === CellTypes::INLINE) {
            $this->file->fwrite('<is><t>' . XmlConverter::parse($value) . '</t></is>');
        }
        $this->file->fwrite('</c>');
        $this->col = $this->col + 1;
    }

    /**
     * Retrieve the internal Office Open Xml internal data type
     *
     * @param string $type
     * @return string
     */
    public static function getDataType($type)
    {
        if ($type === CellTypes::TEXT) {
            return 's';
        } elseif ($type === CellTypes::BOOLEAN) {
            return 'b';
        } elseif ($type === CellTypes::NUMBER) {
            return 'n';
        } else { // INLINE and DATES
            return '';
        }
    }

    /**
     * Get the letters of the column, the first column number is 1
     *
     * @param int $column
     * @return string
     */
    public static function colByNumber($column)
    {
        return static::getNameFromNumber(max(1, $column) - 1);
    }

    /**
     * This function was posted by Anthony Ferrara (ircmaxell) at stackoverflow
     * http://stackoverflow.com/questions/3302857/algorithm-to-get-the-excel-like-column-name-of-a-number
     * The license of this code is considered as public domain
     * @author ircmaxell http://stackoverflow.com/users/338665/ircmaxell
     * @param int $num base zero index
     * @return string
     */
    protected static function getNameFromNumber($num)
    {
        $numeric = ($num) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return self::getNameFromNumber($num2 - 1) . $letter;
        }
        return $letter;
    }
}
