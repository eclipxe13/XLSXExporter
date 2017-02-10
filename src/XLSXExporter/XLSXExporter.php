<?php
namespace XLSXExporter;

class XLSXExporter
{
    /**
     * Save the workbook as an specified file
     * This is a shortcut for write, copy and unlink
     *
     * @param WorkBook $workbook
     * @param string $filename
     * @throws XLSXException if the filename exists and is a directory
     */
    public static function save(WorkBook $workbook, $filename)
    {
        if (file_exists($filename) && is_dir($filename)) {
            throw new XLSXException('The filename exists and is a directory');
        }
        try {
            $tempfile = $workbook->write();
            if (! copy($tempfile, $filename)) {
                throw new XLSXException("Cannot copy $tempfile to $filename");
            }
        } finally {
            unlink($tempfile);
        }
    }

    /**
     * Passtru the workbook and write the Content-type header
     * This is a shortcut for write, header, passtru and unlink
     *
     * @param WorkBook $workbook
     * @throws XLSXException
     */
    public static function passtru(WorkBook $workbook)
    {
        $tempfile = $workbook->write();
        try {
            if (false === $file = fopen($tempfile, 'r')) {
                throw new XLSXException("Can not open file $tempfile");
            }
            header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $passthru = fpassthru($file);
            fclose($file);
            if (false === $passthru) {
                throw new XLSXException("Can not passthru $tempfile");
            }
        } finally {
            unlink($tempfile);
        }
    }
}
