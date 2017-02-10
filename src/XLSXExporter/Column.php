<?php
namespace XLSXExporter;

/**
 * @property-read string $id Column identifier
 * @property string $type Type of cell
 * @property string $title Column title
 * @property Style $style Style object
 */
class Column
{
    protected $id;
    protected $type;
    protected $title;

    /**
     * @var Style
     */
    protected $style;

    public function __construct($id, $title = '', $type = CellTypes::TEXT, $style = null)
    {
        $this->setId($id);
        $this->setType($type);
        $this->setTitle($title);
        if (! ($style instanceof Style)) {
            $style = new Style();
        }
        $this->setStyle($style);
    }

    public function __get($name)
    {
        $props = ['id', 'type', 'title', 'style'];
        if (! in_array($name, $props)) {
            throw new XLSXException("Invalid property name $name");
        }
        $method = 'get' . ucfirst($name);
        return $this->$method();
    }

    public function __set($name, $value)
    {
        $props = ['type', 'title', 'style'];
        if (! in_array($name, $props)) {
            throw new XLSXException("Invalid property name $name");
        }
        $method = 'set' . ucfirst($name);
        $this->$method($value);
    }

    protected function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = (string) $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Style $style
     */
    public function setStyle(Style $style)
    {
        $this->style = $style;
    }

    /**
     * @return Style
     */
    public function getStyle()
    {
        return $this->style;
    }
}
