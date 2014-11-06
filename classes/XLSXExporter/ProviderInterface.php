<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace XLSXExporter;

/**
 * Description of DataProvider
 *
 * @author eclipxe
 */
interface ProviderInterface
{
    public function get($key);
    public function next();
    public function valid();
    public function count();
}
