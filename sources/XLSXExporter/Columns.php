<?php

namespace XLSXExporter;

class Columns extends AbstractCollection
{
    /**
     * @param Column $item
     * @throws XLSXException
     */
    public function add($item)
    {
        $this->addItem($item, ! $this->isValidInstance($item) ? null : $item->getId());
    }

    public function isValidInstance($item)
    {
        return ($item instanceof Column);
    }
}
