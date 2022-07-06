<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests\Unit\Utils;

use Eclipxe\XLSXExporter\Tests\TestCase;
use Eclipxe\XLSXExporter\Utils\OpenXmlColor;
use stdClass;

final class OpenXmlColorCastTest extends TestCase
{
    /**
     * @return array<int, array{string|false, scalar|stdClass|null, string}>
     */
    public function providerCastMethod(): array
    {
        return [
            ['0011FF33', '0011FF33', '8 valid hex digits'],
            ['0011FF33', '#0011FF33', '8 valid hex digits with #'],
            ['FF11FF33', '11FF33', '6 valid hex digits'],
            ['FF11FF33', '#11FF33', '6 valid hex digits with #'],
            ['FFAABBCC', 'ABC', '3 valid hex digits'],
            ['FFAABBCC', '#ABC', '3 valid hex digits with #'],
            ['00AABBCC', '0ABC', '4 valid hex digits'],
            ['00AABBCC', '#0ABC', '4 valid hex digits with #'],
            // bad cases
            [false, null, 'error null'],
            [false, new stdClass(), 'error object'],
            [false, 'ZXCZXCMM', 'error all non hex'],
            [false, '0123456X', 'error one non hex'],
            [false, '12345', 'error 5 chars'],
            [false, '123456789', 'error 9 chars'],
            // integers
            ['FF000000', 0, 'integer 0'],
            ['FFFFFFFF', 16_777_215, 'integer 16777215'],
            ['01000000', 16_777_216, 'integer 16777215'],
            ['FFFFFFFF', 4_294_967_295, 'integer 4294967295'],
            [false, -1, 'error negative integer'],
            [false, 4_294_967_296, 'error overflow integer 4294967296'],
        ];
    }

    /**
     * @param string|false $expected
     * @param scalar|null $color
     * @param string $message
     * @dataProvider providerCastMethod
     */
    public function testCastMethod($expected, $color, string $message): void
    {
        $this->assertSame($expected, OpenXmlColor::cast($color), "OpenXmlColor::cast fail on '$message'");
        if (is_string($color)) {
            $lower = strtolower($color);
            $this->assertSame($expected, OpenXmlColor::cast($lower), "OpenXmlColor::cast fail on '$message lowercase'");
        }
    }
}
