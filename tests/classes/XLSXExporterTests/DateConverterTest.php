<?php

namespace XLSXExporterTests;

use XLSXExporter\DateConverter;

class DateConverterTest extends \PHPUnit_Framework_TestCase
{

    public function testTimeStampToTime()
    {
        $a = [
            ["s" => "00:00:00", "e" => 0.0], // only times, 0 hours
            ["s" => "12:00:00", "e" => 0.5], // only time, 12:00:00
            ["s" => "23:59:59", "e" => 0.999988426], // only time, 23:59:59
            ["s" => "2014-01-13 23:59:59", "e" => 0.999988426], // only time, 23:59:59
        ];
        foreach ($a as $test) {
            $ts = strtotime($test["s"]);
            $this->assertEquals(round($test["e"], 9), round(DateConverter::tsToExcelTime($ts), 9), "Checking ".$test["s"].", TS: ".$ts." - ".date("H:i:s", $ts), 1 / 86000);
        }
    }
    public function testTimeStampToDate()
    {
        $a = [
            ["s" => "2014-01-13", "e" => 41652],
            ["s" => "2014-12-31", "e" => 42004],
            ["s" => "2015-01-01", "e" => 42005],
            ["s" => "2014-10-26", "e" => 41938],
            ["s" => "2014-10-27", "e" => 41939],
        ];
        foreach ($a as $test) {
            $ts = strtotime($test["s"]);
            $this->assertEquals(round($test["e"], 9), round(DateConverter::tsToExcelDateTime($ts), 9), "Checking ".$test["s"].", TS: ".$ts, 1 / 86000);
        }
    }
    public function testTimeStampToDate1900()
    {
        $a = [
            ["s" => "1900-01-01", "e" => 1],
            ["s" => "1900-01-02", "e" => 2],
            ["s" => "1900-01-31", "e" => 31],
            ["s" => "1900-02-01", "e" => 32],
            ["s" => "1900-02-02", "e" => 33],
            ["s" => "1900-02-28", "e" => 59],
            // ["s" => "1900-02-29", "e" => 60], BUG!
            ["s" => "1900-02-29", "e" => 61], // as we are using strtotime then is the same as "1900-02-29"
            ["s" => "1900-03-01", "e" => 61],
            ["s" => "1900-03-31", "e" => 91],
            ["s" => "1900-12-31", "e" => 366],
        ];
        foreach ($a as $test) {
            $ts = strtotime($test["s"]);
            $this->assertEquals(round($test["e"], 9), round(DateConverter::tsToExcelDateTime($ts), 9), "Checking ".$test["s"].", TS: ".$ts, 1 / 86000);
        }

    }

    public function testTimeStampToDateTime()
    {
        $a = [
            ["s" => "2014-01-13 14:15:16", "e" => 41652.5939351852],
            ["s" => "2014-12-31 23:59:59", "e" => 42004.9999884259],
            ["s" => "2015-01-01 00:00:00", "e" => 42005],
            ["s" => "2014-10-26 00:15:00", "e" => 41938.0104166667],
            ["s" => "2014-10-26 01:15:00", "e" => 41938.0520833333],
            ["s" => "2014-10-26 02:15:00", "e" => 41938.09375],
            ["s" => "2014-10-26 03:15:00", "e" => 41938.1354166667],
            ["s" => "2014-10-26 04:15:00", "e" => 41938.1770833333],
            ["s" => "1900-01-01 00:00:00", "e" => 1.0], // first date
            ["s" => "1900-01-01 01:00:00", "e" => 1.04166666666667],
        ];
        foreach ($a as $test) {
            $ts = strtotime($test["s"]);
            $this->assertEquals(round($test["e"], 9), round(DateConverter::tsToExcelDateTime($ts), 9), "Checking ".$test["s"].", TS: ".$ts, 1 / 86000);
        }
    }

}