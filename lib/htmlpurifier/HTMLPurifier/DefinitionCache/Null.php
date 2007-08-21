<?php

require_once 'HTMLPurifier/DefinitionCache.php';

/**
 * Null cache object to use when no caching is on.
 */
class HTMLPurifier_DefinitionCache_Null extends HTMLPurifier_DefinitionCache
{
    
    function add($def, $config) {
        return false;
    }
    
    function set($def, $config) {
        return false;
    }
    
    function replace($def, $config) {
        return false;
    }
    
    function get($config) {
        return false;
    }
    
    function flush($config) {
        return false;
    }
    
    function cleanup($config) {
        return false;
    }
    
}

