<?php

namespace XLSXExporter;

use SplFileObject;

//TODO: Try to use XML Writer
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
        $this->file = new SplFileObject($filename, "a");
    }

    public function openSheet()
    {
        $firstcell = $this->colByNumber($this->initialcol).$this->initialrow;
        $lastcell = $this->colByNumber($this->colscount).($this->rowscount + 1);
        $this->file->fwrite(
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<dimension ref="'.$firstcell.':'.$lastcell.'"/>'
            .'<sheetViews><sheetView tabSelected="1" workbookViewId="0"/></sheetViews>'
            .'<sheetFormatPr baseColWidth="10" defaultRowHeight="15"/>'
            .'<sheetData>'
        );
    }

    public function closeSheet() {
        $this->file->fwrite(''
            .'</sheetData>'
            .'<pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>'
            .'</worksheet>'
        );
        $this->file = null;
    }

    public function openRow() {
        $this->file->fwrite('<row r="'.$this->row.'" spans="1:'.$this->colscount.'">');
    }

    public function closeRow() {
        $this->file->fwrite('</row>');
        $this->row = $this->row + 1;
        $this->col = $this->initialcol;
    }

    public function writeCell($type, $value, $style) {
        if ($value === null) {
            $type = CellTypes::TEXT;
            $value = "";
        }
        $t = static::getDataType($type);
        $this->file->fwrite('<c r="'.static::colByNumber($this->col).$this->row.'"'
            .(($style) ? ' s="'.$style.'"' : '')
            .(($t) ? ' t="'.$t.'"' : '')
            .'>');
        if ($type === CellTypes::TEXT) {
            $this->file->fwrite('<v>'.$value.'</v>');
        } elseif ($type === CellTypes::NUMBER) {
            $this->file->fwrite('<v>'.$value.'</v>');
        } elseif ($type === CellTypes::BOOLEAN) {
            $this->file->fwrite('<v>'.(($value) ? 1 : 0).'</v>');
        } elseif ($type === CellTypes::TIMESTAMP) {
            $this->file->fwrite('<v>'.$value.'</v>');
        } elseif ($type === CellTypes::INLINETEXT) {
            $this->file->fwrite('<is><t>'.static::xml($value).'</t></is>');
        }
        $this->file->fwrite('</c>');
        $this->col = $this->col + 1;
    }

    public static function xml($text)
    {
        // do not convert single quotes
        return htmlspecialchars($text, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    public static function getDataType($type)
    {
        if ($type === CellTypes::TEXT) {
            return "s";
        } elseif ($type === CellTypes::BOOLEAN) {
            return "b";
        } elseif ($type === CellTypes::TIMESTAMP) {
            return "d";
        } elseif ($type === CellTypes::INLINE) {
            return "";
        } else { // DATE & NUMBER
            return "n";
        }
    }

    public static function colByNumber($column)
    {
        return static::getNameFromNumber($column - 1);
    }

    /**
     * This function was posted by ircmaxell at stackoverflow
     * http://stackoverflow.com/questions/3302857/algorithm-to-get-the-excel-like-column-name-of-a-number
     * The licence is consider as public domain
     * @author ircmaxell http://stackoverflow.com/users/338665/ircmaxell
     * @param integer $num base zero index
     * @return string
     */
    protected static function getNameFromNumber($num) {
        $numeric = ($num) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return self::getNameFromNumber($num2 - 1) . $letter;
        }
        return $letter;
    }
}
