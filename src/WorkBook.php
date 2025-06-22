<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter;

use Eclipxe\XlsxExporter\Exceptions\InvalidPropertyNameException;
use Eclipxe\XlsxExporter\Exceptions\TemporaryFileException;
use Eclipxe\XlsxExporter\Exceptions\WorkBookCreateZipFileException;
use Eclipxe\XlsxExporter\Exceptions\WorkBookWithoutWorkSheetsException;
use Eclipxe\XlsxExporter\Exceptions\WorkBookWithRepeatedNamesException;
use Eclipxe\XlsxExporter\Utils\TemporaryFile;
use Eclipxe\XlsxExporter\Utils\TemporaryFiles;
use EngineWorks\ProgressStatus\NullProgress;
use EngineWorks\ProgressStatus\ProgressInterface;
use ZipArchive;

/**
 *
 *
 * @property-read Style $style Default style for the whole document
 * @property-read WorkSheets $worksheets Collections of worksheets
 * @property-read ProgressInterface $globalProgress
 * @property-read ProgressInterface $detailedProgress
 */
class WorkBook
{
    protected WorkSheets $worksheets;

    protected Style $style;

    protected ProgressInterface $globalProgress;

    protected ProgressInterface $detailedProgress;

    /**
     * WorkBook constructor.
     *
     * @param WorkSheets|null $worksheets
     * @param Style|null $style
     * @param ProgressInterface|null $globalProgress
     * @param ProgressInterface|null $detailedProgress
     */
    public function __construct(
        ?WorkSheets $worksheets = null,
        ?Style $style = null,
        ?ProgressInterface $globalProgress = null,
        ?ProgressInterface $detailedProgress = null
    ) {
        $this->worksheets = $worksheets ?? new WorkSheets();
        $this->style = $style ?? BasicStyles::defaultStyle();
        $this->setGlobalProgress($globalProgress ?? new NullProgress());
        $this->setDetailedProgress($detailedProgress ?? new NullProgress());
    }

    /** @return mixed */
    public function __get(string $name)
    {
        if ('worksheets' === $name) {
            return $this->worksheets;
        }
        if ('style' === $name) {
            return $this->style;
        }
        if ('globalProgress' === $name) {
            return $this->globalProgress;
        }
        if ('detailedProgress' === $name) {
            return $this->detailedProgress;
        }
        throw new InvalidPropertyNameException($name);
    }

    public function getWorkSheets(): WorkSheets
    {
        return $this->worksheets;
    }

    public function getGlobalProgress(): ProgressInterface
    {
        return $this->globalProgress;
    }

    public function getDetailedProgress(): ProgressInterface
    {
        return $this->detailedProgress;
    }

    public function setGlobalProgress(ProgressInterface $globalProgress): void
    {
        $this->globalProgress = $globalProgress;
    }

    public function setDetailedProgress(ProgressInterface $detailedProgress): void
    {
        $this->detailedProgress = $detailedProgress;
    }

