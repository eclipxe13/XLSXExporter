<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Exceptions;

use LogicException;

final class WorkBookWithoutWorkSheetsException extends LogicException implements XlsxException
{
}
