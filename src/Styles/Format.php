<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Styles;

use Eclipxe\XLSXExporter\Utils\XmlConverter;

/**
 * @property int $id numeric identification for the format
 * @property string $code format code
 *
 * References:
 * http://msdn.microsoft.com/en-us/library/documentformat.openxml.spreadsheet.numberingformat%28v=office.14%29.aspx
 * https://github.com/PHPOffice/PHPExcel/blob/develop/Classes/PHPExcel/Style/NumberFormat.php
 */
class Format extends AbstractStyle
{
    public const FORMAT_GENERAL = 'General';

    public const FORMAT_TEXT = '@';

    public const FORMAT_NUMBER = '0';

    public const FORMAT_NUMBER_00 = '0.00';

    public const FORMAT_NUMBER_COMMA_SEPARATED1 = '#,##0.00';

    public const FORMAT_NUMBER_COMMA_SEPARATED2 = '#,##0.00_-';

    public const FORMAT_PERCENTAGE = '0%';

    public const FORMAT_PERCENTAGE_00 = '0.00%';

    public const FORMAT_DATE_YYYYMMDD2 = 'yyyy-mm-dd';

    public const FORMAT_DATE_YYYYMMDD = 'yy-mm-dd';

    public const FORMAT_DATE_DDMMYYYY = 'dd/mm/yy';

    public const FORMAT_DATE_DMYSLASH = 'd/m/yy';

    public const FORMAT_DATE_DMYMINUS = 'd-m-yy';

    public const FORMAT_DATE_DMMINUS = 'd-m';

    public const FORMAT_DATE_MYMINUS = 'm-yy';

    public const FORMAT_DATE_XLSX14 = 'mm-dd-yy';

    public const FORMAT_DATE_XLSX15 = 'd-mmm-yy';

    public const FORMAT_DATE_XLSX16 = 'd-mmm';

    public const FORMAT_DATE_XLSX17 = 'mmm-yy';

    public const FORMAT_DATE_XLSX22 = 'm/d/yy h:mm';

    public const FORMAT_DATE_DATETIME = 'd/m/yy h:mm';

    public const FORMAT_DATE_TIME1 = 'h:mm AM/PM';

    public const FORMAT_DATE_TIME2 = 'h:mm:ss AM/PM';

    public const FORMAT_DATE_TIME3 = 'h:mm';

    public const FORMAT_DATE_TIME4 = 'h:mm:ss';

    public const FORMAT_DATE_TIME5 = 'mm:ss';

    public const FORMAT_DATE_TIME6 = 'h:mm:ss';

    public const FORMAT_DATE_TIME7 = 'm:s.000000';

    public const FORMAT_DATE_TIME8 = 'h:mm:ss;@';

    public const FORMAT_DATE_YYYYMMDDSLASH = 'yy/mm/dd;@';

    public const FORMAT_CURRENCY_USD_SIMPLE = '"$"#,##0.00_-';

    public const FORMAT_CURRENCY_USD = '$#,##0_-';

    public const FORMAT_CURRENCY_EUR_SIMPLE = '[$EUR ]#,##0.00_-';

    public const FORMAT_ACCOUNTING_00 = '_-"$"* #,##0.00_-;\-"$"* #,##0.00_-;_-"$"* "-"??_-;_-@_-';

    public const FORMAT_ACCOUNTING = '_-"$"* #,##0_-;\-"$"* #,##0_-;_-"$"* "-"??_-;_-@_-';

    // custom formats
    public const FORMAT_COMMA_0DECS = '#,##0';

    public const FORMAT_COMMA_2DECS = '#,##0.00';

    public const FORMAT_ZERO_0DECS = '0';

    public const FORMAT_ZERO_2DECS = '0.00';

    public const FORMAT_YESNO = '"YES";"YES";"NO"';

    public const FORMAT_DATE_YMDHM = 'yyyy\-mm\-dd\ hh:mm:ss';

    public const FORMAT_DATE_YMD = 'yyyy\-mm\-dd';

    public const FORMAT_DATE_HM = 'hh:mm';

    protected function properties(): array
    {
        return ['id', 'code'];
    }

    public function asXML(): string
    {
        if ('' === $this->code) {
            return '';
        }
        return sprintf('<numFmt numFmtId="%d" formatCode="%s"/>', $this->id, XmlConverter::parse($this->code));
    }

    /**
     * This method forces $format->int to be always a string
     */
    public function getId(): int
    {
        return intval($this->data['id'] ?? 0);
    }

    /**
     * This method forces $format->code to be always a string
     */
    public function getCode(): string
    {
        return strval($this->data['code'] ?? '');
    }

    /** @return array<int, string> */
    public static function getBuiltInCodes(): array
    {
        return [
            0 => self::FORMAT_GENERAL,
            1 => self::FORMAT_NUMBER,
            2 => self::FORMAT_NUMBER_00,
            3 => '#,##0',
            4 => '#,##0.00',
            9 => '0%',
            10 => '0.00%',
            11 => '0.00E+00',
            12 => '# ?/?',
            13 => '# ??/??',
            14 => 'mm-dd-yy',
            15 => 'd-mmm-yy',
            16 => 'd-mmm',
            17 => 'mmm-yy',
            18 => 'h:mm AM/PM',
            19 => 'h:mm:ss AM/PM',
            20 => 'h:mm',
            21 => 'h:mm:ss',
            22 => 'm/d/yy h:mm',
            37 => '#,##0 ;(#,##0)',
            38 => '#,##0 ;[Red](#,##0)',
            39 => '#,##0.00;(#,##0.00)',
            40 => '#,##0.00;[Red](#,##0.00)',
            44 => self::FORMAT_ACCOUNTING_00,
            45 => 'mm:ss',
            46 => '[h]:mm:ss',
            47 => 'mmss.0',
            48 => '##0.0E+0',
            49 => '@',
            27 => '[$-404]e/m/d',
            30 => 'm/d/yy',
            36 => '[$-404]e/m/d',
            50 => '[$-404]e/m/d',
            57 => '[$-404]e/m/d',
            59 => 't0',
            60 => 't0.00',
            61 => 't#,##0',
            62 => 't#,##0.00',
            67 => 't0%',
            68 => 't0.00%',
            69 => 't# ?/?',
            70 => 't# ??/??',
        ];
    }

    /**
     * Get the code for a built-in id, if not found return an empty string
     */
    public static function getBuiltInCodeById(int $id): string
    {
        return static::getBuiltInCodes()[$id] ?? '';
    }

    /**
     * Get the id for a built-in code, if not found return false
     *
     * @return int|false
     */
    public static function getBuiltInCodeIdByCode(string $code)
    {
        return array_search($code, static::getBuiltInCodes(), true);
    }
}
