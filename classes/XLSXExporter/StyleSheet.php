<?php

namespace XLSXExporter;

class StyleSheet
{
    /** @var Style */
    protected $styles;
    /** @var array */
    protected $hashes;
    /** @var array */
    protected $objects;

    public function __construct(array $styles) {
        if (!count($styles)) {
            throw new XLSXException("Error creating the stylesheet, no styles found");
        }
        foreach($styles as $style) {
            if (!($style instanceof Style)) {
                throw new XLSXException("Error creating the stylesheet, received an invalid style object");
            }
        }
        // mandatory styles
        $this->styles = array_merge([
            new Style([
                "format" => [
                    "code" => Styles\Format::FORMAT_GENERAL,
                ],
                "fill" => [
                    "pattern" => Styles\Fill::NONE
                ],
                "alignment" => [
                    "horizontal" => Styles\Alignment::HORIZONTAL_GENERAL,
                    "vertical" => Styles\Alignment::VERTICAL_BOTTOM
                ],
            ]),
            new Style([
                "fill" => [
                    "pattern" => Styles\Fill::GRAY125
                ]
            ]),
        ], $styles);
    }

    protected function processStylesFormat()
    {
        $codes = [];
        $fmtcounter = 164;
        $this->objects["format"] = [];
        $this->hashes["format"] = [];
        foreach($this->styles as $style) {
            $format = $style->getFormat();
            if (!$format->hasValues()) {
                $format->code = Styles\Format::FORMAT_GENERAL;
            }
            if (false !== $builtin = $format->getBuiltInCodeByCode($format->code)) {
                $format->id = $builtin;
                if (!in_array($format->code, $codes)) {
                    $codes[$format->id] = $format->code;
                    array_push($this->objects["format"], $format);
                }
            } elseif (false !== $numfmtid = array_search($format->code, $codes)) {
                $format->id = $numfmtid;
            } else {
                $format->id = $fmtcounter;
                $fmtcounter = $fmtcounter + 1;
                $codes[$format->id] = $format->code;
                array_push($this->objects["format"], $format);
            }
        }
    }

    protected function processAddToArray($name, Styles\StyleInterface $generic)
    {
        $generic->setIndex(0);
        if ($generic->hasValues()) {
            $hash = $generic->getHash();
            if (false === $i = array_search($hash, $this->hashes[$name], true)) {
                $i = count($this->hashes[$name]);
                array_push($this->hashes[$name], $hash);
                array_push($this->objects[$name], $generic);
            }
            $generic->setIndex($i);
        }
    }

    protected function processStyles()
    {
        // different process for "format"
        $this->processStylesFormat();
        // same process for "font", "fill" and "border"
        $namedcollections = ["font", "fill", "border"];
        foreach($namedcollections as $name) {
            $this->objects[$name] = [];
            $this->hashes[$name] = []; // init styles
        }
        foreach($this->styles as $style) {
            $style->setStyleIndex(0);
            foreach($namedcollections as $name) {
                $method = "get".ucfirst($name);
                $this->processAddToArray($name, $style->$method());
            }
        }
    }

    protected function xmlCollection($name, $tag)
    {
        return '<'.$tag.' count="'.count($this->objects[$name]).'">'
            .array_reduce($this->objects[$name], function($r, Styles\StyleInterface $generic) {
                return $r.$generic->asXML();
            })
            .'</'.$tag.'>'
        ;
    }

    protected function xmlCellStylesXF()
    {
        return '<cellStyleXfs count="1">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>'
            .'</cellStyleXfs>'
        ;
    }

    protected function xmlCellXfs()
    {
        $i = 0;
        return '<cellXfs count="'.count($this->styles).'">'
            .array_reduce($this->styles, function($r, Style $style) use (&$i) {
                // // if no values then do not include
                // if (!$style->hasValues()) return $r;
                $style->setStyleIndex($i);
                $i = $i + 1;
                return $r.$style->asXML();
            })
            .'</cellXfs>'
        ;
    }

    public function asXML()
    {
        $this->processStyles();
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .$this->xmlCollection("format", "numFmts")
            .$this->xmlCollection("font", "fonts")
            .$this->xmlCollection("fill", "fills")
            //.$this->xmlCollection("border", "borders")
            .'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            .$this->xmlCellStylesXF()
            .$this->xmlCellXfs()
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'<dxfs count="0"/>'
            .'</styleSheet>'
            ;
    }

    public function getStyles()
    {
        return $this->styles;
    }
}
