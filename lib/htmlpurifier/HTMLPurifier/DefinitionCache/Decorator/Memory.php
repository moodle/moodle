<?php

require_once 'HTMLPurifier/DefinitionCache/Decorator.php';

/**
 * Definition cache decorator class that saves all cache retrievals
 * to PHP's memory; good for unit tests or circumstances where 
 * there are lots of configuration objects floating around.
 */
class HTMLPurifier_DefinitionCache_Decorator_Memory extends
      HTMLPurifier_DefinitionCache_Decorator
{
    
    var $definitions;
    var $name = 'Memory';
    
    function copy() {
        return new HTMLPurifier_DefinitionCache_Decorator_Memory();
    }
    
    function add($def, $config) {
        $status = parent::add($def, $config);
        if ($status) $this->definitions[$this->generateKey($config)] = $def;
        return $status;
    }
    
    function set($def, $config) {
        $status = parent::set($def, $config);
        if ($status) $this->definitions[$this->generateKey($config)] = $def;
        return $status;
    }
    
    function replace($def, $config) {
        $status = parent::replace($def, $config);
        if ($status) $this->definitions[$this->generateKey($config)] = $def;
        return $status;
    }
    
    function get($config) {
        $key = $this->generateKey($config);
        if (isset($this->definitions[$key])) return $this->definitions[$key];
        $this->definitions[$key] = parent::get($config);
        return $this->definitions[$key];
    }
    
}

