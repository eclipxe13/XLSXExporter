<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Exceptions;

use LogicException;

final class ItemNotFoundException extends LogicException implements XLSXException
{
    /** @var scalar */
    private $search;

    /** @param scalar $search */
    public function __construct(string $message, $search)
    {
        parent::__construct($message);
        $this->search = $search;
    }

    /** @return scalar */
    public function getSearch()
    {
        return $this->search;
    }
}
