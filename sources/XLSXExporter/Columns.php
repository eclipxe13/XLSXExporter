<?php

namespace XLSXExporter;

class Columns extends AbstractCollection
{
    /**
     * Add a column to this collection
     *
     * @param Column $item
     * @throws XLSXException
     */
    public function add($item)
    {
        if (! $item instanceof Column) {
            throw new XLSXException('Invalid Column object');
        }
        $this->collection[] = $item;
    }

    /**
     * @param string $id
     * @param Column $item
     * @return bool
     */
    protected function elementMatchId($id, $item)
    {
        return ($id == $item->getId());
    }
}
