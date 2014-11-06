<?php

namespace XLSXExporter;

class WorkSheets extends AbstractCollection
{

    public function add($item)
    {
        $this->addItem($item, !$this->isValidInstance($item) ? null : $item->getName());
    }


    public static function isValidInstance($item)
    {
        return ($item instanceof WorkSheet);
    }

}