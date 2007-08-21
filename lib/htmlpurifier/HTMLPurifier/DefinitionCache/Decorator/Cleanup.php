<?php

require_once 'HTMLPurifier/DefinitionCache/Decorator.php';

/**
 * Definition cache decorator class that cleans up the cache
 * whenever there is a cache miss.
 */
class HTMLPurifier_DefinitionCache_Decorator_Cleanup extends
      HTMLPurifier_DefinitionCache_Decorator
{
    
    var $name = 'Cleanup';
    
    function copy() {
        return new HTMLPurifier_DefinitionCache_Decorator_Cleanup();
    }
    
    function add($def, $config) {
        $status = parent::add($def, $config);
        if (!$status) parent::cleanup($config);
        return $status;
    }
    
    function set($def, $config) {
        $status = parent::set($def, $config);
        if (!$status) parent::cleanup($config);
        return $status;
    }
    
    function replace($def, $config) {
        $status = parent::replace($def, $config);
        if (!$status) parent::cleanup($config);
        return $status;
    }
    
    function get($config) {
        $ret = parent::get($config);
        if (!$ret) parent::cleanup($config);
        return $ret;
    }
    
}

