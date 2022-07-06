<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests\Unit;

use Eclipxe\XLSXExporter\DateConverter;
use Eclipxe\XLSXExporter\Tests\TestCase;

final class DateConverterTest extends TestCase
{
    public function testTimeStampToTime(): void
    {
        $a = [
            ['s' => '00:00:00', 'e' => 0.0], // only times, 0 hours
            ['s' => '12:00:00', 'e' => 0.5], // only time, 12:00:00
            ['s' => '23:59:59', 'e' => 0.999989], // only time, 23:59:59
            ['s' => '2014-01-13 23:59:59', 'e' => 0.999989], // only time, 23:59:59
            ['s' => '19:49:23', 'e' => 0.825961], // known fail
            ['s' => '19:49:24', 'e' => 0.825973], // known fail
        ];
        foreach ($a as $test) {
            $ts = strtotime($test['s']);
            $this->assertEqualsWithDelta(
                round($test['e'], 9),
                round((float) DateConverter::tsToExcelTime($ts), 9),
                10 ** -7,
                'Checking ' . $test['s'] . ', TS: ' . $ts . ' - ' . date('H:i:s', $ts)
            );
        }
    }

    public function testTimeStampToDate(): void
    {
        $a = [
            ['s' => '2014-01-13', 'e' => 41652],
            ['s' => '2014-12-31', 'e' => 42004],
            ['s' => '2015-01-01', 'e' => 42005],
            ['s' => '2014-10-26', 'e' => 41938],
            ['s' => '2014-10-27', 'e' => 41939],
        ];
        foreach ($a as $test) {
            $ts = strtotime($test['s']);
            $this->assertEqualsWithDelta(
                round($test['e'], 9),
                round((float) DateConverter::tsToExcelDateTime($ts), 9),
                1 / 86000,
                sprintf('Checking %s, TS: %s', $test['s'], $ts)
            );
        }
    }

    public function testTimeStampToDate1900(): void
    {
        $a = [
            ['s' => '1900-01-01', 'e' => 1],
            ['s' => '1900-01-02', 'e' => 2],
            ['s' => '1900-01-31', 'e' => 31],
            ['s' => '1900-02-01', 'e' => 32],
            ['s' => '1900-02-02', 'e' => 33],
            ['s' => '1900-02-28', 'e' => 59],
            // ["s" => "1900-02-29", "e" => 60], BUG!
            ['s' => '1900-02-29', 'e' => 61], // as we are using strtotime, "1900-02-29" equals to "1900-03-01"
            ['s' => '1900-03-01', 'e' => 61],
            ['s' => '1900-03-31', 'e' => 91],
            ['s' => '1900-12-31', 'e' => 366],
        ];
        foreach ($a as $test) {
            $ts = strtotime($test['s']);
            $this->assertEqualsWithDelta(
                round($test['e'], 9),
                round((float) DateConverter::tsToExcelDateTime($ts), 9),
                1 / 86000,
                sprintf('Checking %s, TS: %s', $test['s'], $ts)
            );
        }
    }

    public function testTimeStampToDateTime(): void
    {
        $a = [
            ['s' => '2014-01-13 14:15:16', 'e' => 41652.593935],
            ['s' => '2014-12-31 23:59:59', 'e' => 42004.999988],
            ['s' => '2015-01-01 00:00:00', 'e' => 42005],
            ['s' => '2014-10-26 00:15:00', 'e' => 41938.010417],
            ['s' => '2014-10-26 01:15:00', 'e' => 41938.052083],
            ['s' => '2014-10-26 02:15:00', 'e' => 41938.09375],
            ['s' => '2014-10-26 03:15:00', 'e' => 41938.135417],
            ['s' => '2014-10-26 04:15:00', 'e' => 41938.177083],
            ['s' => '1900-01-01 00:00:00', 'e' => 1.0], // first date
            ['s' => '1900-01-01 01:00:00', 'e' => 1.041667],
        ];
        foreach ($a as $test) {
            $ts = strtotime($test['s']);
            $this->assertEqualsWithDelta(
                round($test['e'], 9),
                round((float) DateConverter::tsToExcelDateTime($ts), 9),
                round(1 / 86000, DateConverter::PRECISION_TIME),
                sprintf('Checking %s, TS: %s', $test['s'], $ts)
            );
        }
    }

    /**
     * @return array<int, array<string>>
     */
    public function providerTimeStampToExcel(): array
    {
        return [
            ['2014-12-31', DateConverter::DATE, '42004'],
            ['2014-10-26 01:15:00', DateConverter::DATETIME, '41938.052084'],
            ['01:15:00', DateConverter::TIME, '0.052084'],
        ];
    }

    /**
     * @dataProvider providerTimeStampToExcel
     */
    public function testTimeStampToExcel(string $ts, string $type, string $expected): void
    {
        $this->assertSame($expected, DateConverter::tsToExcel((int) strtotime($ts), $type));
    }
}
