<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter;

use Eclipxe\XLSXExporter\Exceptions\InvalidDestinationFileException;
use Eclipxe\XLSXExporter\Exceptions\TemporaryFileException;
use Eclipxe\XLSXExporter\Exceptions\WorkBookCreateZipFileException;
use Eclipxe\XLSXExporter\Exceptions\WorkBookWithoutWorkSheetsException;
use Eclipxe\XLSXExporter\Exceptions\WorkBookWithRepeatedNamesException;
use Eclipxe\XLSXExporter\Utils\TemporaryFile;

class XLSXExporter
{
    /**
     * Save the workbook as a specified file
     * This is a shortcut for write, copy and unlink
     *
     * @throws WorkBookWithoutWorkSheetsException
     * @throws WorkBookWithRepeatedNamesException
     * @throws WorkBookCreateZipFileException
     * @throws TemporaryFileException
     * @throws InvalidDestinationFileException
     */
    public static function save(WorkBook $workbook, string $filename): void
    {
        if (file_exists($filename) && is_dir($filename)) {
            throw InvalidDestinationFileException::isDirectory($filename);
        }
        if (! file_exists($filename) && ! is_dir(dirname($filename))) {
            throw InvalidDestinationFileException::existsAndParentisNotDirectory($filename);
        }
        $temporaryFile = new TemporaryFile('xslx-');
        $workbook->write($temporaryFile);
        $temporaryFile->copy($filename);
    }

    /**
     * Pass-tru the workbook and write the Content-type header
     * This is a shortcut for write, header, passtru and unlink
     *
     * @param WorkBook $workbook
     *
     * @throws WorkBookWithoutWorkSheetsException
     * @throws WorkBookWithRepeatedNamesException
     * @throws WorkBookCreateZipFileException
     * @throws TemporaryFileException
     */
    public static function passtru(WorkBook $workbook): void
    {
        $temporaryFile = new TemporaryFile('xslx-');
        $workbook->write($temporaryFile);
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $temporaryFile->passthru();
    }
}
