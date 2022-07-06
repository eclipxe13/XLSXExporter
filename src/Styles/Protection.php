<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Styles;

/**
 * @property bool $hidden
 * @property bool $locked
 */
class Protection extends AbstractStyle
{
    protected function properties(): array
    {
        return [
            'hidden',
            'locked',
        ];
    }

    public function asXML(): string
    {
        return '<protection'
            . ' locked="' . (($this->locked) ? '1' : '0') . '"'
            . ' hidden="' . (($this->hidden) ? '1' : '0') . '"'
            . '/>'
        ;
    }

    /** @param static|null $value */
    protected function castLocked($value): bool
    {
        return (bool) $value;
    }

    /** @param static|null $value */
    protected function castHidden($value): bool
    {
        return (bool) $value;
    }
}
