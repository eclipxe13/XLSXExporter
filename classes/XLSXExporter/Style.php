<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace XLSXExporter;

use XLSXExporter\Styles\Format;
use XLSXExporter\Styles\Font;
use XLSXExporter\Styles\Fill;
use XLSXExporter\Styles\Alignment;
use XLSXExporter\Styles\Border;

class Style
{

    protected $stylename;
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

    public function __construct()
    {
        $this->format = new Format();
        $this->font = new Font();
        $this->fill = new Fill();
        $this->alignment = new Alignment();
        $this->border = new Border();
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

    public function hasValues()
    {
        return $this->format->hasValues()
            or $this->font->hasValues()
            or $this->alignment->hasValues()
            or $this->fill->hasValues()
            or $this->border->hasValues()
            ;
    }

     public function getStyleName()
    {
        return $this->stylename;
    }

    public function setStyleName($stylename)
    {
        $this->stylename = $stylename;
        return $this;
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

    public function asXML($xfid = null)
    {
        return '<xf'
            .((null !== $xfid) ? ' xfId="'.$xfid.'"' : '')
            .' numFmtId="'.$this->format->getIndex().'"'
            .' fontId="'.$this->font->getIndex().'"'
            .' borderId="'.$this->border->getIndex().'"'
            .' fillId="'.$this->fill->getIndex().'"'
            .' applyAlignment="'.(($this->alignment->hasValues()) ? '1' : '0').'"'
            .' applyBorder="'.(($this->border->hasValues()) ? '1' : '0').'"'
            .' applyFill="'.(($this->fill->hasValues()) ? '1' : '0').'"'
            .' applyFont="'.(($this->font->hasValues()) ? '1' : '0').'"'
            .' applyNumberFormat="'.(($this->format->hasValues()) ? '1' : '0').'"'
            .' applyProtection="1"'
            .'>'
            .(($this->alignment->hasValues()) ? $this->alignment->asXML() : '')
            .'<protection locked="1" hidden="0"/>'
            .'</xf>'
        ;
    }

}