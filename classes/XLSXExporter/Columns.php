<?php

namespace XLSXExporter;

class Columns extends AbstractCollection
{
    public function add($item)
    {
        $this->addItem($item, !$this->isValidInstance($item) ? null : $item->getId());
    }

    public static function isValidInstance($item)
    {
        return ($item instanceof Column);
    }

}