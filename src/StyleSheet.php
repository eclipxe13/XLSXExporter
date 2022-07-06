<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter;

use InvalidArgumentException;

class StyleSheet
{
    /** @var Style[] */
    protected array $styles;

    /** @var array<string, array<int, string>> */
    protected array $hashes = [];

    /** @var array<string, array<int, Styles\StyleInterface>> */
    protected $objects;

    public function __construct(Style ...$styles)
    {
        if ([] === $styles) {
            throw new InvalidArgumentException('Error creating the stylesheet, no styles found');
        }
        // mandatory styles
        $this->styles = array_merge($this->mandatoryStyles(), array_values($styles));
    }

    /**
     * The mandatory styles, must be declared before any other style
     *
     * @return array<int, Style>
     */
    protected function mandatoryStyles(): array
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

    protected function processStylesFormat(): void
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
                if (! in_array($format->code, $codes, true)) {
                    $codes[$format->id] = $format->code;
                    $this->objects['format'][] = $format;
                }
            } elseif (false !== $numfmtid = array_search($format->code, $codes, true)) {
                $format->id = $numfmtid;
            } else {
                $format->id = $fmtcounter;
                $fmtcounter = $fmtcounter + 1;
                $codes[$format->id] = $format->code;
                $this->objects['format'][] = $format;
            }
        }
    }

    protected function processAddToArray(string $name, Styles\StyleInterface $generic): void
    {
        $generic->setIndex(0);
        if ($generic->hasValues()) {
            $hash = $generic->getHash();
            if (false === $index = array_search($hash, $this->hashes[$name], true)) {
                $index = count($this->hashes[$name]);
                $this->hashes[$name][] = $hash;
                $this->objects[$name][] = $generic;
            }
            $generic->setIndex($index);
        }
    }

    protected function processStyles(): void
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

    protected function xmlCollection(string $name, string $tag): string
    {
        return '<' . $tag . ' count="' . count($this->objects[$name]) . '">'
            . array_reduce($this->objects[$name], fn ($return, Styles\StyleInterface $generic): string => $return . $generic->asXML())
            . '</' . $tag . '>'
        ;
    }

    protected function xmlCellStylesXF(): string
    {
        return '<cellStyleXfs count="1">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>'
            . '</cellStyleXfs>'
        ;
    }

    protected function xmlCellXfs(): string
    {
        $index = 0;
        return '<cellXfs count="' . count($this->styles) . '">'
            . array_reduce($this->styles, function ($return, Style $style) use (&$index): string {
                $style->setStyleIndex($index);
                $index = $index + 1;
                return $return . $style->asXML();
            })
            . '</cellXfs>'
        ;
    }

    /**
     * Return the content of the style sheet in xml format
     */
    public function asXML(): string
    {
        $this->processStyles();
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . $this->xmlCollection('format', 'numFmts')
            . $this->xmlCollection('font', 'fonts')
            . $this->xmlCollection('fill', 'fills')
            // . $this->xmlCollection("border", "borders")
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
    public function getStyles(): array
    {
        return $this->styles;
    }
}
