<?php

namespace XLSXExporter\Styles;

interface StyleInterface
{
    public function setIndex($index);
    public function getIndex();
    public function hasValues();
    public function setValues($array);
    public function getValues();
    public function getHash();
    public function asXML();
}