<?php

namespace XLSXExporter\Styles;

/**
 * @property bool $hidden
 * @property bool $locked
 */
class Protection extends AbstractStyle
{
    protected function properties()
    {
        return [
            'hidden',
            'locked',
        ];
    }

    public function asXML()
    {
        return '<protection'
            . ' locked="' . (($this->locked) ? '1' : '0') . '"'
            . ' hidden="' . (($this->hidden) ? '1' : '0') . '"'
            . '/>'
        ;
    }

    protected function castLocked($value)
    {
        return (bool) $value;
    }

    protected function castHidden($value)
    {
        return (bool) $value;
    }
}
