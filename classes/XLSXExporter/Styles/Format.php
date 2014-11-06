<?php

namespace XLSXExporter\Styles;

use XLSXExporter\WorkSheetWriter;
use XLSXExporter\XLSXException;

/**
 * @property integer $id id code
 * @property string $code format code
 */
class Format extends AbstractStyle
{

    const FORMAT_GENERAL = 0;
    const FORMAT_ZERO_0DECS = 1;
    const FORMAT_ZERO_2DECS = 2;
    const FORMAT_COMMA_0DECS = 3;
    const FORMAT_COMMA_2DECS = 4;
    const FORMAT_PERCENT_0DECS = 9;
    const FORMAT_PERCENT_2DECS = 10;
    const FORMAT_DATE_YMDHM = 160;
    const FORMAT_DATE_YMD = 161;
    const FORMAT_DATE_HM = 162;

    protected function properties()
    {
        return ["id", "code"];
    }

    public function asXML()
    {
        if (null === $this->id and null === $this->code) return "";
        return '<numFmt'
            .((null !== $this->id) ? ' numFmtId="'.WorkSheetWriter::xml($this->id).'"' : '')
            .((null !== $this->code) ? ' formatCode="'.WorkSheetWriter::xml($this->code).'"' : '')
            .'/>'
        ;
    }

    public static function standarFormat($id)
    {
        $formats = [
            0 => "General",
            1 => "0",
            2 => "0.00",
            3 => "#,##0",
            4 => "#,##0.00",
            9 => "0%",
            10 => "0.00%",
            11 => "0.00E+00",
            12 => "# ?/?",
            13 => "# ??/??",
            14 => "mm-dd-yy",
            15 => "d-mmm-yy",
            16 => "d-mmm",
            17 => "mmm-yy",
            18 => "h:mm AM/PM",
            19 => "h:mm:ss AM/PM",
            20 => "h:mm",
            21 => "h:mm:ss",
            22 => "m/d/yy h:mm",
            37 => "#,##0 ;(#,##0)",
            38 => "#,##0 ;[Red](#,##0)",
            39 => "#,##0.00;(#,##0.00)",
            40 => "#,##0.00;[Red](#,##0.00)",
            45 => "mm:ss",
            46 => "[h]:mm:ss",
            47 => "mmss.0",
            48 => "##0.0E+0",
            49 => "@",
            160 => "yyyy-mm-dd hh:mm",
            161 => "yyyy-mm-dd",
            162 => "hh:mm",
        ];
        if (!array_key_exists($id, $formats)) {
            throw new XLSXException("A valid number format was not supplied");
        }
        $format = new Format();
        $format->setValues([
            "id" => $id,
            "code" => $formats[$id]
        ]);
        return $format;
    }

}