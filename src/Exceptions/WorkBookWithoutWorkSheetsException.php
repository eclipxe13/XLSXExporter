<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Exceptions;

use LogicException;

final class WorkBookWithoutWorkSheetsException extends LogicException implements XLSXException
{
}