    /**
     * Write the workbook and return the temporary filename of the created file
     *
     * @throws WorkBookWithoutWorkSheetsException
     * @throws WorkBookWithRepeatedNamesException
     * @throws WorkBookCreateZipFileException
     * @throws TemporaryFileException
     */
    public function write(TemporaryFile $tempfile): void
    {
        // check that there are worksheets
        if ($this->worksheets->isEmpty()) {
            throw new WorkBookWithoutWorkSheetsException('Workbook does not contains any worksheet');
        }
        // check that every sheet has a different name
        $repeatedNames = $this->worksheets->retrieveRepeatedNames();
        if ([] !== $repeatedNames) {
            throw new WorkBookWithRepeatedNamesException($repeatedNames);
        }
        // validations end, create the file
        $globalProgress = $this->getGlobalProgress();
        $detailedProgress = $this->getDetailedProgress();
        $globalProgress->update('Building structures...', 0, 2 + $this->worksheets->count());
        $temporaryFiles = new TemporaryFiles();
        $zip = new ZipArchive();
        $zipOpenResult = $zip->open($tempfile->getPath(), ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if (true !== $zipOpenResult) {
            throw new WorkBookCreateZipFileException($tempfile->getPath(), $zipOpenResult);
        }
        // folders
        $zip->addEmptyDir('xl/');
        $zip->addEmptyDir('xl/_rels/');
        $zip->addEmptyDir('_rels/');
        $zip->addEmptyDir('xl/worksheets/');
        // simple files
        $zip->addFromString('_rels/.rels', $this->xmlRels());
        $zip->addFromString('[Content_Types].xml', $this->xmlContentTypes());
        $zip->addFromString('xl/styles.xml', $this->xmlStyles());
        $zip->addFromString('xl/workbook.xml', $this->xmlWorkbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->xmlWorkbookRels());
        // create the sharedStrings object because worksheets use it
        $sharedStrings = new SharedStrings();
        $workSheetIndex = 1;
        /** @var WorkSheet $worksheet */
        foreach ($this->worksheets as $worksheet) {
            $globalProgress->update(sprintf('Add worksheet %s...', $worksheet->getName()), $workSheetIndex);
            // write and include the sheet
            $workSheetFile = $temporaryFiles->create('ws-');
            $worksheet->write($workSheetFile, $sharedStrings, $detailedProgress);
            $zip->addFile($workSheetFile->getPath(), $this->workSheetFilePath($workSheetIndex));
            $workSheetIndex = $workSheetIndex + 1;
        }
        // include the shared strings file
        $globalProgress->update('Add shared strings...', $workSheetIndex);
        $sharedStringsFile = new TemporaryFile('ss-');
        $sharedStrings->write($sharedStringsFile, $detailedProgress);
        $zip->addFile($sharedStringsFile->getPath(), 'xl/sharedStrings.xml');
        // files must exist when closing the zip file
        $zip->close();
        $globalProgress->update('Done', $workSheetIndex + 1);
        $temporaryFiles->clear();
    }

    protected function workSheetFilePath(int $index, string $prefix = 'xl/'): string
    {
        return $prefix . 'worksheets/sheet' . $index . '.xml';
    }

    protected function xmlRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="wb1"'
            . ' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument"'
            . ' Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    protected function xmlContentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml"'
            . ' ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml"'
            . ' ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/xl/sharedStrings.xml"'
            . ' ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
            . array_reduce(
                range(1, $this->worksheets->count()),
                fn ($return, $index): string => $return
                    . '<Override PartName="/' . $this->workSheetFilePath($index)
                    . '" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            )
            . '</Types>';
    }

    protected function xmlStyles(): string
    {
        $styles = [$this->style];
        foreach ($this->worksheets as $worksheet) {
            // add worksheet header style
            $styles[] = $worksheet->getHeaderStyle();
            /** @var Column $column */
            foreach ($worksheet->getColumns() as $column) {
                // add worksheet column style
                $styles[] = $column->getStyle();
            }
        }
        $stylesheet = new StyleSheet(...$styles);
        return $stylesheet->asXML();
    }

    protected function xmlWorkbook(): string
    {
        $index = 0;
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<bookViews><workbookView/></bookViews>' // without the bookviews the user in MS Excel cannot copy & paste
            . '<sheets>'
            . array_reduce($this->worksheets->all(), function ($return, WorkSheet $worksheet) use (&$index): string {
                $index = $index + 1;
                return $return . sprintf('<sheet name="%s" sheetId="%s" r:id="rId%s"/>', $worksheet->getName(), $index, $index);
            })
            . '</sheets>'
            . '</workbook>'
        ;
    }

    protected function xmlWorkbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . array_reduce(
                range(1, $this->worksheets->count()),
                fn ($return, $index): string => $return . '<Relationship Id="rId' . $index . '"'
                    . ' Target="' . $this->workSheetFilePath($index, '') . '"'
                    . ' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"/>'
            )
            . '<Relationship Id="stl1" Target="styles.xml"'
            . ' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"/>'
            . '<Relationship Id="shs1" Target="sharedStrings.xml"'
            . ' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings"/>'
            . '</Relationships>'
        ;
    }
}
