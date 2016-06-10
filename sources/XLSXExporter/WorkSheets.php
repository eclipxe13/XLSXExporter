<?php

namespace XLSXExporter;

class WorkSheets extends AbstractCollection
{

    /**
     * @param WorkSheet $item
     * @throws XLSXException
     */
    public function add($item)
    {
        $this->addItem($item, !$this->isValidInstance($item) ? null : $item->getName());
    }

    public function isValidInstance($item)
    {
        return ($item instanceof WorkSheet);
    }
}
