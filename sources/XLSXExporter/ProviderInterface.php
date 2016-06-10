<?php

namespace XLSXExporter;

interface ProviderInterface
{
    public function get($key);
    public function next();
    public function valid();
    public function count();
}
