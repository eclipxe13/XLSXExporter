<?php

namespace XLSXExporter;

use XLSXExporter\Styles\Format;
use XLSXExporter\Styles\Font;
use XLSXExporter\Styles\Fill;
use XLSXExporter\Styles\Alignment;
use XLSXExporter\Styles\Border;
use XLSXExporter\Styles\Protection;

class Style
{

    protected $styleindex;

    /** @var Format */
    protected $format;
    /** @var Font */
    protected $font;
    /** @var Fill */
    protected $fill;
    /** @var Alignment */
    protected $alignment;
    /** @var Border */
    protected $border;
    /** @var Protection */
    protected $protection;

    public function __construct($arrayStyles = null)
    {
        $this->format = new Format();
        $this->font = new Font();
        $this->fill = new Fill();
        $this->alignment = new Alignment();
        $this->border = new Border();
        $this->protection = new Protection();
        if (is_array($arrayStyles) and count($arrayStyles)) {
            $this->setFromArray($arrayStyles);
        }
    }

    public function setFromArray(array $array)
    {
        $keys = ["format", "font", "fill", "alignment", "border", "protection"];
        foreach($keys as $key) {
            if (array_key_exists($key, $array) and is_array($array[$key])) {
                $this->$key->setValues($array[$key]);
            }
        }
        return $this;
    }

    /**
     * @return Format
     */
    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat(Format $format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return Font
     */
    public function getFont()
    {
        return $this->font;
    }

    public function setFont(Font $font)
    {
        $this->font = $font;
        return $this;
    }


    /**
     * @return Fill
     */
    public function getFill()
    {
        return $this->fill;
    }

    public function setFill(Fill $fill)
    {
        $this->fill = $fill;
        return $this;
    }

    /**
     * @return Alignment
     */
    public function getAlignment()
    {
        return $this->alignment;
    }

    public function setAlignment(Alignment $alignment)
    {
        $this->alignment = $alignment;
        return $this;
    }

    /**
     * @return Border
     */
    public function getBorder()
    {
        return $this->border;
    }

    public function setBorder(Border $border)
    {
        $this->border = $border;
        return $this;
    }

    /**
     * @return Protection
     */
    public function getProtection()
    {
        return $this->protection;
    }

    public function setProtection(Protection $protection)
    {
        $this->protection = $protection;
        return $this;
    }

    public function hasValues()
    {
        return $this->format->hasValues()
            or $this->font->hasValues()
            or $this->alignment->hasValues()
            or $this->fill->hasValues()
            or $this->border->hasValues()
            or $this->protection->hasValues()
            ;
    }

   public function setStyleIndex($index)
    {
        $this->styleindex = $index;
        return $this;
    }

    public function getStyleIndex()
    {
        return $this->styleindex;
    }

    public function asXML($xfId = 0)
    {
        // all the apply is set to not inherit the value from cellStyleXfs
        return '<xf'
            .' numFmtId="'.intval($this->format->id).'"'
            .' fontId="'.$this->font->getIndex().'"'
            .' fillId="'.$this->fill->getIndex().'"'
            .' borderId="'.$this->border->getIndex().'"'
            .' xfId="'.(($xfId) ? : 0).'"' // all is based on cellStyleXfs[0] by default
            .' applyNumberFormat="false"'
            .' applyFont="false"'
            .' applyFill="false"'
            .' applyBorder="false"'
            .' applyAlignment="false"'
            .' applyProtection="false"'
            .'>'
            .(($this->alignment->hasValues()) ? $this->alignment->asXML() : '')
            .(($this->protection->hasValues()) ? $this->protection->asXML() : '')
            .'</xf>'
        ;
    }

}