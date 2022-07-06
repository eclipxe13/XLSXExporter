<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Exceptions;

use RuntimeException;

final class WorkBookWithRepeatedNamesException extends RuntimeException implements XLSXException
{
    /** @var string[] */
    private array $repeatedNames;

    /** @param string[] $repeatedNames */
    public function __construct(array $repeatedNames)
    {
        parent::__construct(sprintf('Workbook has worksheets with the same name: %s', implode(',', $repeatedNames)));
        $this->repeatedNames = $repeatedNames;
    }

    /** @return string[] */
    public function getRepeatedNames(): array
    {
        return $this->repeatedNames;
    }
}
