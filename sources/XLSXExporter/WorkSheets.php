<?php

namespace XLSXExporter;

class WorkSheets extends AbstractCollection
{
    /**
     * Add a WorkSheet to this collection
     *
     * @param WorkSheet $item
     * @throws XLSXException
     */
    public function add($item)
    {
        if (! $item instanceof WorkSheet) {
            throw new XLSXException('Invalid WorkSheet object');
        }
        $this->collection[] = $item;
    }

    /**
     * Return the repeated worksheet names
     *
     * @return array
     */
    public function retrieveRepeatedNames()
    {
        $names = [];
        $repeated = [];
        foreach ($this->collection as $worksheet) {
            /* @var $worksheet WorkSheet */
            if (! in_array($worksheet->getName(), $names)) {
                $names[] = $worksheet->getName();
                continue;
            }
            $repeated[] = $worksheet->getName();
        }
        return array_unique($repeated);
    }

    /**
     * @param string $id
     * @param WorkSheet $item
     * @return bool
     */
    protected function elementMatchId($id, $item)
    {
        return ($id == $item->getName());
    }
}
