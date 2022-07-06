<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter;

use Eclipxe\XLSXExporter\Utils\XmlConverter;
use SplFileObject;

class WorkSheetWriter
{
    protected SplFileObject $file;

    protected int $row;

    protected int $col;

    protected int $initialrow;

    protected int $initialcol;

    protected int $rowscount;

    protected int $colscount;

    public function createSheet(string $filename, int $colscount, int $rowscount, int $initialcol = 1, int $initialrow = 1): void
    {
        $this->initialrow = $initialrow;
        $this->initialcol = $initialcol;
        $this->colscount = $colscount;
        $this->rowscount = $rowscount;
        $this->row = $initialrow;
        $this->col = $initialcol;
        $this->file = new SplFileObject($filename, 'w');
    }

    public function openSheet(): void
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

    public function closeSheet(): void
    {
        $this->file->fwrite(
            '</sheetData>'
            . '<pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>'
            . '</worksheet>'
        );
    }

    public function openRow(): void
    {
        $this->file->fwrite('<row r="' . $this->row . '" spans="1:' . $this->colscount . '">');
    }

    public function closeRow(): void
    {
        $this->file->fwrite('</row>');
        $this->row = $this->row + 1;
        $this->col = $this->initialcol;
    }

    /**
     * @param string $type one constant of CellTypes class
     * @param scalar|null $value the value to write
     * @param int|string|null $style the cell style
     */
    public function writeCell(string $type, $value, $style): void
    {
        if (null === $value) {
            $type = CellTypes::NUMBER;
            $value = '';
        }
        $ooxType = static::getDataType($type);
        $this->file->fwrite('<c r="' . static::colByNumber($this->col) . $this->row . '"'
            . (($style) ? ' s="' . $style . '"' : '')
            . (($ooxType) ? ' t="' . $ooxType . '"' : '')
            . '>');
        if (CellTypes::TEXT === $type || CellTypes::NUMBER === $type) {
            $this->file->fwrite('<v>' . $value . '</v>');
        } elseif (CellTypes::BOOLEAN === $type) {
            $this->file->fwrite('<v>' . (($value) ? 1 : 0) . '</v>');
        } elseif (CellTypes::DATE === $type) {
            $this->file->fwrite('<v>' . DateConverter::tsToExcelDate((int) $value) . '</v>');
        } elseif (CellTypes::TIME === $type) {
            $this->file->fwrite('<v>' . DateConverter::tsToExcelTime((int) $value) . '</v>');
        } elseif (CellTypes::DATETIME === $type) {
            $this->file->fwrite('<v>' . DateConverter::tsToExcelDateTime((int) $value) . '</v>');
        } elseif (CellTypes::INLINE === $type) {
            $this->file->fwrite('<is><t>' . XmlConverter::parse((string) $value) . '</t></is>');
        }
        $this->file->fwrite('</c>');
        $this->col = $this->col + 1;
    }

    /**
     * Retrieve the internal Office Open Xml internal data type
     */
    public static function getDataType(string $type): string
    {
        if (CellTypes::TEXT === $type) {
            return 's';
        }
        if (CellTypes::BOOLEAN === $type) {
            return 'b';
        }
        if (CellTypes::NUMBER === $type) {
            return 'n';
        }
        // INLINE and DATES
        return '';
    }

    /**
     * Get the letters of the column, the first column number is 1
     */
    public static function colByNumber(int $column): string
    {
        return static::getNameFromNumber(max(1, $column) - 1);
    }

    /**
     * This function was posted by Anthony Ferrara (ircmaxell) at stackoverflow
     * http://stackoverflow.com/questions/3302857/algorithm-to-get-the-excel-like-column-name-of-a-number
     * The license of this code is considered as public domain
     * @author ircmaxell http://stackoverflow.com/users/338665/ircmaxell
     * @param int $num base zero index
     */
    protected static function getNameFromNumber(int $num): string
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
