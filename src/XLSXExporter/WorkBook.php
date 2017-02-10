<?php
namespace XLSXExporter;

use EngineWorks\ProgressStatus\NullProgress;
use EngineWorks\ProgressStatus\ProgressInterface;
use ZipArchive;

/**
 * @property-read Style $style Default style for the whole document
 * @property-read WorkSheets|WorkSheet[] $worksheets Collections of worksheets
 */
class WorkBook
{
    /** @var WorkSheets */
    protected $worksheets;

    /** @var Style default base style */
    protected $style;

    /** @var ProgressInterface */
    protected $globalProgress;

    /** @var ProgressInterface */
    protected $detailedProgress;

    /**
     * WorkBook constructor.
     *
     * @param WorkSheets|null $worksheets
     * @param Style|null $style
     * @param ProgressInterface $globalProgress
     * @param ProgressInterface $detailedProgress
     */
    public function __construct(
        WorkSheets $worksheets = null,
        Style $style = null,
        ProgressInterface $globalProgress = null,
        ProgressInterface $detailedProgress = null
    ) {
        $this->worksheets = ($worksheets) ? : new WorkSheets();
        $this->style = ($style) ? : BasicStyles::defaultStyle();
        $this->setGlobalProgress($globalProgress ? : new NullProgress());
        $this->setDetailedProgress($detailedProgress ? : new NullProgress());
    }

    public function __get($name)
    {
        // read-only properties
        $props = ['worksheets', 'style', 'globalProgress', 'detailedProgress'];
        if (! in_array($name, $props)) {
            throw new XLSXException("Invalid property name $name");
        }
        $method = 'get' . ucfirst($name);
        return $this->$method();
    }

    /**
     * @return WorkSheet[]|WorkSheets
     */
    public function getWorkSheets()
    {
        return $this->worksheets;
    }

    /**
     * @return ProgressInterface
     */
    public function getGlobalProgress()
    {
        return $this->globalProgress;
    }

    /**
     * @return ProgressInterface
     */
    public function getDetailedProgress()
    {
        return $this->detailedProgress;
    }

    /**
     * @param ProgressInterface $globalProgress
     */
    public function setGlobalProgress(ProgressInterface $globalProgress)
    {
        $this->globalProgress = $globalProgress;
    }

    /**
     * @param ProgressInterface $detailedProgress
     */
    public function setDetailedProgress(ProgressInterface $detailedProgress)
    {
        $this->detailedProgress = $detailedProgress;
    }

    /**
     * Write the workbook and return the temporary filename of the created file
     *
     * @return string
     * @throws XLSXException if the workbook does not constains any worksheet
     * @throws XLSXException if the workbook has two sheets with the same name
     */
    public function write()
    {
        // check that there are worksheets
        if (! $this->worksheets->count()) {
            throw new XLSXException('Workbook does not contains any worksheet');
        }
        // check that every sheet has a different name
        $repeatedNames = $this->worksheets->retrieveRepeatedNames();
        if (count($repeatedNames)) {
            throw new XLSXException('Workbook has worksheets with the same name: ' . implode(',', $repeatedNames));
        }
        // validations end, create the file
        $filename = tempnam(sys_get_temp_dir(), 'xlsx-');
        $removefiles = [];
        try {
            $globalProgress = $this->getGlobalProgress();
            $detailedProgress = $this->getDetailedProgress();
            $globalProgress->update('Building structures...', 0, 2 + $this->worksheets->count());
            $zip = new ZipArchive();
            $zip->open($filename, ZipArchive::CREATE);
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
            $sharedstrings = new SharedStrings();
            $wsIndex = 1;
            foreach ($this->worksheets as $worksheet) {
                $globalProgress->update('Add worksheet ' . $worksheet->getName() . '...', $wsIndex);
                // write and include the sheet
                $wsfile = $worksheet->write($sharedstrings, $detailedProgress);
                $removefiles[] = $wsfile;
                $zip->addFile($wsfile, $this->workSheetFilePath($wsIndex));
                $wsIndex = $wsIndex + 1;
            }
            // include the shared strings file
            $globalProgress->update('Add shared strings...', $wsIndex);
            $shstrsfile = $sharedstrings->write($detailedProgress);
            $removefiles[] = $shstrsfile;
            $zip->addFile($shstrsfile, 'xl/sharedStrings.xml');
            // end with zip
            $zip->close();
            $globalProgress->update('Done', $wsIndex + 1);
        } finally {
            // remove temporary files
            foreach ($removefiles as $file) {
                unlink($file);
            }
        }
        return $filename;
    }

    protected function workSheetFilePath($index, $prefix = 'xl/')
    {
        return $prefix . 'worksheets/sheet' . $index . '.xml';
    }

    protected function xmlRels()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="wb1"'
            . ' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument"'
            . ' Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    protected function xmlContentTypes()
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
            . array_reduce(range(1, $this->worksheets->count()), function ($return, $index) {
                return $return . '<Override PartName="/' . $this->workSheetFilePath($index)
                    . '" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                ;
            })
            . '</Types>';
    }

    protected function xmlStyles()
    {
        $styles = [];
        $styles[] = $this->style;
        foreach ($this->worksheets as $worksheet) {
            // add worksheet header style
            $styles[] = $worksheet->getHeaderStyle();
            foreach ($worksheet->getColumns() as $column) {
                // add worksheet column style
                $styles[] = $column->getStyle();
            }
        }
        $stylesheet = new StyleSheet($styles);
        return $stylesheet->asXML();
    }

    protected function xmlWorkbook()
    {
        $index = 0;
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<bookViews><workbookView/></bookViews>' // without the bookviews the user in MS Excel cannot copy paste
            . '<sheets>'
            . array_reduce($this->worksheets->all(), function ($return, WorkSheet $worksheet) use (&$index) {
                $index = $index + 1;
                return $return . '<sheet name="' . $worksheet->getName() . '"'
                    . ' sheetId="' . $index . '" r:id="rId' . $index . '"/>';
            })
            . '</sheets>'
            . '</workbook>'
            ;
    }

    protected function xmlWorkbookRels()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . array_reduce(range(1, $this->worksheets->count()), function ($return, $index) {
                return $return . '<Relationship Id="rId' . $index . '"'
                    . ' Target="' . $this->workSheetFilePath($index, '') . '"'
                    . ' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"/>'
                ;
            })
            . '<Relationship Id="stl1" Target="styles.xml"'
            . ' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"/>'
            . '<Relationship Id="shs1" Target="sharedStrings.xml"'
            . ' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings"/>'
            . '</Relationships>'
            ;
    }
}
