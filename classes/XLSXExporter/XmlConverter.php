<?php


namespace XLSXExporter;

class XmlConverter
{
    public static function specialchars($text)
    {
        // do not convert single quotes
        return htmlspecialchars($text, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}
