<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Utils;

class XmlConverter
{
    public static function parse(string $text): string
    {
        // do not convert single quotes
        return htmlspecialchars($text, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}
