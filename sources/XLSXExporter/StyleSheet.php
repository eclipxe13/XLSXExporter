<?php

namespace XLSXExporter;

class StyleSheet
{
    /** @var Style[] */
    protected $styles;
    /** @var array */
    protected $hashes;
    /** @var array */
    protected $objects;

    /**
     * StyleSheet constructor.
     *
     * @param Style[] $styles
     * @throws XLSXException Error creating the stylesheet, no styles found
     * @throws XLSXException Error creating the stylesheet, received an invalid style object
     */
    public function __construct(array $styles)
    {
        if (! count($styles)) {
            throw new XLSXException('Error creating the stylesheet, no styles found');
        }
        foreach ($styles as $style) {
            if (! ($style instanceof Style)) {
                throw new XLSXException('Error creating the stylesheet, received an invalid style object');
            }
        }
        // mandatory styles
        $this->styles = array_merge($this->mandatoryStyles(), $styles);
    }

    /**
     * The mandatory styles, must be declared before any other style
     *
     * @return Style[]
     */
    protected function mandatoryStyles()
    {
        return [
            new Style([
                'format' => [
                    'code' => Styles\Format::FORMAT_GENERAL,
                ],
                'fill' => [
                    'pattern' => Styles\Fill::NONE,
                ],
                'alignment' => [
                    'horizontal' => Styles\Alignment::HORIZONTAL_GENERAL,
                    'vertical' => Styles\Alignment::VERTICAL_BOTTOM,
                ],
            ]),
            new Style([
                'fill' => [
                    'pattern' => Styles\Fill::GRAY125,
                ],
            ]),
        ];
    }

    protected function processStylesFormat()
    {
        $codes = [];
        $fmtcounter = 164;
        $this->objects['format'] = [];
        $this->hashes['format'] = [];
        foreach ($this->styles as $style) {
            $format = $style->getFormat();
            if (! $format->hasValues()) {
                $format->code = Styles\Format::FORMAT_GENERAL;
            }
            if (false !== $builtin = $format->getBuiltInCodeIdByCode($format->code)) {
                $format->id = $builtin;
                if (! in_array($format->code, $codes)) {
                    $codes[$format->id] = $format->code;
                    array_push($this->objects['format'], $format);
                }
            } elseif (false !== $numfmtid = array_search($format->code, $codes)) {
                $format->id = $numfmtid;
            } else {
                $format->id = $fmtcounter;
                $fmtcounter = $fmtcounter + 1;
                $codes[$format->id] = $format->code;
                array_push($this->objects['format'], $format);
            }
        }
    }

    protected function processAddToArray($name, Styles\StyleInterface $generic)
    {
        $generic->setIndex(0);
        if ($generic->hasValues()) {
            $hash = $generic->getHash();
            if (false === $index = array_search($hash, $this->hashes[$name], true)) {
                $index = count($this->hashes[$name]);
                array_push($this->hashes[$name], $hash);
                array_push($this->objects[$name], $generic);
            }
            $generic->setIndex($index);
        }
    }

    protected function processStyles()
    {
        // different process for "format"
        $this->processStylesFormat();
        // same process for "font", "fill" and "border"
        $namedcollections = ['font', 'fill', 'border'];
        foreach ($namedcollections as $name) {
            $this->objects[$name] = [];
            $this->hashes[$name] = []; // init styles
        }
        foreach ($this->styles as $style) {
            $style->setStyleIndex(0);
            foreach ($namedcollections as $name) {
                $method = 'get' . ucfirst($name);
                $this->processAddToArray($name, $style->$method());
            }
        }
    }

    /**
     * @param string $name
     * @param string $tag
     * @return string
     */
    protected function xmlCollection($name, $tag)
    {
        return '<' . $tag . ' count="' . count($this->objects[$name]) . '">'
            . array_reduce($this->objects[$name], function ($return, Styles\StyleInterface $generic) {
                return $return . $generic->asXML();
            })
            . '</' . $tag . '>'
        ;
    }

    /**
     * @return string
     */
    protected function xmlCellStylesXF()
    {
        return '<cellStyleXfs count="1">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>'
            . '</cellStyleXfs>'
        ;
    }

    /**
     * @return string
     */
    protected function xmlCellXfs()
    {
        $index = 0;
        return '<cellXfs count="' . count($this->styles) . '">'
            . array_reduce($this->styles, function ($return, Style $style) use (&$index) {
                $style->setStyleIndex($index);
                $index = $index + 1;
                return $return . $style->asXML();
            })
            . '</cellXfs>'
        ;
    }

    /**
     * Return the content of the style sheet in xml format
     *
     * @return string
     */
    public function asXML()
    {
        $this->processStyles();
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . $this->xmlCollection('format', 'numFmts')
            . $this->xmlCollection('font', 'fonts')
            . $this->xmlCollection('fill', 'fills')
            //.$this->xmlCollection("border", "borders")
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . $this->xmlCellStylesXF()
            . $this->xmlCellXfs()
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '<dxfs count="0"/>'
            . '</styleSheet>'
        ;
    }

    /**
     * Styles collection
     * @return Style[]
     */
    public function getStyles()
    {
        return $this->styles;
    }
}
