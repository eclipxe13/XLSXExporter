<?php
namespace XLSXExporter;

/**
 * Class to access the style specification
 * @property Styles\Alignment $alignment
 * @property Styles\Border $border
 * @property Styles\Fill $fill
 * @property Styles\Font $font
 * @property Styles\Format $format
 * @property Styles\Protection $protection
 * @method Styles\Alignment getAlignment()
 * @method Styles\Border getBorder()
 * @method Styles\Fill getFill()
 * @method Styles\Font getFont()
 * @method Styles\Format getFormat()
 * @method Styles\Protection getProtection()
 * @method Style setAlignment(Styles\Alignment $value)
 * @method Style setBorder(Styles\Border $value)
 * @method Style setFill(Styles\Fill $value)
 * @method Style setFont(Styles\Font $value)
 * @method Style setFormat(Styles\Format $value)
 * @method Style setProtection(Styles\Protection $value)
 */
class Style
{
    protected $styleindex;

    /** @var Styles\StyleInterface[] */
    protected $members = [
        'format' => null,
        'font' => null,
        'fill' => null,
        'alignment' => null,
        'border' => null,
        'protection' => null,
    ];

    public function __construct(array $arrayStyles = null)
    {
        foreach (array_keys($this->members) as $stylename) {
            $styleclass = '\XLSXExporter\Styles\\' . ucfirst($stylename);
            $this->$stylename = new $styleclass();
        }
        if (null !== $arrayStyles) {
            $this->setFromArray($arrayStyles);
        }
    }

    public function __get($name)
    {
        if (! array_key_exists($name, $this->members)) {
            throw new \LogicException("Invalid property name $name");
        }
        return $this->members[$name];
    }

    public function __set($name, $value)
    {
        if (! array_key_exists($name, $this->members)) {
            throw new \LogicException("Invalid property name $name");
        }
        $styleclass = '\XLSXExporter\Styles\\' . ucfirst($name);
        if (! $value instanceof $styleclass) {
            throw new \LogicException("The value must be an instance of $styleclass");
        }
        return $this->members[$name] = $value;
    }

    public function __call($name, $arguments)
    {
        $getter = (0 === strpos($name, 'get'));
        $setter = (0 === strpos($name, 'set'));
        if ($getter || $setter) {
            $name = lcfirst(substr($name, 3));
        } elseif (! $getter && ! $setter) {
            throw new \LogicException("Invalid method name $name");
        }
        if (! array_key_exists($name, $this->members)) {
            throw new \LogicException("Invalid setter/getter name $name");
        }
        if ($getter) {
            return $this->$name;
        }
        if (1 != count($arguments)) {
            throw new \LogicException('Invalid setter argument');
        }
        $this->$name = $arguments[0];
        return $this;
    }

    public function getMemberNames()
    {
        return array_keys($this->members);
    }

    /**
     * Set styles from an array of key-values
     * Keys: format, font, fill, alignment, border, protection
     * @param array $array
     * @return \XLSXExporter\Style
     */
    public function setFromArray(array $array)
    {
        if (! count($array)) {
            return $this;
        }
        foreach ($this->members as $key => $style) {
            if (array_key_exists($key, $array) && is_array($array[$key])) {
                $style->setValues($array[$key]);
            }
        }
        return $this;
    }

    /**
     * Check if any of the styles has valid values
     * @return bool
     */
    public function hasValues()
    {
        foreach ($this->members as $style) {
            if ($style->hasValues()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param int $index
     * @return \XLSXExporter\Style
     */
    public function setStyleIndex($index)
    {
        $this->styleindex = $index;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getStyleIndex()
    {
        return $this->styleindex;
    }

    /**
     * Method to return the XML xf node
     * This method could be outside this object
     *
     * @param int $xfId
     * @return string
     * @access private
     */
    public function asXML($xfId = 0)
    {
        // all the apply is set to not inherit the value from cellStyleXfs
        return '<xf'
            . ' numFmtId="' . intval($this->format->id) . '"'
            . ' fontId="' . $this->font->getIndex() . '"'
            . ' fillId="' . $this->fill->getIndex() . '"'
            . ' borderId="' . $this->border->getIndex() . '"'
            . ' xfId="' . (($xfId) ? : 0) . '"' // all is based on cellStyleXfs[0] by default
            . ' applyNumberFormat="false"'
            . ' applyFont="false"'
            . ' applyFill="false"'
            . ' applyBorder="false"'
            . ' applyAlignment="false"'
            . ' applyProtection="false"'
            . '>'
            . (($this->alignment->hasValues()) ? $this->alignment->asXML() : '')
            . (($this->protection->hasValues()) ? $this->protection->asXML() : '')
            . '</xf>'
        ;
    }
}
