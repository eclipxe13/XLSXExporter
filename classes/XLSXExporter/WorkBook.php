<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace XLSXExporter;

use ZipArchive;

/**
 * @property-read Style $style Default style for the whole document
 * @property-read WorkSheets $worksheets Collections of worksheets
 */
class WorkBook {

    /** @var WorkSheets */
    protected $worksheets;

    /** @var Style default base style */
    protected $style;

    public function __construct($worksheets = null, $style = null)
    {
        // wroksheets
        if (!($worksheets instanceof WorkSheets)) {
            $worksheets = new WorkSheets();
        }
        $this->worksheets = $worksheets;
        // style
        if (!($style instanceof Style)) {
            $style = BasicStyles::defaultStyle();
        }
        $this->style = $style;
    }

    public function __get($name)
    {
        $props = ["worksheets", "style"];
        if (!in_array($name, $props)) {
            throw new XLSXException("Invalid property name $name");
        }
        $method = "get".ucfirst($name);
        return $this->$method();
    }

    public function getWorkSheets()
    {
        return $this->worksheets;
    }

    public function write()
    {
        if (!$this->worksheets->count()) {
            throw new XLSXException("Workbook does not contains any worksheet");
        }
        $removefiles = [];
        $filename = tempnam(sys_get_temp_dir(), "xlsx-");
        $zip = new ZipArchive();
        $zip->open($filename, ZipArchive::CREATE);

//        $zip->addEmptyDir("docProps/");
//        $zip->addFromString("docProps/app.xml" , self::buildAppXML());
//        $zip->addFromString("docProps/core.xml", self::buildCoreXML());
        $zip->addEmptyDir("xl/");
        $zip->addEmptyDir("xl/_rels/");
        $zip->addEmptyDir("_rels/");
        // files
        $zip->addFromString("_rels/.rels", $this->xmlRels());
        $zip->addFromString("[Content_Types].xml" , $this->xmlContentTypes());
        $zip->addFromString("xl/styles.xml", $this->xmlStyles());
        $zip->addFromString("xl/workbook.xml" , $this->xmlWorkbook());
        $zip->addFromString("xl/_rels/workbook.xml.rels", $this->xmlWorkbookRels());

        // worksheets using sharedStrings
        $sharedstrings = new SharedStrings();
        $zip->addEmptyDir("xl/worksheets/");
        foreach($this->worksheets as $worksheet) {
            // write and include the sheet
            $wsfile = $worksheet->write($sharedstrings);
            $zip->addFile($wsfile, $this->workSheetFilePath($worksheet));
            $removefiles[] = $wsfile;
        }
        $shstrsfile = $sharedstrings->write();
        // add the shared string
        $zip->addFile($shstrsfile, "xl/sharedStrings.xml" );
        $removefiles[] = $shstrsfile;
        $zip->close();
        foreach($removefiles as $file) {
            unlink($file);
        }
        return $filename;

    }

    protected function workSheetFilePath(WorkSheet $worksheet, $prefix = "xl/")
    {
        return $prefix."worksheets/".$worksheet->getName().".xml";
    }

    protected function xmlRels()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="wb1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    protected function xmlContentTypes()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
            .array_reduce($this->worksheets->all(), function($r, WorkSheet $ws) {
                return $r.'<Override PartName="/'.$this->workSheetFilePath($ws).'" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
            })
            .'</Types>';
    }

    public function xmlStyles()
    {
        $styles = [];
        $styles[] = $this->style;
        foreach($this->worksheets as $worksheet) {
            // add worksheet header style
            $styles[] = $worksheet->getHeaderStyle();
            foreach($worksheet->getColumns() as $column) {
                // add worksheet column style
                $styles[] = $column->getStyle();
            }
        }
//        print_r([
//            "before" => array_map(function(Style $s){ return $s->getStyleIndex();}, $styles),
//        ]);
        $stylesheet = new StyleSheet($styles);
//        print_r([
//            "after" => array_map(function(Style $s){ return $s->getStyleIndex();}, $styles),
//        ]);
//        print_r([
//            "styles" => array_map(function(Style $s){ return $s->getStyleIndex();}, $stylesheet->getStyles()),
//        ]);
        return $stylesheet->asXML();
    }

    public function xmlWorkbook()
    {
        $i = 0;
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets>'
            .array_reduce($this->worksheets->all(), function ($r, WorkSheet $worksheet) use (&$i) {
                $i = $i + 1;
                return $r.'<sheet name="'.$worksheet->getName().'" sheetId="'.$i.'" r:id="rId'.$i.'"/>';
            })
            .'</sheets>'
            .'</workbook>'
            ;
    }

    public function xmlWorkbookRels()
    {
        $i = 0;
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .array_reduce($this->worksheets->all(), function ($r, WorkSheet $worksheet) use (&$i) {
                $i = $i + 1;
                return $r.'<Relationship Id="rId'.$i.'"'
                    .' Target="'.$this->workSheetFilePath($worksheet, "").'"'
                    .' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"/>'
                ;
            })
            .'<Relationship Id="stl1" Target="styles.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"/>'
            .'<Relationship Id="shs1" Target="sharedStrings.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings"/>'
            .'</Relationships>'
            ;
    }

}
