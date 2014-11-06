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
        $this->styles = $styles;
    }

    protected function processAddToArray($name, Styles\AbstractStyle $generic)
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
        $namedcollections = ["format", "font", "fill", "border"];
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
            .array_reduce($this->objects[$name], function($r,Styles\AbstractStyle $generic) {
                return $r.$generic->asXML();
            })
            .'</'.$tag.'>'
        ;
    }

    protected function xmlCellStylesXF()
    {
        $i = 0;
        return '<cellStyleXfs count="'.count($this->styles).'">'
            .array_reduce($this->styles, function($r, Style $style) use (&$i) {
//                if (!$style->hasValues()) return $r;
                $style->setStyleIndex($i);
                $i = $i + 1;
                return $r.$style->asXML();

            })
            .'</cellStyleXfs>'
        ;
    }

    protected function xmlCellXfs()
    {
        $i = -1;
        return '<cellXfs count="'.count($this->styles).'">'
            .array_reduce($this->styles, function($r, Style $style) use (&$i) {
//                if (!$style->hasValues()) return $r;
                $i = $i + 1;
                return $r.$style->asXML($i);

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
            .'</styleSheet>'
            ;
    }

    public function getStyles()
    {
        return $this->styles;
    }
}
