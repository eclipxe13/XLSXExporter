<?php

namespace XLSXExporter\Utils;

class XmlConverter
{
    /**
     * @param string $text
     * @return string
     */
    public static function parse($text)
    {
        // do not convert single quotes
        return htmlspecialchars($text, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}
